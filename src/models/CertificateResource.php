<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\validators\NumberValidator;

/**
 * Class CertificateResource
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CertificateResource extends Resource
{
    use ModelTrait;

    public static function tableName()
    {
        return 'resource';
    }

    const TYPE_CERT_REGISTRATION = 'certificate_purchase';
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
            3 => Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => 3]),
        ];
    }

    public function getPriceForPeriod($period)
    {
        if (!isset(self::getPeriods()[$period])) {
            throw new InvalidConfigException('Period ' . $period . ' is not available');
        }

        return (float)$this->data['prices'][$period];
    }

    public function validatePrices()
    {
        $periods = $this->getPeriods();
        $validator = new NumberValidator();

        foreach (array_keys($periods) as $period) {
            $validation = $validator->validate($this->data['prices'][$period]);
            if ($validation === false) {
                unset($this->data['prices'][$period]);
            }
        }

        $this->data = ['prices' => $this->data['prices']];

        return true;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return [
            static::TYPE_CERT_REGISTRATION => Yii::t('hipanel:finance:tariff', 'Registration'),
            static::TYPE_CERT_RENEWAL => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }
}
