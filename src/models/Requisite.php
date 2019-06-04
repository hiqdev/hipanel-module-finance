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

use borales\extensions\phoneInput\PhoneInputValidator;
use hipanel\modules\client\models\query\ContactQuery;
use hipanel\modules\document\models\Document;
use Yii;

/**
 * Class Requisite.
 *
 * @property Requisite[] $localizations
 * @property int|string $id
 * @property bool $gdpr_consent
 * @property bool $policy_consent
 */
class Requisite extends \hipanel\base\Model
{
    /*
     * @return array the list of attributes for this record
     */
    use \hipanel\base\ModelTrait;

    public $oldEmail;

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'setBankDetails']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'setBankDetails']);
    }

    public function setBankDetails($insert)
    {
        $this->bank_details = $this->renderBankDetails();
    }

    public function rules()
    {
        return array_filter([
            [['id', 'obj_id', 'client_id', 'seller_id'], 'integer'],
            [['type_id', 'state_id'], 'integer'],
            [['client_name', 'client_type'], 'safe'],
            [['create_time', 'update_time', 'created_date', 'updated_date'], 'date'],
            [['client', 'seller', 'state', 'type'], 'safe'],
            [['email', 'abuse_email', 'email_new'], 'email'],
            [['emails'], 'trim'],
            [['country', 'country_name', 'province', 'province_name'], 'safe'],
            [['postal_code'], 'safe'],
            [['city', 'street1', 'street2', 'street3', 'address'], 'safe'],
            [['street1', 'street2', 'street3'], 'string', 'max' => 60],
            [['voice_phone', 'fax_phone'], 'safe'],
            [['icq', 'skype', 'jabber', 'viber', 'telegram', 'whatsapp', 'social_net'], 'safe'],
            [['roid', 'epp_id', 'remoteid', 'other_messenger'], 'safe'],
            [['name', 'first_name', 'last_name'], 'string'],
            [['birth_date', 'passport_date'], 'safe'],
            [['passport_no', 'passport_by', 'organization', 'password', 'xxx_token'], 'safe'],
            [['localization'], 'safe'],
            [['invoice_last_no'], 'safe'],

            [['reg_data', 'vat_number', 'tax_comment', 'bank_details'], 'trim'],
            [['bank_account', 'bank_name', 'bank_address', 'bank_swift'], 'trim'],
            [['vat_number', 'tax_comment'], 'string'],
            [['vat_rate'], 'number', 'max' => 99],

            [['remote', 'file'], 'safe'],
            [['used_count'], 'integer'],
            [
                ['voice_phone', 'fax_phone'],
                'match',
                'pattern' => '/^[+]?[()0-9 .-]{3,20}$/',
                'message' => Yii::t('hipanel:client', 'This field must contains phone number in international format.'),
            ],
            [['voice_phone', 'fax_phone'], PhoneInputValidator::class],

            Yii::$app->user->can('manage') ? null : [
                [
                    'first_name',
                    'last_name',
                    'email',
                    'street1',
                    'city',
                    'country',
                    'postal_code',
                    'voice_phone',
                ],
                'required', 'on' => ['create', 'create-require-passport', 'update', 'update-require-passport'],
            ],

            [['pincode', 'oldEmail'], 'safe', 'on' => ['update', 'update-require-passport']],

            [['isresident', 'is_requisite'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            [['birth_date', 'passport_date'], 'safe', 'on' => ['update', 'create', 'create-require-passport', 'update-require-passport']],
            [
                [
                    // Для регистрации доменов в зоне RU в качестве физического лица
                    'passport_no',
                    'passport_by',

                    // Для регистрации доменов в зоне RU в качестве юридического лица
                    'organization_ru',
                    'director_name',
                    'inn',
                    'kpp',
                ],
                'safe',
            ],
            [
                [
                    // Для регистрации доменов в зоне RU в качестве физического лица
                    'passport_no', 'passport_by',
                    'birth_date', 'passport_date',
                ],
                'required',
                'on' => ['create-require-passport', 'update-require-passport'],
            ],
            [
                [
                    'email_confirmed', 'email_confirm_date',
                    'voice_phone_confirmed', 'voice_phone_confirm_date',
                    'fax_phone_confirmed', 'fax_phone_confirm_date',
                    'name_confirm_level', 'name_confirm_date',
                    'address_confirm_level', 'address_confirm_date',
                ], 'safe',
            ],
            [
                ['id'],
                'required',
                'on' => ['request-email-confirmation', 'request-phone-confirmation', 'delete', 'update', 'update-require-passport'],
            ],
            [
                ['gdpr_consent', 'policy_consent'], 'default', 'value' => 1, 'on' => ['create'],
            ],
            [
                ['gdpr_consent', 'policy_consent'],
                'required', 'requiredValue' => 1,
                'on' => ['update'],
                'when' => function () { return (string) Yii::$app->user->getId() === (string) $this->id; },
                'message' => Yii::t('hipanel:client', 'We need your permission in order to provide services'),
            ],
            [['id', 'client_id'], 'required', 'on' => ['reserve-number']],
        ]);
    }

    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'name'              => Yii::t('hipanel', 'Name'),
            'first_name'        => Yii::t('hipanel:client', 'First name'),
            'last_name'         => Yii::t('hipanel:client', 'Last name'),
            'organization'      => Yii::t('hipanel:client', 'Organization'),
            'abuse_email'       => Yii::t('hipanel:client', 'Abuse email'),
            'passport_no'       => Yii::t('hipanel:client', 'Passport number'),
            'icq'               => 'ICQ',
            'voice_phone'       => Yii::t('hipanel:client', 'Phone'),
            'fax_phone'         => Yii::t('hipanel:client', 'Fax'),
            'country_name'      => Yii::t('hipanel:client', 'Country'),
            'country'           => Yii::t('hipanel:client', 'Country'),
            'isresident'        => Yii::t('hipanel:client', 'RF resident'),
            'street1'           => Yii::t('hipanel:client', 'Address'),
            'street2'           => Yii::t('hipanel:client', 'Address 2'),
            'street3'           => Yii::t('hipanel:client', 'Address 3'),
            'inn'               => Yii::t('hipanel:client', 'Taxpayer identification number'),
            'kpp'               => Yii::t('hipanel:client', 'Code of reason for registration'),
            'organization_ru'   => Yii::t('hipanel:client', 'Organization (Russian title)'),
            'director_name'     => Yii::t('hipanel:client', 'Director\'s full name'),
            'address'           => Yii::t('hipanel:client', 'Address'),
            'city'              => Yii::t('hipanel:client', 'City'),
            'province'          => Yii::t('hipanel:client', 'Province'),
            'postal_code'       => Yii::t('hipanel:client', 'Postal code'),
            'birth_date'        => Yii::t('hipanel:client', 'Birth date'),
            'messengers'        => Yii::t('hipanel:client', 'Messengers'),
            'other_messenger'   => Yii::t('hipanel:client', 'Other messenger'),
            'passport_date'     => Yii::t('hipanel:client', 'Passport issue date'),
            'passport_by'       => Yii::t('hipanel:client', 'Issued by'),
            'social_net'        => Yii::t('hipanel:client', 'Social'),
            'reg_data'          => Yii::t('hipanel:client', 'Registration data'),
            'vat_number'        => Yii::t('hipanel:client', 'VAT number'),
            'vat_rate'          => Yii::t('hipanel:client', 'VAT rate'),
            'bank_account'      => Yii::t('hipanel:client', 'Bank account'),
            'bank_name'         => Yii::t('hipanel:client', 'Bank name'),
            'bank_address'      => Yii::t('hipanel:client', 'Bank address'),
            'bank_swift'        => Yii::t('hipanel:client', 'SWIFT code'),
            'localization'      => Yii::t('hipanel:client', 'Localization'),
            'xxx_token'         => Yii::t('hipanel:client', 'XXX Token'),
            'policy_consent'    => Yii::t('hipanel:client', 'Privacy policy agreement'),
            'gdpr_consent'      => Yii::t('hipanel:client', 'GDPR policy agreement'),
            'invoice_last_no' => Yii::t('hipanel:client', 'Last document number'),
        ]);
    }

    /**
     * Returns verification model for the $attribute.
     *
     * @param string $attribute
     * @return Verification
     */
    public function getVerification($attribute)
    {
        return Verification::fromModel($this, $attribute);
    }

    public function getDocuments()
    {
        if (!Yii::getAlias('@document', false)) {
            return null;
        }

        return $this->hasMany(Document::class, ['object_id' => 'id'])->joinWith('file')->joinWith('statuses');
    }

    public function scenarioActions()
    {
        return [
            'request-email-confirmation' => 'notify-confirm-email',
            'request-phone-confirmation' => 'notify-confirm-phone',
            'gdpr-consent' => 'update',
        ];
    }

    public function getLocalizations()
    {
        return $this->hasMany(self::class, ['id' => 'id']);
    }

    public function getName()
    {
        return $this->name ?: $this->first_name . ' ' . $this->last_name;
    }

    public function getMessengers()
    {
        $res = [];
        $messengers = [
            'skype' => 'Skype',
            'icq' => 'ICQ',
            'jabber' => 'Jabber',
            'viber' => 'Viber',
            'telegram' => 'Telegram',
            'whatsapp' => 'WhatsApp',
        ];
        foreach ($messengers as $k => $label) {
            if ($this->{$k}) {
                $res[] = "<b>$label:</b>&nbsp;" . $this->{$k};
            }
        }

        return implode('<br>', $res);
    }

    public function renderAddress()
    {
        $res = implode("\n", array_filter([$this->street1, $this->street2, $this->street3])) . "\n";
        $res .= $this->postal_code . ' ';
        $res .= $this->province . ' ';
        $res .= $this->city . ', ';
        $res .= $this->country_name;

        return preg_replace('/ +/', ' ', $res);
    }

    public function renderBankDetails()
    {
        return implode("\n", array_filter([
            $this->renderBankAccount($this->bank_account),
            $this->renderBankName($this->bank_name),
            $this->renderBankAddress($this->bank_address),
            $this->renderBankSwift($this->bank_swift),
        ]));
    }

    public function renderBankAccount($iban)
    {
        if (empty($iban)) {
            return null;
        }

        return strpos($iban, "\n")===false ? "IBAN: $iban" : $iban;
    }

    public function renderBankName($name)
    {
        return $name ? "Bank Name: $name" : null;
    }

    public function renderBankAddress($address)
    {
        return $address ? 'Bank Address: ' . $address : null;
    }

    public function renderBankSwift($swift)
    {
        return $swift ? 'SWIFT code: ' . $swift : null;
    }

    /**
     * {@inheritdoc}
     * @return ContactQuery
     */
    public static function find($options = [])
    {
        return new ContactQuery(get_called_class(), [
            'options' => $options,
        ]);
    }

    public function isRequisite()
    {
        return (boolean) $this->is_requisite;
    }
}
