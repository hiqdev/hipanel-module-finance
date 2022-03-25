<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2021, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\AcceptanceTester;
use hipanel\tests\_support\Page\Widget\Grid;
use hipanel\helpers\Url;

class View extends Authenticated
{
    public Grid $gridView;
    protected string $gridSelector = "//form[contains(@id, 'bulk') and contains(@id, 'search')]";

    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I);

        $this->gridView = new Grid($I, $this->gridSelector);
    }

    public function viewBillById(string $billId): void
    {
        $this->tester->needPage(Url::to("@bill/view?id=$billId"));
    }

    public function containsBillDataInTable(array $billData): void
    {
        if (isset($billData['currency'])) {
            unset($billData['currency']);
            $billData['amount'] = '$' . $billData['amount'] . '.00';
        }

        foreach ($billData as $value) {
            $this->tester->see($value, "table[class *= 'detail-view']");
        }
    }

    public function ensureChargeViewContainsData(array $chargeData): void
    {
        foreach ($chargeData as $element) {
            $this->tester->see($element, '//div[@class="table-responsive"]//tr');
        }
    }
}
