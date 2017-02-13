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

use hipanel\models\File;
use hipanel\modules\client\models\Client;
use hipanel\modules\client\models\Contact;
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
            [['id', 'client_id', 'seller_id'], 'integer'],
            [['client', 'seller'], 'safe'],
            [['provided_services'], 'safe'],
            [['contact_id', 'requisite_id'], 'integer'],
            [['currency_id'], 'integer'],
            [['currency'], 'safe'],
            [['no'], 'integer'],
            [['credit', 'balance'], 'number'],

            [['month'], 'date', 'on' => 'update-monthly-invoice'],
            [['id', 'contact_id'], 'required', 'on' => ['update-contact']],
            [['id', 'requisite_id'], 'required', 'on' => ['update-requisite']],
        ];
    }

    public function getFiles()
    {
        return $this->hasMany(File::class, ['object_id' => 'id']);
    }

    public function getClientModel()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function getContact()
    {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
    }

    public function getRequisite()
    {
        return $this->hasOne(Contact::class, ['id' => 'requisite_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'provided_services' => Yii::t('hipanel:finance', 'Provided services'),
            'currency' => Yii::t('hipanel:finance', 'Currency'),
            'invoices' => Yii::t('hipanel:finance', 'Invoices'),
            'contact_id' => Yii::t('hipanel:finance', 'Contact'),
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
        ]);
    }

    public function scenarioActions()
    {
        return [
            'update-contact' => 'update',
            'update-requisite' => 'update',
        ];
    }
}
