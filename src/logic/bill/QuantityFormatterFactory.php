<?php

namespace hipanel\modules\finance\logic\bill;

use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\server\models\Consumption;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;

/**
 * Class QuantityFormatterFactory
 */
final class QuantityFormatterFactory implements QuantityFormatterFactoryInterface
{
    /**
     * @var array maps bill type to a QuantityFormatter
     * // TODO: use DI to configure
     */
    private $types = [
        'support_time' => SupportTimeQuantity::class,
        'monthly,support_time' => MonthlyQuantity::class,

        'server_traf_max' => DefaultQuantityFormatter::class,
        'monthly,server_traf_max' => MonthlyQuantity::class,

        'server_traf95_max' => DefaultQuantityFormatter::class,
        'monthly,server_traf95_max' => MonthlyQuantity::class,

        'backup_du' => DefaultQuantityFormatter::class,
        'monthly,backup_du' => MonthlyQuantity::class,

        'server_du' => DefaultQuantityFormatter::class,
        'monthly,server_du' => DefaultQuantityFormatter::class,

        'ip_num' => IPNumQuantity::class,
        'monthly,ip_num' => MonthlyQuantity::class,

        'monthly' => MonthlyQuantity::class,
        'monthly,hardware' => MonthlyQuantity::class,
        'monthly,rack_unit' => MonthlyQuantity::class,

        'hw_purchase' => DefaultQuantityFormatter::class,
        'drenewal' => DomainRenewalQuantity::class,
    ];
    /**
     * @var IntlFormatter
     */
    private $intlFormatter;

    public function __construct(IntlFormatter $intlFormatter)
    {
        $this->intlFormatter = $intlFormatter;
    }

    /** {@inheritdoc} */
    public function create($model): ?QuantityFormatterInterface
    {
        if ($model instanceof Bill || $model instanceof BillForm) {
            return $this->forBill($model);
        }

        if ($model instanceof Charge) {
            return $this->forCharge($model);
        }

        throw new \InvalidArgumentException('Create is not supported for the passed model');
    }

    /**
     * @param Bill|BillForm $bill
     * @return QuantityFormatterInterface|null
     */
    public function forBill($bill): ?QuantityFormatterInterface
    {
        return $this->createByType($bill->type, Quantity::create($bill->unit, $bill->quantity), $bill);
    }

    public function forCharge(Charge $charge): ?QuantityFormatterInterface
    {
        return $this->createByType($charge->type, Quantity::create($charge->unit, $charge->quantity), $charge);
    }

    public function forConsumption(Consumption $consumption): ?QuantityFormatterInterface
    {
        return $this->createByType($consumption->type, Quantity::create($consumption->unit, $consumption->quantity), $consumption);
    }

    /** {@inheritdoc} */
    public function createByType(string $type, Quantity $quantity, $context = null): ?QuantityFormatterInterface
    {
        if (!isset($this->types[$type])) {
            $type = $this->fixType($type);
        }

        if ($className = $this->types[$type] ?? null) {
            /** @var QuantityFormatterInterface $formatter */
            $formatter = new $className($quantity, $this->intlFormatter);

            if ($formatter instanceof ContextAwareQuantityFormatter) {
                $formatter->setContext($context);
            }

            return $formatter;
        }

        return null;
    }

    private function fixType($type): string
    {
        if (strpos($type, ',') !== false) {
            $types = explode(',', $type);
            return end($types);
        }

        return $type;
    }
}
