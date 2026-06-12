import { getCollection } from "astro:content";
import { buildSearchIndex } from "../lib/site-data";

export async function GET() {
  const products = await getCollection("products");
  const logs = await getCollection("logs");
  const productLogs = await getCollection("product-logs");

  return new Response(JSON.stringify(buildSearchIndex({ products, logs, productLogs }), null, 2), {
    headers: {
      "Content-Type": "application/json; charset=utf-8",
    },
  });
}
