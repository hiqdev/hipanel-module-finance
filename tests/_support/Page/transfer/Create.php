<?php

namespace hipanel\modules\finance\tests\_support\Page\transfer;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;

class Create extends Authenticated
{
    public function fillMainInternalTransferFields(array $transferData): void
    {
        $I = $this->tester;

        (new Input($I, '#bill-0-sum'))->setValue($transferData['sum']);
        $I->click('//div[@class=\'input-group-btn\']/button[2]');
        $I->click('//li/a[contains(text(),\'$\')]');

        (new Select2($I, '#bill-0-client_id'))->setValue($transferData['client']);
        (new Select2($I, '#bill-0-receiver_id'))->setValue($transferData['receiverId']);
    }
    
    public function containsBlankFieldsError(): void
    {
        foreach ($this->getTransferFormFields() as $field) {
            $this->tester->waitForText("$field cannot be blank.");
        }
    }

    protected function getTransferFormFields(): array
    {
        return [
            'Sum',
            'Client',
            'Receiver ID',
            'Currency',
        ];
    }
}
