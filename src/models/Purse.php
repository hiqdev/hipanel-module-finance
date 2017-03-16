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
use hipanel\modules\document\models\Document;
use Yii;

/**
 * Class Purse
 *
 * @property Client clientModel
 * @property Document[] contracts
 * @property Document[] probations
 * @property Document[] acceptances
 * @property Document[] invoices
 * @property Document[] ndas
 */
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

            [['id', 'contact_id'], 'required', 'on' => ['update-contact']],
            [['id', 'requisite_id'], 'required', 'on' => ['update-requisite']],

            [['month'], 'date', 'on' => ['generate-and-save-monthly-document']],
            [['type'], 'string', 'on' => ['generate-and-save-monthly-document', 'generate-and-save-document']],
        ];
    }

    public function getFiles()
    {
        return $this->hasMany(File::class, ['object_id' => 'id']);
    }

    public function getDocuments()
    {
        return $this->hasMany(Document::class, ['object_id' => 'id']);
    }

    public function getInvoices()
    {
        return $this->getDocumentsOfType('invoice');
    }

    public function getContracts()
    {
        return $this->getDocumentsOfType('contract');
    }

    public function getProbations()
    {
        return $this->getDocumentsOfType('probation');
    }

    public function getNdas()
    {
        return $this->getDocumentsOfType('nda');
    }

    public function getAcceptances()
    {
        return $this->getDocumentsOfType('acceptance');
    }

    public function getDocumentsOfType($type)
    {
        $res = [];

        foreach ($this->documents as $id => $doc) {
            if ($doc->type === $type) {
                $res[$id] = $doc;
            }
        }

        return $res;
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
            'acceptances' => Yii::t('hipanel:finance', 'Acceptance reports'),
            'contracts' => Yii::t('hipanel:finance', 'Contracts'),
            'probations' => Yii::t('hipanel:finance', 'Probation'),
            'ndas' => Yii::t('hipanel:finance', 'NDA'),
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
