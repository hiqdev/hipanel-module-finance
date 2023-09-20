<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

use Yii;
use yii\base\Model;

class Costprice extends Model
{
    use \hipanel\base\ModelTrait;

    private const TYPE_ADMIN = 'admin_split';
    private const TYPE_COLOCATION = 'colocation_split';
    private const TYPE_IP = 'ip_split';
    private const TYPE_HW = 'hw_split';
    private const TYPE_NRC = 'nrc_split';
    private const TYPE_SALARIES = 'salaries_split';

    public function rules()
    {
        return [
            [['mask', 'month','type'], 'safe'],
        ];
    }

    public static function getAvailableType(): array
    {
        return [
            'all' => Yii::t('hipanel:client', 'All'),
            self::TYPE_ADMIN => Yii::t('hipanel:client', 'Admin'),
            self::TYPE_COLOCATION => Yii::t('hipanel:client', 'Colocation'),
            self::TYPE_IP => Yii::t('hipanel:client', 'IP'),
            self::TYPE_HW => Yii::t('hipanel:client', 'HW'),
            self::TYPE_NRC => Yii::t('hipanel:client', 'NRC'),
            self::TYPE_SALARIES => Yii::t('hipanel:client', 'Salaries'),
        ];
    }

}