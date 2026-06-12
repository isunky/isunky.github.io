import rss from "@astrojs/rss";
import { getCollection } from "astro:content";
import type { APIContext } from "astro";
import {
  SITE,
  formatDate,
  getLogHref,
  getProductLogHref,
  getPublishedEntries,
  sortByDateDesc,
} from "../lib/site-data";

export async function GET(context: APIContext) {
  const logs = getPublishedEntries(await getCollection("logs"));
  const productLogs = getPublishedEntries(await getCollection("product-logs"));
  const entries = sortByDateDesc([...logs, ...productLogs]);

  return rss({
    title: SITE.title,
    description: SITE.description,
    site: context.site ?? SITE.url,
    items: entries.map((entry) => {
      const isProductLog = entry.collection === "product-logs";
      return {
        title: isProductLog ? `产品日志：${entry.data.title}` : entry.data.title,
        description: `${entry.data.description}（${formatDate(entry.data.date)}）`,
        pubDate: entry.data.date,
        link: isProductLog ? getProductLogHref(entry) : getLogHref(entry),
      };
    }),
  });
}
