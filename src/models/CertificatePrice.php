<?php

namespace hipanel\modules\finance\models;

use Yii;
use hipanel\base\ModelTrait;
use yii\validators\NumberValidator;

/**
 * Class CertificatePrice
 * @package hipanel\modules\finance\models
 * @property array $sums
 */
class CertificatePrice extends Price
{
    use ModelTrait;

    public static function tableName()
    {
        return 'price';
    }

    const TYPE_CERT_PURCHASE = 'certificate_purchase';
    const TYPE_CERT_RENEWAL = 'certificate_renewal';

    public function rules()
    {
        $rules = parent::rules();
        $rules['create-required'] = [
            ['object_id'],
            'required',
            'on' => ['create', 'update'],
            'when' => function ($model) {
                /** @var self $model */
                return $model->isTypeCorrect();
            },
        ];
        $rules[] = [['certificateType'], 'safe'];
        $rules[] = [['sums'], 'validatePrices', 'on' => ['create', 'update']];

        return $rules;
    }

    public function isTypeCorrect()
    {
        return isset($this->getTypes()[$this->type]);
    }

    /**
     * @return array
     */
    public static function getPeriods()
    {
        return [
            1 => Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => 1]),
            2 => Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => 2]),
        ];
    }

    /**
     * @return array
     */
    public function getAvailablePeriods()
    {
        $periods = [];
        foreach ([1,2] as $period) {
            if ($this->hasPriceForPeriod($period)) {
                $periods[$period] = Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => $period]);
            }
        }

        return $periods;
    }

    public function getPriceForPeriod($period)
    {
        if (!$this->hasPriceForPeriod($period)) {
            return null;
        }

        return ((float)$this->sums[$period]) / 100;
    }

    public function hasPriceForPeriod($period)
    {
        return !empty($this->sums[$period]);
    }

    public function validatePrices()
    {
        $periods = $this->getPeriods();
        $validator = new NumberValidator();

        foreach (array_keys($periods) as $period) {
            $validation = $validator->validate($this->sums[$period]);
            if ($validation === false) {
                unset($this->sums[$period]);
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return [
            static::TYPE_CERT_PURCHASE => Yii::t('hipanel:finance:tariff', 'Purchase'),
            static::TYPE_CERT_RENEWAL => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }
}
