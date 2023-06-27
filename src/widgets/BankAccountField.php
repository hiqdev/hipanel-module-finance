<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\client\models\Contact;
use hipanel\modules\finance\models\Purse;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 *
 * @property-read string $attributeName
 */
class BankAccountField extends Widget
{
    public Contact $contact;
    public Purse $purse;
    public ActiveForm $form;

    public function run(): string
    {
        if (!$this->contact->hasBankDetails()) {
            return '';
        }
        if ($this->contact->hasMoreThenOneBankDetailsOptions()) {
            $options = $this->contact->getBankDetailsDropDownOptions();

            return (string)$this->form
                ->field($this->purse, $this->getAttributeName())
                ->dropDownList($options, ['id' => $this->getAttributeName() . "-" . $this->id]);
        }
        $html[] = Html::beginTag('dl');
        $html[] = Html::tag('dt', $this->purse->getAttributeLabel($this->getAttributeName()));
        $html[] = Html::tag('dd', $this->contact->getBankDetailsSummary());
        $html[] = Html::endTag('dl');

        return implode("\n", $html);
    }

    private function getAttributeName(): string
    {
        return $this->contact->isRequisite() ? 'seller_bank_account_no' : 'client_bank_account_no';
    }
}
