<?php
declare(strict_types=1);


namespace hipanel\modules\finance\tests\_support\Page\sale;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Helper\PressButtonHelper;

class Index extends IndexPage
{
    public function deleteSelectedSales(): void
    {
        $I = $this->tester;
        $I->pressButton('Delete');
        $I->acceptPopup();
        $this->seeDeleteActionSuccess();
    }

    private function seeDeleteActionSuccess(): void
    {
        $this->tester->closeNotification('Sale was successfully deleted.');
    }
}
