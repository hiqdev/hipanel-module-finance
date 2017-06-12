<?php

namespace hipanel\modules\finance\models;

use Yii;

class Charge extends \hiqdev\hiart\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    private $isNewRecord;

    public function rules()
    {
        return [
            [['id', 'type_id', 'object_id', 'bill_id'], 'integer'],
            [['type', 'label', 'ftype', 'time', 'type_label', 'currency'], 'safe'],
            [['sum', 'quantity'], 'number'],

            [['sum', 'type', 'label', 'quantity'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['id'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]]
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sum' => Yii::t('hipanel', 'Sum'),
            'type' => Yii::t('hipanel', 'Type'),
            'quantity' => Yii::t('hipanel', 'Quantity'),
            'label' => Yii::t('hipanel', 'Description'),
            'time' => Yii::t('hipanel', 'Time'),
        ]);
    }

    public function markAsNotNew()
    {
        $this->isNewRecord = false;
    }

    public function getIsNewRecord()
    {
        return $this->isNewRecord !== false && parent::getIsNewRecord();
    }
}
