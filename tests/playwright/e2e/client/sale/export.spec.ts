import { test } from "@hipanel-core/fixtures";
import Index from "@hipanel-core/page/Index";

test("sale export works correctly @hipanel-module-finance @client", async ({ page }) => {
  await page.goto("/finance/sale/index");
  const indexPage = new Index(page);

  await indexPage.testExport();
});
