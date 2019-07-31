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

use Yii;

/**
 * Class Tariff.
 * @property resource[]|DomainResource[]|ServerResource[] $resources
 */
class TariffProfile extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public static function tableName()
    {
        return 'tariffprofile';
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
                    return !$model->isDefault();
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
                $main[] = [[$type], 'filter', 'filter' => function ($value) { return explode(',', $value); }];
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
                continue;
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
            'name' => Yii::t('hipanel.finance.tariffprofile', 'Name'),
            'tariff_names' => Yii::t('hipanel.finance.tariffprofile', 'Tariffs'),
            'domain' => Yii::t('hipanel.finance.tariffprofile', 'Domain tariff'),
            'certificate' => Yii::t('hipanel.finance.tariffprofile', 'Certificate tariff'),
            'svds' => Yii::t('hipanel.finance.tariffprofile', 'XEN tariffs'),
            'ovds' => Yii::t('hipanel.finance.tariffprofile', 'Open-VZ tariffs'),
            'server' => Yii::t('hipanel.finance.tariffprofile', 'Server tariffs'),
        ]);
    }

    /**
     * Check is model is default profile
     *
     * @return bool
     */
    public function isDefault() : bool
    {
        return (bool) ((string) $this->id === (string) $this->client_id);
    }
}
