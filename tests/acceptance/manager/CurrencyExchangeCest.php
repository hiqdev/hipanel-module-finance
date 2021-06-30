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
        $this->create = new Create($I);
    }

    /**
     * @dataProvider provideExchangeData
     */
    public function ensureThatIndexPageWorks(Manager $I, Example $example): void
    {
        $I->login();
        $exchangeData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@bill/create-exchange'));
        $I->see('Create currency exchange', 'h1');
        $I->see('Client');

        $this->ensureICantCreateCurrencyExchangeWithoutData();
        $this->ensureICanCreateCurrencyExchange($exchangeData);
    }

    private function ensureICantCreateCurrencyExchangeWithoutData(): void
    {
        $this->create->clickCreateButton();
        $this->create->containsBlankFieldsError(['Client']);
    }

    private function ensureICanCreateCurrencyExchange(array $exchangeData): void
    {
        $this->create->fillMainExchangeFields($exchangeData);
        $this->create->clickCreateButton();
        $this->create->seeActionSuccess();
    }

    protected function provideExchangeData(): array
    {
        return [
            'exchange' => [
                'client'       => 'hipanel_test_user1',
                'currencyFrom' => 'USD',
                'currencyTo'   => 'UAH',
                'sum'          => 200,
            ],
        ];
    }
}
