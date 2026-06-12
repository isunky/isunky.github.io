import { describe, expect, it } from "vitest";
import {
  buildArchiveMonths,
  buildSearchIndex,
  getFeaturedProducts,
  getLatestProductActivity,
} from "../src/lib/site-data";

const products = [
  {
    slug: "isunky-github-io",
    data: {
      name: "isunky.github.io",
      description: "个人站点重建，用于沉淀日志与产品进展。",
      status: "构建中",
      displayOrder: 3,
      repo: "https://github.com/isunky/isunky.github.io",
      stack: ["Astro", "Markdown"],
    },
  },
  {
    slug: "mdview",
    data: {
      name: "MDView",
      description: "快速查看 Markdown 文件的桌面工具。",
      status: "规划中",
      displayOrder: 1,
      repo: "https://github.com/isunky/MDView",
      stack: ["TypeScript"],
    },
  },
  {
    slug: "agent-md-wizard",
    data: {
      name: "agent-md-wizard",
      description: "通过引导流程生成或更新 AGENT.MD。",
      status: "迭代中",
      displayOrder: 2,
      repo: "https://github.com/isunky/agent-md-wizard",
      stack: ["Codex Skill", "Markdown"],
    },
  },
];

const productLogs = [
  {
    slug: "mdview-log",
    collection: "productLogs",
    data: {
      title: "规划产品日志结构",
      description: "将产品动态从普通日志中拆出，便于追踪工具进展。",
      date: new Date("2026-06-12T00:00:00.000Z"),
      product: "mdview",
      stage: "v0.1 规划",
      tags: ["产品", "Markdown"],
    },
  },
  {
    slug: "site-rebuild-log",
    collection: "productLogs",
    data: {
      title: "重建个人站点",
      description: "把旧 Hexo 静态页替换为 Astro 内容站。",
      date: new Date("2026-06-11T00:00:00.000Z"),
      product: "isunky-github-io",
      stage: "首版搭建",
      tags: ["网站", "Astro"],
    },
  },
  {
    slug: "draft-log",
    collection: "productLogs",
    data: {
      title: "未发布动态",
      description: "草稿不应出现在首页或搜索里。",
      date: new Date("2026-06-13T00:00:00.000Z"),
      product: "agent-md-wizard",
      stage: "草稿",
      tags: ["草稿"],
      draft: true,
    },
  },
];

const logs = [
  {
    slug: "first-note",
    collection: "logs",
    data: {
      title: "关于长期记录的第一篇笔记",
      description: "说明为什么重建个人站点。",
      date: new Date("2026-06-10T00:00:00.000Z"),
      tags: ["思考", "写作"],
    },
  },
  {
    slug: "draft-note",
    collection: "logs",
    data: {
      title: "个人草稿",
      description: "草稿不应公开。",
      date: new Date("2026-06-14T00:00:00.000Z"),
      tags: ["草稿"],
      draft: true,
    },
  },
];

describe("site data helpers", () => {
  it("orders featured products and attaches the newest published product log", () => {
    const featured = getFeaturedProducts(products, productLogs, 3);

    expect(featured.map((product) => product.slug)).toEqual([
      "mdview",
      "agent-md-wizard",
      "isunky-github-io",
    ]);
    expect(featured[0].latestUpdate?.title).toBe("规划产品日志结构");
    expect(featured[1].latestUpdate).toBeNull();
  });

  it("returns latest product activity enriched with product metadata", () => {
    const activity = getLatestProductActivity(productLogs, products, 5);

    expect(activity.map((entry) => entry.slug)).toEqual([
      "mdview-log",
      "site-rebuild-log",
    ]);
    expect(activity[0]).toMatchObject({
      productName: "MDView",
      productStatus: "规划中",
      stage: "v0.1 规划",
    });
  });

  it("builds a public search index for products, personal logs, and product logs", () => {
    const index = buildSearchIndex({ products, logs, productLogs });

    expect(index.map((entry) => entry.href)).toEqual([
      "/products/mdview/",
      "/products/agent-md-wizard/",
      "/products/isunky-github-io/",
      "/logs/first-note/",
      "/product-logs/mdview-log/",
      "/product-logs/site-rebuild-log/",
    ]);
    expect(index.find((entry) => entry.title === "个人草稿")).toBeUndefined();
    expect(index.find((entry) => entry.title === "未发布动态")).toBeUndefined();
    expect(index[0]).toMatchObject({
      type: "产品",
      title: "MDView",
    });
  });

  it("groups public logs into archive months newest first", () => {
    const months = buildArchiveMonths([...logs, ...productLogs]);

    expect(months).toEqual([
      {
        key: "2026-06",
        label: "2026 年 6 月",
        count: 3,
      },
    ]);
  });
});
