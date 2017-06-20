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
    const TYPE_CERT_RENEW = 'certificate_renew';

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
        $rules['create-required-price'] = [['price'], 'required', 'on' => ['create', 'update']];
        $rules[] = [['certificateType'], 'safe'];

        return $rules;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return [
            static::TYPE_CERT_REGISTRATION => Yii::t('hipanel:finance:tariff', 'Registration'),
            static::TYPE_CERT_RENEW => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }
}
