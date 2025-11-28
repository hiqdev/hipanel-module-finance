import { test } from "@hipanel-core/fixtures";
import Index from "@hipanel-core/page/Index";

test("charge export works correctly @hipanel-module-finance @seller", async ({ page }) => {
  await page.goto("/finance/charge/index");
  const indexPage = new Index(page);

  await indexPage.columnFilters.applyFilter("client_id", "hipanel_test_user");

  await indexPage.testExport();
});
