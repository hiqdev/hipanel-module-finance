<?php
declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use hipanel\base\ModelTrait;
use hipanel\hiart\hiapi\Connection;
use hipanel\modules\finance\models\Target;
use hiqdev\hiart\ResponseInterface;
use Yii;

final class TargetManagementForm extends Target
{
    use ModelTrait;

    public static function tableName()
    {
        return 'target';
    }

    public function fillFromTarget(Target $target): void
    {
        $this->setAttribute('remoteid', $target->id);
        $this->setAttribute('target_id', $target->id);
        $this->setAttribute('plan_id', $target->tariff_id);
        $this->setAttribute('type', $target->type);
        $this->setAttribute('name', $target->name);
        $this->setAttribute('customer_id', $target->client_id);
    }

    public function rules()
    {
        return [
            [['remoteid', 'type', 'name', 'plan_id', 'time', 'customer_id'], 'required', 'on' => 'change-plan'],
            [['target_id', 'plan_id', 'time', 'customer_id'], 'required', 'on' => 'close-sale'],
            [['remoteid', 'type', 'name', 'plan_id', 'time', 'customer_id'], 'required', 'on' => 'sale'],
            [['time'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'plan_id' => Yii::t('hipanel:finance', 'Tariff plan'),
            'time' => Yii::t('hipanel', 'Time'),
            'customer_id' => Yii::t('hipanel', 'Client'),
        ]);
    }

    public function submit(Connection $api): ResponseInterface
    {
        $actionsMap = [
            'change-plan' => 'api/v1/target/sale',
            'close-sale' => 'api/v1/sale/close',
            'sale' => 'api/v1/target',
        ];

        return $api->post($actionsMap[$this->scenario], [], $this->attributes);
    }
}
