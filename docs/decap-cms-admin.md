# Decap CMS Admin Setup

The writing backend is available at:

```text
https://isunky.github.io/admin/
```

It edits the Markdown files under:

- `src/content/logs`
- `src/content/product-logs`
- `src/content/products`

## OAuth Worker

GitHub Pages is static, so Decap CMS needs an external OAuth endpoint before the online editor can commit to GitHub. This repo includes a Cloudflare Worker scaffold in `tools/decap-oauth-worker`.

1. Create a GitHub OAuth App:
   - Homepage URL: `https://isunky.github.io`
   - Authorization callback URL: `https://isunky-decap-oauth.isunky.workers.dev/callback`
2. Deploy the Worker:

```powershell
cd tools/decap-oauth-worker
npx wrangler secret put GITHUB_CLIENT_ID
npx wrangler secret put GITHUB_CLIENT_SECRET
npx wrangler deploy
```

3. Confirm `public/admin/config.yml` has the same Worker URL:

```yaml
backend:
  base_url: https://isunky-decap-oauth.isunky.workers.dev
  auth_endpoint: /auth
```

After the Worker is live, open `/admin/`, log in with GitHub, edit content, and publish. Decap CMS commits directly to `master`, which triggers the existing GitHub Pages workflow.
