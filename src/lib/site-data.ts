export const SITE = {
  name: "isunky",
  title: "isunky - 记录思考，记录产品",
  description: "一个长期写作的个人站点，用来沉淀个人日志、产品日志和阶段性复盘。",
  url: "https://isunky.github.io",
  github: "https://github.com/isunky",
};

export type EntryLike<TData extends Record<string, unknown> = Record<string, unknown>> = {
  slug: string;
  collection?: string;
  body?: string;
  data: TData;
};

type ProductData = {
  name: string;
  description: string;
  status: string;
  repo: string;
  site?: string;
  stack?: string[];
  displayOrder?: number;
  draft?: boolean;
};

type LogData = {
  title: string;
  description: string;
  date: Date | string;
  tags?: string[];
  draft?: boolean;
};

type ProductLogData = LogData & {
  product: string;
  stage: string;
  version?: string;
};

export type ProductEntry = EntryLike<ProductData>;
export type LogEntry = EntryLike<LogData>;
export type ProductLogEntry = EntryLike<ProductLogData>;

export type FeaturedProduct = ProductEntry & {
  latestUpdate:
    | (ProductLogEntry & {
        title: string;
        description: string;
        date: Date | string;
        stage: string;
        href: string;
      })
    | null;
};

export type ProductActivity = ProductLogEntry & {
  href: string;
  productName: string;
  productStatus: string;
  stage: string;
};

export type SearchEntry = {
  type: "产品" | "个人日志" | "产品日志";
  title: string;
  description: string;
  href: string;
  date?: string;
  tags: string[];
  body: string;
};

export type ArchiveMonth = {
  key: string;
  label: string;
  count: number;
};

export function isPublished<TData extends { draft?: boolean }>(entry: EntryLike<TData>) {
  return entry.data.draft !== true;
}

export function toDate(value: Date | string) {
  return value instanceof Date ? value : new Date(value);
}

export function formatDate(value: Date | string) {
  return new Intl.DateTimeFormat("zh-CN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  })
    .format(toDate(value))
    .replaceAll("/", "-");
}

export function sortByDateDesc<TEntry extends EntryLike<{ date: Date | string }>>(entries: TEntry[]) {
  return [...entries].sort((a, b) => toDate(b.data.date).getTime() - toDate(a.data.date).getTime());
}

export function getPublishedEntries<TEntry extends EntryLike<{ draft?: boolean }>>(entries: TEntry[]) {
  return entries.filter(isPublished);
}

export function getProductHref(product: ProductEntry) {
  return `/products/${product.slug}/`;
}

export function getLogHref(log: LogEntry) {
  return `/logs/${log.slug}/`;
}

export function getProductLogHref(log: ProductLogEntry) {
  return `/product-logs/${log.slug}/`;
}

export function getFeaturedProducts(
  products: ProductEntry[],
  productLogs: ProductLogEntry[],
  limit = 3,
): FeaturedProduct[] {
  const publishedProducts = getPublishedEntries(products);
  const publishedLogs = sortByDateDesc(getPublishedEntries(productLogs));

  return [...publishedProducts]
    .sort((a, b) => {
      const order = (a.data.displayOrder ?? 999) - (b.data.displayOrder ?? 999);
      return order === 0 ? a.data.name.localeCompare(b.data.name, "zh-CN") : order;
    })
    .slice(0, limit)
    .map((product) => ({
      ...product,
      latestUpdate: normalizeProductUpdate(publishedLogs.find((log) => log.data.product === product.slug)),
    }));
}

function normalizeProductUpdate(entry: ProductLogEntry | undefined) {
  if (!entry) {
    return null;
  }

  return {
    ...entry,
    title: entry.data.title,
    description: entry.data.description,
    date: entry.data.date,
    stage: entry.data.stage,
    href: getProductLogHref(entry),
  };
}

export function getLatestProductActivity(
  productLogs: ProductLogEntry[],
  products: ProductEntry[],
  limit = 6,
): ProductActivity[] {
  const productMap = new Map(products.map((product) => [product.slug, product]));

  return sortByDateDesc(getPublishedEntries(productLogs))
    .slice(0, limit)
    .map((entry) => {
      const product = productMap.get(entry.data.product);
      return {
        ...entry,
        href: getProductLogHref(entry),
        productName: product?.data.name ?? entry.data.product,
        productStatus: product?.data.status ?? "",
        stage: entry.data.stage,
      };
    });
}

export function buildSearchIndex({
  products,
  logs,
  productLogs,
}: {
  products: ProductEntry[];
  logs: LogEntry[];
  productLogs: ProductLogEntry[];
}): SearchEntry[] {
  const productEntries: SearchEntry[] = getFeaturedProducts(products, productLogs, products.length).map((product) => ({
    type: "产品",
    title: product.data.name,
    description: product.data.description,
    href: getProductHref(product),
    tags: product.data.stack ?? [],
    body: [product.data.name, product.data.description, product.data.status, ...(product.data.stack ?? [])].join(" "),
  }));

  const logEntries: SearchEntry[] = sortByDateDesc(getPublishedEntries(logs)).map((log) => ({
    type: "个人日志",
    title: log.data.title,
    description: log.data.description,
    href: getLogHref(log),
    date: formatDate(log.data.date),
    tags: log.data.tags ?? [],
    body: [log.data.title, log.data.description, ...(log.data.tags ?? []), log.body ?? ""].join(" "),
  }));

  const productLogEntries: SearchEntry[] = sortByDateDesc(getPublishedEntries(productLogs)).map((log) => ({
    type: "产品日志",
    title: log.data.title,
    description: log.data.description,
    href: getProductLogHref(log),
    date: formatDate(log.data.date),
    tags: log.data.tags ?? [],
    body: [log.data.title, log.data.description, log.data.product, log.data.stage, ...(log.data.tags ?? []), log.body ?? ""].join(
      " ",
    ),
  }));

  return [...productEntries, ...logEntries, ...productLogEntries];
}

export function buildArchiveMonths(entries: Array<LogEntry | ProductLogEntry>): ArchiveMonth[] {
  const months = new Map<string, ArchiveMonth>();

  for (const entry of sortByDateDesc(getPublishedEntries(entries))) {
    const date = toDate(entry.data.date);
    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`;
    const existing = months.get(key);

    if (existing) {
      existing.count += 1;
    } else {
      months.set(key, {
        key,
        label: `${date.getFullYear()} 年 ${date.getMonth() + 1} 月`,
        count: 1,
      });
    }
  }

  return [...months.values()];
}

export function buildTagIndex(entries: Array<LogEntry | ProductLogEntry>) {
  const tags = new Map<string, number>();

  for (const entry of getPublishedEntries(entries)) {
    for (const tag of entry.data.tags ?? []) {
      tags.set(tag, (tags.get(tag) ?? 0) + 1);
    }
  }

  return [...tags.entries()]
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count || a.name.localeCompare(b.name, "zh-CN"));
}

export function entriesForTag<TEntry extends LogEntry | ProductLogEntry>(entries: TEntry[], tag: string) {
  return sortByDateDesc(getPublishedEntries(entries)).filter((entry) => entry.data.tags?.includes(tag));
}
