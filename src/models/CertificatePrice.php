<?php

namespace hipanel\modules\finance\models;

use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Yii;
use hipanel\base\ModelTrait;
use yii\behaviors\AttributeTypecastBehavior;
use yii\validators\NumberValidator;

/**
 * @property array $sums
 */
class CertificatePrice extends Price
{
    use ModelTrait;

    const TYPE_CERT_PURCHASE = 'certificate,certificate_purchase';
    const TYPE_CERT_RENEWAL = 'certificate,certificate_renewal';

    /**
     * @var DecimalMoneyFormatter
     */
    private $moneyFormatter;

    /**
     * @var DecimalMoneyParser
     */
    private $moneyParser;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
        $this->moneyParser = new DecimalMoneyParser(new ISOCurrencies());
    }

    public static function tableName()
    {
        return 'price';
    }

    public function behaviors()
    {
        return [
            'typecastAfterFind' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'sums' => function ($sums) {
                        foreach ($sums as $key => $value) {
                            if ($value) {
                                $sums[$key] = new Money($value, new Currency(strtoupper($this->currency)));
                            }
                        }
                        return $sums;
                    }],
                'typecastAfterFind' => true,
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => false,
            ],
            'typecastBeforeSave' => [
                'class' => AttributeTypecastBehavior::class,
                'attributeTypes' => [
                    'sums' => function ($sums) {
                        foreach ($sums as $key => $value) {
                            $sums[$key] = $this->moneyParser
                                ->parse($value, strtoupper($this->currency))
                                ->getAmount();
                        }
                        return $sums;
                    }],
                'typecastAfterFind' => false,
                'typecastAfterValidate' => false,
                'typecastBeforeSave' => true,
            ],
        ];
    }


    public function rules()
    {
        $rules = parent::rules();
        $rules['create-required'] = [
            ['object_id'],
            'required',
            'on' => ['create', 'update'],
            'when' => function ($model) {
                /** @var self $model */
                return $model->isTypeCorrect();
            },
        ];
        $rules[] = [['certificateType'], 'safe'];
        $rules[] = [['sums'], 'validatePrices', 'on' => ['create', 'update']];

        return $rules;
    }

    public function isTypeCorrect()
    {
        return isset($this->getTypes()[$this->type]);
    }

    /**
     * @return array
     */
    public static function getPeriods()
    {
        return [
            1 => Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => 1]),
            2 => Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => 2]),
        ];
    }

    /**
     * @return array
     */
    public function getAvailablePeriods()
    {
        $periods = [];
        foreach ([1, 2] as $period) {
            if ($this->hasPriceForPeriod($period)) {
                $periods[$period] = Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => $period]);
            }
        }

        return $periods;
    }

    public function getPriceForPeriod($period)
    {
        if (!$this->hasPriceForPeriod($period)) {
            return null;
        }

        return $this->moneyFormatter->format($this->sums[$period]);
    }

    public function hasPriceForPeriod($period)
    {
        return !empty($this->sums[$period]);
    }

    public function validatePrices()
    {
        $periods = $this->getPeriods();
        $validator = new NumberValidator();

        foreach (array_keys($periods) as $period) {
            $validation = $validator->validate($this->sums[$period]);
            if ($validation === false) {
                unset($this->sums[$period]);
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return [
            static::TYPE_CERT_PURCHASE => Yii::t('hipanel:finance:tariff', 'Purchase'),
            static::TYPE_CERT_RENEWAL => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }
}
