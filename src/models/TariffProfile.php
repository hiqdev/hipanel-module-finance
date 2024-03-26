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
 *
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
            [['items'], 'safe', 'on' => ['create', 'update']],
            [['tariffs'], 'safe'],
        ];

        foreach ($this->getTariffTypes() as $type) {
            if (in_array($type, $this->getDomainTariffTypes(), true)) {
                $main[] = [[$type], 'integer'];
            } else {
                $main[] = [[$type], 'filter', 'filter' => static fn(?string $value): array => explode(',', (string)$value)];
                $main[] = [[$type], 'each', 'rule' => ['trim'], 'on' => ['update', 'create']];
                $main[] = [[$type], 'each', 'rule' => ['integer'], 'on' => ['update', 'create']];
            }
        }

        return $main;
    }

    public function beforeValidate()
    {
        foreach ($this->getNotDomainTariffTypes() as $attribute) {
            if (empty($this->$attribute)) {
                continue;
            }
            $this->$attribute = count($this->$attribute) > 1 ? implode(',', $this->$attribute) : reset($this->$attribute);
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
            Plan::TYPE_DOMAIN => Yii::t('hipanel.finance.tariffprofile', 'Domain tariff'),
            Plan::TYPE_CERTIFICATE => Yii::t('hipanel.finance.tariffprofile', 'Certificate tariff'),
            Plan::TYPE_SVDS => Yii::t('hipanel.finance.tariffprofile', 'XEN tariffs'),
            Plan::TYPE_OVDS => Yii::t('hipanel.finance.tariffprofile', 'Open-VZ tariffs'),
            Plan::TYPE_SERVER => Yii::t('hipanel.finance.tariffprofile', 'Server tariffs'),
            Plan::TYPE_VPS => Yii::t('hipanel.finance.tariffprofile', 'VPS tariffs'),
            Plan::TYPE_VCDN => Yii::t('hipanel.finance.tariffprofile', 'VideoCDN tariffs'),
            Plan::TYPE_ANYCASTCDN => Yii::t('hipanel.finance.tariffprofile', 'AnycastCDN tariffs'),
            Plan::TYPE_SNAPSHOT => Yii::t('hipanel.finance.tariffprofile', 'Snapshot tariffs'),
            Plan::TYPE_VOLUME => Yii::t('hipanel.finance.tariffprofile', 'Volume tariffs'),
            Plan::TYPE_STORAGE => Yii::t('hipanel.finance.tariffprofile', 'Storage tariffs'),
            Plan::TYPE_PRIVATE_CLOUD => Yii::t('hipanel.finance.tariffprofile', 'Private cloud tariffs'),
            Plan::TYPE_PRIVATE_CLOUD_BACKUP => Yii::t('hipanel.finance.tariffprofile', 'Private cloud backup tariffs'),
            Plan::TYPE_LOAD_BALANCER => Yii::t('hipanel.finance.tariffprofile', 'Load balancer tariffs'),
            Plan::TYPE_MANAGED_KUBERNETES_CLUSTER => Yii::t('hipanel.finance.tariffprofile', 'Kubernetes cluster tariffs'),
        ]);
    }

    /**
     * Check is model is default profile
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return ((string)$this->id === (string)$this->client_id);
    }

    /**
     * Human-readable title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->isDefault() ? Yii::t('hipanel.finance.tariffprofile', 'Default') : $this->name;
    }

    public function getTariffTypes(): array
    {
        return [
            Plan::TYPE_DOMAIN,
            Plan::TYPE_CERTIFICATE,
            Plan::TYPE_SVDS,
            Plan::TYPE_OVDS,
            Plan::TYPE_SERVER,
            Plan::TYPE_VPS,
            Plan::TYPE_VCDN,
            Plan::TYPE_ANYCASTCDN,
            Plan::TYPE_SNAPSHOT,
            Plan::TYPE_VOLUME,
            Plan::TYPE_STORAGE,
            Plan::TYPE_PRIVATE_CLOUD_BACKUP,
            Plan::TYPE_PRIVATE_CLOUD,
            Plan::TYPE_LOAD_BALANCER,
            Plan::TYPE_MANAGED_KUBERNETES_CLUSTER,
        ];
    }

    public function getDomainTariffTypes(): array
    {
        return [Plan::TYPE_DOMAIN, Plan::TYPE_CERTIFICATE];
    }

    public function getNotDomainTariffTypes(): array
    {
        return array_diff($this->getTariffTypes(), $this->getDomainTariffTypes());
    }

    public function reassignTariffTypeAttributes(): void
    {
        foreach ($this->getTariffTypes() as $type) {
            if (empty($this->$type)) {
                continue;
            }
            $ids = explode(',', $this->$type);
            $this->$type = array_combine($ids, $ids);
        }
    }
}
