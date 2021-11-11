<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Seller;

class TariffProfilesCest
{
    private string $testProfileName = 'Test tariff profile 1';
    private string $testPlanName = 'Test Suit AnycastCDN tariff 1';

    public function ensureIndexPageWorks(Seller $I): void
    {
        $I->login();
        $I->needPage(Url::to('@tariffprofile/index'));
        $I->see('Tariff profiles', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkTariffProfilesSearchBox($I);
    }

    public function ensureISeeDefaultProfile(Seller $I): void
    {
        $I->login();
        $I->needPage(Url::to('@tariffprofile'));
        $I->see('Default', 'a');
    }

    public function ensureIChangeDefaultProfile(Seller $I): void
    {
        $I->login();
        $I->needPage(Url::to('@tariffprofile'));
        $I->click('Default');
        $I->seeInCurrentUrl('/finance/tariff-profile/view?id');
        $I->click('Update');
        $I->seeInCurrentUrl('/finance/tariff-profile/update?id');
        (new Select2($I, '#tariffprofile-anycastcdn'))->setValue($this->testPlanName);
        $I->pressButton('Save');
        $I->closeNotification('success');
        $I->seeInCurrentUrl('/finance/tariff-profile/view?id');
        $I->see($this->testPlanName);
    }


    public function ensureICreateNewProfile(Seller $I): void
    {
        $testPlanName = 'Test AnycastCDN tariff 1';
        $I->login();
        $I->needPage(Url::to('@tariffprofile/create'));
        $I->seeInCurrentUrl('/finance/tariff-profile/create');
        (new Input($I, '#tariffprofile-name'))->setValue($this->testProfileName);
        (new Select2($I, '#tariffprofile-anycastcdn'))->setValue($testPlanName);
        $I->pressButton('Save');
        $I->waitForPageUpdate();
        $I->closeNotification('success');
        $I->seeInCurrentUrl('/finance/tariff-profile/view?id');
        $I->see($this->testProfileName, 'h1');
        $I->see($testPlanName);
    }

    public function ensureICanDeleteProfile(Seller $I)
    {
        $I->login();
        $I->needPage(Url::to('@tariffprofile'));
        $I->see($this->testProfileName);
        $I->click($this->testProfileName);
        $I->click('Delete');
        $I->waitForText('Confirm tariff profile deleting');
        $I->click('Delete tariff profile');
        $I->waitForPageUpdate();
        $I->closeNotification('Profile deleted');
        $I->seeInCurrentUrl(Url::to('@tariffprofile'));
        $I->dontSee($this->testProfileName);
    }

    private function ensureICanSeeAdvancedSearchBox(Seller $I): void
    {
        $index = new IndexPage($I);

        $index->containsFilters([
            Input::asAdvancedSearch($I, 'Name'),
        ]);
    }

    private function ensureICanSeeBulkTariffProfilesSearchBox(Seller $I): void
    {
        $index = new IndexPage($I);

        $index->containsBulkButtons([
            'Delete',
        ]);
        $index->containsColumns([
            'Name',
            'Client',
            'Tariff',
        ]);
    }
}
