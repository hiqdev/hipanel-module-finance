<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\validators\NumberValidator;

/**
 * Class CertificateResource.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 * @deprecated Is implemented in plan module
 */
class CertificateResource extends Resource
{
    use ModelTrait;

    public $certificateType;

    public static function tableName()
    {
        return 'resource';
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
        $rules[] = [['data'], 'validatePrices', 'on' => ['create', 'update']];

        return $rules;
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
        foreach ([1, 2] as $period) {
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
            /// XXX throw new InvalidConfigException('Period ' . $period . ' is not available');
        }

        return (float) $this->data['sums'][$period];
    }

    public function hasPriceForPeriod($period)
    {
        return !empty($this->data['sums'][$period]);
    }

    public function validatePrices()
    {
        $periods = $this->getPeriods();
        $validator = new NumberValidator();

        foreach (array_keys($periods) as $period) {
            $validation = $validator->validate($this->data['sums'][$period]);
            if ($validation === false) {
                unset($this->data['sums'][$period]);
            }
        }

        $this->data = ['sums' => $this->data['sums']];

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
