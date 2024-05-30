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

use hipanel\models\File;
use hipanel\modules\client\models\Client;
use hipanel\modules\client\models\Contact;
use hipanel\modules\document\models\Document;
use hipanel\models\Ref;
use Yii;

/**
 * Class Purse.
 *
 * @property string|int id
 * @property string|float currency
 * @property string|float balance
 * @property string credit
 * @property string month
 * @property Client clientModel
 * @property Document[] contracts
 * @property Document[] probations
 * @property Document[] acceptances
 * @property Document[] invoices
 * @property Document[] purchase_invoices
 * @property Document[] service_invoices
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
            [['no', 'count'], 'integer'],
            [['credit', 'balance'], 'number'],

            [['id', 'contact_id'], 'required', 'on' => ['update-contact']],
            [['id', 'requisite_id'], 'required', 'on' => ['update-requisite']],

            [['month'], 'date', 'format' => 'php:Y-m', 'on' => ['generate-and-save-monthly-document']],
            [['month'], 'required', 'on' => ['generate-and-save-monthly-document']],
            [['type'], 'string', 'on' => ['generate-and-save-monthly-document', 'generate-and-save-document']],
            [['client_bank_account_no', 'seller_bank_account_no'], 'number', 'on' => ['generate-and-save-monthly-document', 'generate-and-save-document']],
            [['client_id', 'currency'], 'required', 'on' => ['create']],
            [['id'], 'required', 'on' => 'update'],
        ];
    }

    public function getFiles()
    {
        return $this->hasMany(File::class, ['object_id' => 'id']);
    }

    public function getDocuments()
    {
        if (Yii::getAlias('@document', false)) {
            return $this->hasMany(Document::class, ['object_id' => 'id']);
        }

        return [];
    }

    public function getInvoices()
    {
        return $this->getDocumentsOfType('invoice');
    }

    public function getServiceInvoices()
    {
        return $this->getDocumentsOfType('service_invoice');
    }

    public function getPurchaseInvoices()
    {
        return $this->getDocumentsOfType('purchase_invoice');
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

    public function getInternalInvoices()
    {
        return $this->getDocumentsOfType('internal_invoice');
    }

    public function getPaymentRequestInvoices()
    {
        return $this->getDocumentsOfType('payment_request');
    }

    public function getPurchasePaymentRequests(): array
    {
        return $this->getDocumentsOfType('purchase_payment_request');
    }

    public function getServicePaymentRequests(): array
    {
        return $this->getDocumentsOfType('service_payment_request');
    }

    public function getDocumentsOfType($type): array
    {
        if (Yii::$app->user->can('document.read') === false) {
            return [];
        }

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
            'serviceInvoices' => Yii::t('hipanel:finance', 'Service Invoices'),
            'purchaseInvoices' => Yii::t('hipanel:finance', 'Purchase Invoices'),
            'payment_requestInvoices' => Yii::t('hipanel:finance', 'Payment Request'),
            'purchasePaymentRequests' => Yii::t('hipanel:finance', 'Purchase Payment Request'),
            'servicePaymentRequests' => Yii::t('hipanel:finance', 'Service Payment Request'),
            'acceptances' => Yii::t('hipanel:finance', 'Acceptance reports'),
            'contracts' => Yii::t('hipanel:finance', 'Contracts'),
            'probations' => Yii::t('hipanel:finance', 'Probation'),
            'ndas' => Yii::t('hipanel:finance', 'NDA'),
            'contact_id' => Yii::t('hipanel:finance', 'Contact'),
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
            'month' => Yii::t('hipanel:finance', 'Period'),
            'client_bank_account_no' => Yii::t('hipanel:finance', 'Contractor bank acccount'),
            'seller_bank_account_no' => Yii::t('hipanel:finance', 'Customer bank acccount'),
        ]);
    }

    public function scenarioActions()
    {
        return [
            'update-contact' => 'update',
            'update-requisite' => 'update',
        ];
    }

    /**
     * Full available budget, including the credit
     */
    public function getBudget(): float
    {
        return (float)$this->balance + (float)$this->credit;
    }

    public static function getCurrencyOptions(): array
    {
        return Ref::getList('type,currency');
    }
}
