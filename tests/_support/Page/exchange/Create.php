<?php

namespace hipanel\modules\finance\tests\_support\Page\exchange;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Helper\PressButtonHelper;
use hipanel\tests\_support\Page\Widget\Input\Select2;

class Create extends Authenticated
{
    public function fillMainExchangeFields(array $exchangeData): void
    {
        $I = $this->tester;

       (new Select2($I, "#currencyexchangeform-client_id"))->setValue($exchangeData['client']);

       if (isset($exchangeData['currencyFrom']) && isset($exchangeData['currencyTo'])) {
            (new Select2($I, "#currencyexchangeform-from"))->setValue($exchangeData['currencyFrom']);
            (new Select2($I, "#currencyexchangeform-to"))->setValue($exchangeData['currencyTo']);
        }

       if (isset($exchangeData['sum'])) {
            (new Input($I, "//input[@id='currencyexchangeform-sum']"))->setValue($exchangeData['sum']);
        }
    }

    public function clickCreateButton(): void
    {
        $this->tester->pressButton('Create');
    }

    public function containsBlankFieldsError(array $fieldsList): void
    {
        foreach ($fieldsList as $field) {
            $this->tester->waitForText("$field cannot be blank.");
        }
    }

    public function seeActionSuccess(): void
    {
        $I = $this->tester;

        $I->closeNotification('Currency was exchanged successfully');
    }
}
