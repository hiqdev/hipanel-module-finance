<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use Yii;

class Purse extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'seller_id'],      'integer'],
            [['client', 'seller'],                  'safe'],
            [['provided_services'],                 'safe'],
            [['contact', 'files'],                  'safe'],
            [['contact_id', 'requisite_id'],        'integer'],
            [['currency_id'],                       'integer'],
            [['currency'],                          'safe'],
            [['no'],                                'integer'],
            [['credit', 'balance'],                  'number'],

            [['month'],                             'date', 'on' => 'update-monthly-invoice'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'provided_services' => Yii::t('app', 'Provided services'),
            'currency'          => Yii::t('app', 'Currency'),
            'invoices'          => Yii::t('app', 'Invoices'),
        ]);
    }
}
