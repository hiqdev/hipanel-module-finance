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

use hipanel\models\Ref;
use Yii;

/**
 * Class Tariff.
 * @property resource[]|DomainResource[]|ServerResource[] $resources
 */
class ProfileTariff extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public static function tableName()
    {
        return 'profiletariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $main = [
            [['name'], 'safe'],
            [
                ['name'],
                'required',
                'on' => ['update', 'create'],
                'when' => function ($model) {
                    return $model->id != $model->client_id;
                },
            ],
            [['id'], 'integer'],
            [['id'], 'required', 'on' => ['update', 'delete']],
            [['tariff_names'], 'safe'],
            [['seller_id', 'client_id'], 'integer'],
            [['seller', 'client'], 'safe'],
            [['tariffs'], 'safe'],
        ];

        $tariffTypes = [Tariff::TYPE_DOMAIN, Tariff::TYPE_CERT, Tariff::TYPE_XEN, Tariff::TYPE_OPENVZ, Tariff::TYPE_SERVER];
        foreach ($tariffTypes as $type) {
            if (in_array($type, [Tariff::TYPE_DOMAIN, Tariff::TYPE_CERT], true)) {
                $main[] = [[$type], 'integer'];
            } else {
                $main[] = [[$type], 'filter', 'filter' => function($value) { return explode(",", $value); }];
                $main[] = [[$type], 'each', 'rule' => ['trim'], 'on' => ['update', 'create']];
                $main[] = [[$type], 'each', 'rule' => ['integer'], 'on' => ['update', 'create']];
            }
        }

        return $main;
    }

    public function beforeValidate()
    {
        foreach ([Tariff::TYPE_XEN, Tariff::TYPE_OPENVZ, Tariff::TYPE_SERVER] as $attribute) {
            if (empty($this->$attribute)) {
                continue ;
            }
            $this->$attribute = reset($this->$attribute);
        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'name' => Yii::t('hipanel.finance.profiletariff', 'Name'),
            'tariff_names' => Yii::t('hipanel.finance.profiletariff', 'Tariffs'),
            'domain' => Yii::t('hipanel.finance.profiletariff', 'Domain tariff'),
            'certificate' => Yii::t('hipanel.finance.profiletariff', 'Certificate tariff'),
            'svds' => Yii::t('hipanel.finance.profiletariff', 'XEN tariffs'),
            'ovds' => Yii::t('hipanel.finance.profiletariff', 'Open-VZ tariffs'),
            'server' => Yii::t('hipanel.finance.profiletariff', 'Server tariffs'),
        ]);
    }

}
