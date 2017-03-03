<?php

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use hiqdev\hiart\ActiveQuery;
use hiqdev\hiart\ActiveRecord;

class ExchangeRate extends ActiveRecord
{
    use ModelTrait;

    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     * @return ActiveQuery
     */
    public static function find()
    {
        return parent::find()->action('search-rates');
    }

    public function rules()
    {
        return [
            [['from', 'to', 'rate'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'from' => \Yii::t('hipanel:finance', 'From'),
            'to' => \Yii::t('hipanel:finance', 'To'),
            'rate' => \Yii::t('hipanel:finance', 'Rate')
        ];
    }
}
