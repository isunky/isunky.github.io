const GITHUB_AUTHORIZE_URL = "https://github.com/login/oauth/authorize";
const GITHUB_TOKEN_URL = "https://github.com/login/oauth/access_token";
const DEFAULT_ALLOWED_ORIGIN = "https://isunky.github.io";

export default {
  async fetch(request, env) {
    const allowedOrigin = env.ALLOWED_ORIGIN || DEFAULT_ALLOWED_ORIGIN;
    const url = new URL(request.url);

    if (request.method === "OPTIONS") {
      return withCors(new Response(null, { status: 204 }), allowedOrigin);
    }

    try {
      if (url.pathname === "/auth") {
        return handleAuth(request, env);
      }

      if (url.pathname === "/callback") {
        return handleCallback(request, env, allowedOrigin);
      }
    } catch (error) {
      return htmlResponse(renderOAuthMessage("error", { error: error.message }, allowedOrigin), allowedOrigin, 500);
    }

    return withCors(new Response("Not found", { status: 404 }), allowedOrigin);
  },
};

function handleAuth(request, env) {
  requireEnv(env);
  const url = new URL(request.url);
  const authorizeUrl = new URL(GITHUB_AUTHORIZE_URL);

  authorizeUrl.searchParams.set("client_id", env.GITHUB_CLIENT_ID);
  authorizeUrl.searchParams.set("redirect_uri", `${url.origin}/callback`);
  authorizeUrl.searchParams.set("scope", "repo user:email");
  authorizeUrl.searchParams.set("state", crypto.randomUUID());

  return Response.redirect(authorizeUrl.href, 302);
}

async function handleCallback(request, env, allowedOrigin) {
  requireEnv(env);
  const url = new URL(request.url);
  const code = url.searchParams.get("code");

  if (!code) {
    return htmlResponse(renderOAuthMessage("error", { error: "missing_code" }, allowedOrigin), allowedOrigin, 400);
  }

  const response = await fetch(GITHUB_TOKEN_URL, {
    method: "POST",
    headers: {
      accept: "application/json",
      "content-type": "application/json",
      "user-agent": "isunky-decap-oauth-worker",
    },
    body: JSON.stringify({
      client_id: env.GITHUB_CLIENT_ID,
      client_secret: env.GITHUB_CLIENT_SECRET,
      code,
    }),
  });

  const result = await response.json();

  if (!response.ok || result.error || !result.access_token) {
    return htmlResponse(renderOAuthMessage("error", result, allowedOrigin), allowedOrigin, 401);
  }

  return htmlResponse(
    renderOAuthMessage("success", {
      token: result.access_token,
      provider: "github",
    }, allowedOrigin),
    allowedOrigin,
  );
}

function renderOAuthMessage(status, content, allowedOrigin) {
  const payload = JSON.stringify(content).replaceAll("<", "\\u003c");

  return `<!doctype html>
<html lang="zh-CN">
  <head><meta charset="utf-8"><title>GitHub OAuth</title></head>
  <body>
    <script>
      const receiveMessage = (message) => {
        if (message.origin !== ${JSON.stringify(allowedOrigin)}) return;
        window.opener.postMessage(
          'authorization:github:${status}:${payload}',
          message.origin
        );
        window.removeEventListener('message', receiveMessage, false);
        window.close();
      };
      window.addEventListener('message', receiveMessage, false);
      window.opener.postMessage('authorizing:github', '*');
    </script>
  </body>
</html>`;
}

function htmlResponse(body, allowedOrigin, status = 200) {
  return withCors(
    new Response(body, {
      status,
      headers: {
        "content-type": "text/html; charset=utf-8",
        "cache-control": "no-store",
      },
    }),
    allowedOrigin,
  );
}

function withCors(response, allowedOrigin) {
  const headers = new Headers(response.headers);
  headers.set("access-control-allow-origin", allowedOrigin);
  headers.set("access-control-allow-methods", "GET, OPTIONS");
  headers.set("access-control-allow-headers", "content-type");
  headers.set("x-robots-tag", "noindex");
  return new Response(response.body, { status: response.status, statusText: response.statusText, headers });
}

function requireEnv(env) {
  if (!env.GITHUB_CLIENT_ID || !env.GITHUB_CLIENT_SECRET) {
    throw new Error("Missing GITHUB_CLIENT_ID or GITHUB_CLIENT_SECRET");
  }
}
