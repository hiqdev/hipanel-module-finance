<?php

namespace hipanel\modules\finance\tests\_support\Page\import;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\helpers\Url;

class Create extends Authenticated
{
    public function fillImportField(array $importData): void
    {
        $I = $this->tester;

        $importString = null;

        foreach ($importData as $element) {
                $importString = $importString . ';' . $element;
            }
        $importString = substr_replace($importString, '', 0, 1);

        (new Input($I, '#billimportform-data'))->setValue($importString);
    }

    public function containsBlankFieldsError(array $fieldsList): void
    {
        foreach ($fieldsList as $field) {
            $this->tester->waitForText("$field cannot be blank.");
        }
    }
}
