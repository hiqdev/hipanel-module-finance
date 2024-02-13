<?php

declare(strict_types=1);

namespace hipanel\modules\finance\tests\_support\Page\import;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\Input;

class Create extends Authenticated
{
    public function fillImportField(array $importData): void
    {
        $I = $this->tester;

        $importString = implode(';', $importData);
        (new Input($I, '#billimportform-data'))->setValue($importString);
    }

    public function containsBlankFieldsError(array $fieldsList): void
    {
        foreach ($fieldsList as $field) {
            $this->tester->waitForText("$field cannot be blank.");
        }
    }

    public function closeImportedBillPopup(): void
    {
        $this->tester->click("//div[@id = 'w1'] //button[@class = 'close']");
    }

    public function ensureImportTipIsCorrectlyDisplayed(): void
    {
        $I = $this->tester;

        $I->see('Use the following format:');
        $I->see('Client;Time;Amount;Currency;Type;Description;Requisite', 'pre');
    }
}
