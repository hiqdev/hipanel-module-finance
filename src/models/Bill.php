<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use Yii;

class Bill extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public $time_from;
    public $time_till;

    public static $i18nDictionary = 'hipanel/finance';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'seller_id', 'id'],    'integer'],
            [['object_id', 'tariff_id'],          'integer'],
            [['client', 'seller', 'bill'],        'safe'],
            [['domain', 'server', 'time'],        'safe'],
            [['sum', 'balance', 'quantity'],      'number'],
            [['currency', 'label', 'descr'],      'safe'],
            [['object', 'domains', 'tariff'],     'safe'],
            [['type', 'gtype', 'class'],          'safe'],
            [['class_label'],                     'safe'],
            [['type_label', 'gtype_label'],       'safe'],

            [['id'],                              'integer', 'on' => 'delete'],

            [['client_id'], 'integer', 'on' => 'create'],
            [['type', 'label'], 'safe', 'on' => 'create'],
            [['time'], 'date', 'format' => 'php:d.m.Y H:i:s', 'on' => 'create'],
            [['sum'], 'number', 'on' => 'create'],
            [['client_id', 'type', 'label', 'sum', 'currency', 'time'], 'required', 'on' => 'create'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'client'      => Yii::t('hipanel', 'Client'),
            'time'        => Yii::t('hipanel', 'Time'),
            'currency'    => Yii::t('hipanel', 'Currency'),
            'balance'     => Yii::t('hipanel', 'Balance'),
            'gtype'       => Yii::t('app', 'Type'),
            'gtype_label' => Yii::t('app', 'Type'),
            'sum'         => Yii::t('hipanel/finance', 'Sum'),
        ]);
    }
}
