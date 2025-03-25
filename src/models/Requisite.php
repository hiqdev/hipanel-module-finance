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

use hipanel\modules\client\models\Contact;
use hipanel\modules\document\models\Document;
use Yii;

/**
 * Class Requisite.
 */
class Requisite extends Contact
{
    /*
     * @return array the list of attributes for this record
     */
    use \hipanel\base\ModelTrait;

    const TEMPLATE_INVOICE = 'invoice';
    const TEMPLATE_ACCEPTANCE = 'acceptance';
    const TEMPLATE_CONTRACT = 'contract';
    const TEMPLATE_PROBATION = 'probation';
    const TEMPLATE_INTERNAL_INVOICE = 'internal_invoice';
    const TEMPLATE_PAYMENT_REQUEST = 'payment_request';
    const TEMPLATE_PURCHASE_INVOICE = 'purchase_invoice';
    const TEMPLATE_SERVICE_INVOICE = 'service_invoice';
    const TEMPLATE_PURCHASE_PAYMENT_REQUEST = 'purchase_payment_request';
    const TEMPLATE_SERVICE_PAYMENT_REQUEST = 'service_payment_request';
    const TEMPLATE_DETAILED_SERVICE_INVOICE = 'detailed_service_invoice';
    const TEMPLATE_DETAILED_PAYMENT_REQUEST = 'detailed_service_payment_request';

    public static function tableName()
    {
        return 'requisite';
    }

    public function rules()
    {
        $rules = array_merge(parent::rules(), [
            [['client_id', 'country', 'first_name', 'email', 'postal_code', 'city', 'street1'], 'required', 'on' => 'create'],
            [['id', 'client_id', 'recipient_id'], 'integer'],
            [['id'], 'required', 'on' => ['reserve-number', 'set-templates', 'set-serie']],
            [['client_id', 'recipient_id'], 'required', 'on' => ['reserve-number']],
            [['serie'], 'safe'],
            [['serie'], 'required', 'on' => ['set-serie', 'update']],
            [['invoice_id'], 'required', 'on' => ['set-templates', 'update']],
            [['balance', 'balances', 'documents_by_types'], 'safe'],
            [
                [
                    'invoice_id',
                    'acceptance_id',
                    'contract_id',
                    'probation_id',
                    'internal_invoice_id',
                    'payment_request_id',
                    'service_invoice_id',
                    'purchase_invoice_id',
                    'service_payment_request_id',
                    'purchase_payment_request_id',
                    'detailed_service_payment_request_id',
                    'detailed_service_invoice_id',
                    'nda_id',
                ],
                'string', // template2pdf ID
            ],
            [
                [
                    'invoice_name',
                    'acceptance_name',
                    'contract_name',
                    'probation_name',
                    'internal_invoice_name',
                    'purchase_invoice_name',
                    'service_invoice_name',
                    'purchase_payment_request_name',
                    'service_payment_request_name',
                    'payment_request_name',
                    'detailed_service_payment_request_name',
                    'detailed_service_invoice_name',
                    'nda_name',
                ],
                'string',
            ],
        ]);
        $templatesTypes = [];
        foreach (self::getTemplatesTypes() as $templatesType) {
            $templatesTypes[] = "{$templatesType}_id";
        }
        $rules[] = [$templatesTypes, 'string', 'on' => 'create'];
        $rules[] = [['invoice_id'], 'required', 'on' => 'create'];

        return $rules;
    }

    public function isRequisite()
    {
        return (bool)$this->is_requisite;
    }

    public function isEmpty($fields): bool
    {
        $fields = is_string($fields) ? array_map(function ($v) {
            return trim($v);
        }, explode(",", $fields)) : $fields;

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'serie' => Yii::t('hipanel:finance', 'Serie'),
            'invoice_id' => Yii::t('hipanel:finance', 'Invoice template'),
            'service_invoice_id' => Yii::t('hipanel:finance', 'Service invoice template'),
            'purchase_invoice_id' => Yii::t('hipanel:finance', 'Purchase invoice template'),
            'payment_request_id' => Yii::t('hipanel:finance', 'Payment request template'),
            'service_payment_request_id' => Yii::t('hipanel:finance', 'Service payment request template'),
            'purchase_payment_request_id' => Yii::t('hipanel:finance', 'Purchase payment request template'),
            'acceptance_id' => Yii::t('hipanel:finance', 'Acceptance template'),
            'contract_id' => Yii::t('hipanel:finance', 'Contract template'),
            'probation_id' => Yii::t('hipanel:finance', 'Probation template'),
            'requisites' => Yii::t('hipanel:finance', 'Requisites'),
            'invoice_name' => Yii::t('hipanel:finance', 'Invoice template'),
            'acceptance_name' => Yii::t('hipanel:finance', 'Acceptance template'),
            'contract_name' => Yii::t('hipanel:finance', 'Contract template'),
            'probation_name' => Yii::t('hipanel:finance', 'Probation template'),
            'payment_request_name' => Yii::t('hipanel:finance', 'Payment request template'),
            'recipient_id' => Yii::t('hipanel:finance', 'Recipient'),
            'balance' => Yii::t('hipanel:finance', 'Balance'),
        ]);
    }

    public static function getTemplatesTypes(): array
    {
        return [
            self::TEMPLATE_INVOICE => self::TEMPLATE_INVOICE,
            self::TEMPLATE_PURCHASE_INVOICE => self::TEMPLATE_PURCHASE_INVOICE,
            self::TEMPLATE_SERVICE_INVOICE => self::TEMPLATE_SERVICE_INVOICE,
            self::TEMPLATE_ACCEPTANCE => self::TEMPLATE_ACCEPTANCE,
            self::TEMPLATE_CONTRACT => self::TEMPLATE_CONTRACT,
            self::TEMPLATE_PROBATION => self::TEMPLATE_PROBATION,
            self::TEMPLATE_INTERNAL_INVOICE => self::TEMPLATE_INTERNAL_INVOICE,
            self::TEMPLATE_PAYMENT_REQUEST => self::TEMPLATE_PAYMENT_REQUEST,
            self::TEMPLATE_PURCHASE_PAYMENT_REQUEST => self::TEMPLATE_PURCHASE_PAYMENT_REQUEST,
            self::TEMPLATE_SERVICE_PAYMENT_REQUEST => self::TEMPLATE_SERVICE_PAYMENT_REQUEST,
            self::TEMPLATE_DETAILED_SERVICE_INVOICE => self::TEMPLATE_DETAILED_SERVICE_INVOICE,
            self::TEMPLATE_DETAILED_PAYMENT_REQUEST => self::TEMPLATE_DETAILED_PAYMENT_REQUEST,
        ];
    }

    public function getDocumentsByTypes(): array
    {
        $documents = [];
        if (empty($this->documents_by_types)) {
            return $documents;
        }
        foreach ($this->documents_by_types as $type => $rawData) {
            $document = new Document();
            $document->setAttributes($rawData, false);
            $documents[$type] = $document;
        }

        return $documents;
    }
}
