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

use hipanel\base\ModelTrait;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\validators\NumberValidator;

/**
 * @property array $sums
 */
class CertificatePrice extends Price
{
    use ModelTrait;

    public const TYPE_CERT_PURCHASE = 'certificate,certificate_purchase';
    public const TYPE_CERT_RENEWAL = 'certificate,certificate_renewal';

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
                            $sums[$key] = new Money($value, new Currency(strtoupper($this->currency)));
                        }

                        return $sums;
                    }, ],
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
                    }, ],
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
        $rules[] = [['sums'], 'number', 'min' => 0, 'when' => function () {
            return false;
        }];
        $rules[] = [['sums'], 'each', 'rule' => ['number', 'min' => 0]];

        return $rules;
    }

    /**
     * @return bool
     */
    public function isTypeCorrect(): bool
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
            // XXX only 1 year available now
            // 2 => Yii::t('hipanel:finance:tariff', '{n, plural, one{# year} other{# years}}', ['n' => 2]),
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

    /**
     * @param int $period
     * @return string|null
     */
    public function getPriceForPeriod(int $period)
    {
        return $this->moneyFormatter->format($this->getMoneyForPeriod($period));
    }

    public function getMoneyForPeriod(int $period): ?Money
    {
        if (!$this->hasPriceForPeriod($period)) {
            return null;
        }

        return $this->sums[$period];
    }

    /**
     * @param int $period
     * @return bool
     */
    public function hasPriceForPeriod(int $period)
    {
        return !empty($this->sums[$period]);
    }

    public function validatePrices(): bool
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
    public static function getTypes()
    {
        return [
            static::TYPE_CERT_PURCHASE => Yii::t('hipanel:finance:tariff', 'Purchase'),
            static::TYPE_CERT_RENEWAL => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }
}
