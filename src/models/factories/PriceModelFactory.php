<?php

namespace hipanel\modules\finance\models\factories;

use hipanel\helpers\StringHelper;
use hipanel\modules\finance\models\CertificatePrice;
use hipanel\modules\finance\models\DomainServicePrice;
use hipanel\modules\finance\models\DomainZonePrice;
use hipanel\modules\finance\models\TemplatePrice;
use hipanel\modules\finance\models\Price;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class PriceModelFactory builds objects, that must be children of [[Price]] class
 * using short class name. See definitions in $map property.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceModelFactory
{
    /**
     * @var array
     */
    protected $map = [
        'Price' => Price::class,
        'TemplatePrice' => TemplatePrice::class,
        'SinglePrice' => [
            'domain,*' => DomainZonePrice::class,
            'feature,*' => DomainServicePrice::class,
            '*' => Price::class
        ],
        'CertificatePrice' => CertificatePrice::class,
        'EnumPrice' => CertificatePrice::class,
        'DomainZonePrice' => DomainZonePrice::class,
        'DomainServicePrice' => DomainServicePrice::class,
    ];

    /**
     * @param string $className
     * @param null|string $priceType
     * @return Price
     * @throws InvalidConfigException
     */
    public function build(string $className, ?string $priceType = null)
    {
        if (!isset($this->map[$className])) {
            throw new InvalidConfigException('Can not create model for class ' . $className);
        }

        if (is_array($this->map[$className])) {
            foreach ($this->map[$className] as $type => $class) {
                if (StringHelper::matchWildcard($type, (string)$priceType)) {
                    return new $class();
                }
            }
        }

        return new $this->map[$className]();
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    public function instantiate(string $className): Model
    {
        return new $className;
    }
}
