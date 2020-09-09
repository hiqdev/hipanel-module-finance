<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\CertificatePrice;
use hipanel\modules\finance\models\DomainServicePrice;
use hipanel\modules\finance\models\DomainZonePrice;
use hipanel\modules\finance\models\FakeGroupingSale;
use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\FakeSharedSale;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class PlanInternalsGrouper can be used to group prices inside $plan depending on
 * different factors.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PlanInternalsGrouper
{
    /**
     * @var Plan
     */
    private $plan;

    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * Should be used to group prices of [[Plan]] with the following types:
     * - server
     * - sVDS
     * - oVDS
     * - certificate.
     * @return array
     */
    public function group()
    {
        switch ($this->plan->type) {
            case Plan::TYPE_CERTIFICATE:
                return $this->groupCertificatePrices();
            case Plan::TYPE_HARDWARE:
                return $this->groupHardwarePrices();
            case Plan::TYPE_REFERRAL:
                return $this->plan->prices;
            case Plan::TYPE_DOMAIN:
                $byType = static function (array $servicePrices) {
                    return ArrayHelper::index($servicePrices, 'type', static function ($servicePrice) {
                        if (strpos($servicePrice->type, 'premium_dns') !== false) {
                            return 'premium_dns';
                        }
                        if (strpos($servicePrice->type, 'whois_protect') !== false) {
                            return 'whois_protect';
                        }
                    });
                };
                [$zonePrices, $servicePrices] = $this->groupDomainPrices();

                return [$zonePrices, $byType($servicePrices)];
            default:
                return $this->groupServerPrices();
        }
    }

    /**
     * @return array of two elements:
     * 0: sales, grouped by sold object
     * 1: prices, grouped by sold object
     */
    private function groupServerPrices()
    {
        $model = $this->plan;
        /** @var Sale[] $salesByObject */
        $salesByObject = [];
        /** @var Price[][] $pricesByMainObject */
        $pricesByMainObject = [];

        foreach ($model->prices as $price) {
            $pricesByMainObject[$price->main_object_id ?? $price->object_id ?? 0][$price->id] = $price;
        }

        if (isset($pricesByMainObject[0])) {
            $salesByObject[0] = new FakeSharedSale([
                'object' => Yii::t('hipanel.finance.price', 'For all sold objects'),
                'tariff_id' => $model->id,
                'tariff_type' => $model->type,
            ]);
        }
        if (isset($pricesByMainObject[$model->id])) {
            $salesByObject[$model->id] = new FakeGroupingSale([
                'object' => Yii::t('hipanel.finance.price', 'Grouping prices'),
                'object_id' => $model->id,
                'tariff_id' => $model->id,
                'tariff_type' => $model->type,
            ]);
        }
        foreach ($model->sales as $sale) {
            $salesByObject[$sale->object_id] = $sale;
        }

        foreach ($pricesByMainObject as $id => $prices) {
            if (isset($salesByObject[$id])) {
                continue;
            }

            foreach ($prices as $price) {
                if ((int) $price->main_object_id === (int) $id) {
                    $salesByObject[$id] = new FakeSale([
                        'object' => $price->main_object_name,
                        'tariff_id' => $model->id,
                        'object_id' => $id,
                        'tariff_type' => $model->type,
                    ]);
                    continue 2;
                }

                if ((int) $price->object_id === (int) $id) {
                    $salesByObject[$id] = new FakeSale([
                        'object' => $price->object->name,
                        'tariff_id' => $model->id,
                        'object_id' => $price->object_id,
                        'tariff_type' => $model->type,
                    ]);
                    continue 2;
                }
            }

            $salesByObject[$id] = new FakeSale([
                'object' => Yii::t('hipanel.finance.price', 'Unknown object name - neither direct object prices, nor resolvable references exist'),
                'tariff_id' => $model->id,
                'object_id' => $id,
                'tariff_type' => $model->type,
            ]);
        }

        return $this->sortSaleAndPrices($salesByObject, $pricesByMainObject);
    }

    private function groupHardwarePrices(): array
    {
        $model = $this->plan;
        /** @var Sale[] $salesByObject */
        $salesByObject = [];
        $salesWithId = [];
        /** @var Price[][] $pricesByMainObject */
        $pricesByMainObject = [];

        foreach ($model->prices as $price) {
            $pricesByMainObject[$price->main_object_id ?? $price->object_id ?? 0][$price->id] = $price;
        }

        foreach ($model->sales as $sale) {
            $salesWithId[$sale->object_id] = $sale;
        }

        foreach ($pricesByMainObject as $id => $prices) {
            foreach ($prices as $price) {
                if ((int)$price->main_object_id === (int)$id) {
                    $tmpSale = $salesWithId[$price->object_id];
                    $tmpSale->object = $price->main_object_name;
                    $tmpSale->tariff_id = $model->id;
                    $tmpSale->object_id = $id;
                    $tmpSale->tariff_type = 'model_group';
                    $salesByObject[$id] = $tmpSale;
                    continue 2;
                }
            }
        }

        return $this->sortSaleAndPrices($salesByObject, $pricesByMainObject);
    }

    private function sortSaleAndPrices(array $salesByObject, array $pricesByMainObject): array
    {
        foreach ($pricesByMainObject as &$objPrices) {
            $objPrices = PriceSort::anyPrices()->values($objPrices, true);
        }

        $salesByObject = SaleSort::toDisplayInPlan()->values($salesByObject, true);

        return [$salesByObject, $pricesByMainObject];
    }

    /**
     * @return array of certificate prices
     * every element of array consist of two elements:
     * certificate,certificate_purchase: CertificatePrice
     * certificate,certificate_renewal: CertificatePrice
     */
    private function groupCertificatePrices(): array
    {
        $result = [];

        foreach ($this->plan->prices as $price) {
            /** @var CertificatePrice $price */
            $result[$price->object_id][$price->type] = $price;
        }

        return $result;
    }

    /**
     * @return array[] of domain prices
     */
    private function groupDomainPrices(): array
    {
        $zonePrices = [];
        $servicePrices = [];

        foreach ($this->plan->prices as $price) {
            if ($price instanceof DomainZonePrice) {
                /** @var DomainZonePrice $price */
                $zonePrices[$price->object_id][$price->type] = $price;
            } elseif ($price instanceof DomainServicePrice) {
                /** @var DomainServicePrice $price */
                $servicePrices[$price->type] = $price;
            }
        }
        $zonePrices = PriceSort::zonePrices()->values($zonePrices, true);

        return [$zonePrices, $servicePrices];
    }
}
