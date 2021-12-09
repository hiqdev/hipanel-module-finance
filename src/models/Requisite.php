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
    const TEMPLATE_PROFORMA = 'proforma';

    public static function tableName()
    {
        return 'requisite';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'client_id', 'recipient_id'], 'integer'],
            [['id'], 'required', 'on' => ['reserve-number', 'set-templates', 'set-serie']],
            [['client_id', 'recipient_id'], 'required', 'on' => ['reserve-number']],
            [['invoice_id', 'acceptance_id', 'contract_id', 'probation_id', 'internal_invoice_id', 'proforma_id'], 'safe'],
            [['serie'], 'safe'],
            [['serie'], 'required', 'on' => ['set-serie', 'update']],
            [['invoice_id'], 'required', 'on' => ['set-templates', 'update']],
            [['invoice_name', 'acceptance_name', 'contract_name', 'probation_name', 'internal_invoice_name', 'proforma_name'], 'safe'],
            [['balance', 'balances'], 'safe'],
        ]);
    }

    public function isRequisite()
    {
        return (boolean) $this->is_requisite;
    }

    public function isEmpty($fields) : bool
    {
        $fields = is_string($fields) ? array_map(function($v) {
            return trim($v);
        },  explode(",", $fields)) : $fields;

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
            'acceptance_id' => Yii::t('hipanel:finance', 'Acceptance template'),
            'contract_id' => Yii::t('hipanel:finance', 'Contract template'),
            'probation_id' => Yii::t('hipanel:finance', 'Probation template'),
            'requisites' => Yii::t('hipanel:finance', 'Requisites'),
            'invoice_name' => Yii::t('hipanel:finance', 'Invoice template'),
            'acceptance_name' => Yii::t('hipanel:finance', 'Acceptance template'),
            'contract_name' => Yii::t('hipanel:finance', 'Contract template'),
            'probation_name' => Yii::t('hipanel:finance', 'Probation template'),
            'recipient_id' => Yii::t('hipanel:finance', 'Recipient'),
            'balance' => Yii::t('hipanel:finance', 'Balance'),
        ]);
    }

    public static function getTemplatesTypes(): array
    {
        return [
            self::TEMPLATE_INVOICE => self::TEMPLATE_INVOICE,
            self::TEMPLATE_ACCEPTANCE => self::TEMPLATE_ACCEPTANCE,
            self::TEMPLATE_CONTRACT => self::TEMPLATE_CONTRACT,
            self::TEMPLATE_PROBATION => self::TEMPLATE_PROBATION,
            self::TEMPLATE_INTERNAL_INVOICE => self::TEMPLATE_INTERNAL_INVOICE,
            self::TEMPLATE_PROFORMA => self::TEMPLATE_PROFORMA,
        ];
    }
}
