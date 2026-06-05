<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use hipanel\models\Ref;
use hipanel\widgets\Type;
use Yii;
use yii\helpers\Html;

class BillType extends Type
{
    public $defaultValues = [
        'success' => [
            'deposit,*',
            'monthly,*',
        ],
        'info' => [
            'overuse,*',
        ],
        'warning' => [
            'monthly,monthly',
            'exchange,*',
        ],
        'primary' => [
            'monthly,installment',
            'monthly,leasing',
        ],
        'default' => [
            'monthly,hardware',
            'correction,*',
        ],
    ];

    public $field = 'type';

    public $i18nDictionary = 'hipanel.finance.billTypes';

    public function init(): void
    {
        parent::init();
        $this->setColor('none');
        $this->getView()->registerCss(<<<CSS
            .flex-space-beetween { display: flex; flex-wrap: nowrap; gap: 1rem; justify-content: space-between; }
            .align-center { align-items: center; }
        CSS);
    }

    protected function getModelLabel(): string
    {
        if ($this->getFieldValue() === null) {
            return Yii::t('hipanel.finance.billTypes', 'Unknown');
        }

        $labelField = $this->getLabelField();
        if ($labelField && $this->model->hasAttribute($labelField) && $this->model->getAttribute($labelField) !== null) {
            return $this->model->getAttribute($labelField);
        }

        static $billTypes = null;
        if ($billTypes === null) {
            $billTypes = Ref::getListRecursively('type,bill', false);
        }

        return $billTypes[$this->getFieldValue()] ?? $this->getFieldValue();
    }

    public function renderLabel(): string
    {
        if ($this->getFieldValue() === null) {
            return Yii::t('hipanel.finance.billTypes', 'Unknown');
        }

        $label = parent::renderLabel();
        $color = $this->pickColor();
        $paymentType = Html::tag('span', Yii::t('hipanel.finance.billTypes', explode(',', $this->getFieldValue())[0]), ['class' => "label label-{$color}"]);

        return Html::tag('span', $label . $paymentType, ['class' => 'flex-space-beetween align-center']);
    }
}
