import { readFileSync } from "node:fs";
import { describe, expect, it } from "vitest";

describe("Decap CMS admin config", () => {
  it("defines the GitHub backend and editable content collections", () => {
    const config = readFileSync("public/admin/config.yml", "utf8");

    expect(config).toContain("name: github");
    expect(config).toContain("repo: isunky/isunky.github.io");
    expect(config).toContain("branch: master");
    expect(config).toContain("base_url: https://isunky-decap-oauth");
    expect(config).toContain('folder: "src/content/logs"');
    expect(config).toContain('folder: "src/content/product-logs"');
    expect(config).toContain('folder: "src/content/products"');
    expect(config).toContain('name: "body"');
  });

  it("ships the admin shell and OAuth worker scaffold", () => {
    const admin = readFileSync("public/admin/index.html", "utf8");
    const worker = readFileSync("tools/decap-oauth-worker/src/index.js", "utf8");

    expect(admin).toContain("decap-cms");
    expect(worker).toContain("GITHUB_CLIENT_ID");
    expect(worker).toContain("GITHUB_CLIENT_SECRET");
    expect(worker).toContain("/auth");
    expect(worker).toContain("/callback");
  });
});
