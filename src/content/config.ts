import { defineCollection, z } from "astro:content";

const datedEntry = z.object({
  title: z.string(),
  description: z.string(),
  date: z.date(),
  tags: z.array(z.string()).default([]),
  draft: z.boolean().default(false),
});

const logs = defineCollection({
  type: "content",
  schema: datedEntry,
});

const productLogs = defineCollection({
  type: "content",
  schema: datedEntry.extend({
    product: z.string(),
    stage: z.string(),
    version: z.string().optional(),
  }),
});

const products = defineCollection({
  type: "content",
  schema: z.object({
    name: z.string(),
    description: z.string(),
    status: z.string(),
    repo: z.string().url(),
    site: z.string().url().optional(),
    stack: z.array(z.string()).default([]),
    displayOrder: z.number().int().default(999),
    draft: z.boolean().default(false),
  }),
});

export const collections = {
  logs,
  products,
  "product-logs": productLogs,
};
