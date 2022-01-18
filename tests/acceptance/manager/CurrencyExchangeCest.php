<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Step\Acceptance\Manager;
use hipanel\modules\finance\tests\_support\Page\exchange\Create;

class CurrencyExchangeCest
{
    private Create $create;

    public function _before(Manager $I): void
    {
        $I->login();
        $I->needPage(Url::to('@bill/create-exchange'));
        $this->create = new Create($I);
    }

    public function ensureCurrencyExchangePageWorks(Manager $I): void
    {
        $I->see('Create currency exchange', 'h1');
        $I->see('Client');
    }

    public function ensureICantCreateCurrencyExchangeWithoutData(): void
    {
        $this->create->clickCreateButton();
        $this->create->containsBlankFieldsError(['Client']);
    }

    /**
     * @dataProvider provideExchangeData
     */
    public function ensureICanCreateCurrencyExchange(Manager $I, Example $example): void
    {
        $exchangeData = iterator_to_array($example->getIterator());
        $this->create->fillMainExchangeFields($exchangeData);
        $I->click('Create');
        $this->create->clickCreateButton();
        $this->create->seeActionSuccess();
    }

    protected function provideExchangeData(): array
    {
        return [
            'exchange' => [
                'client' => 'hipanel_test_user1',
                'currencyFrom' => 'USD',
                'currencyTo' => 'UAH',
                'sum' => 200,
            ],
        ];
    }
}
