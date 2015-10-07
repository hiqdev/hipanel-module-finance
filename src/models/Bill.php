<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (https://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use Yii;

class Bill extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public $time_from;
    public $time_till;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'seller_id', 'id'],    'integer'],
            [['client', 'seller', 'bill'],        'safe'],
            [['domain', 'server'],                'safe'],
            [['time'],                            'date'],
            [['sum', 'balance', 'quantity'],      'number'],
            [['currency', 'label', 'descr'],      'safe'],
            [['object', 'domains', 'tariff'],     'safe'],
            [['type', 'gtype'],                   'safe'],
            [['type_label', 'gtype_label'],       'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'gtype'       => Yii::t('app', 'Type'),
            'gtype_label' => Yii::t('app', 'Type'),
        ]);
    }
}
