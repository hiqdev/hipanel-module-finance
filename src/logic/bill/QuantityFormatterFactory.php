<?php

namespace hipanel\modules\finance\logic\bill;

use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
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
    protected $types = [
        'support_time' => SupportTimeQuantity::class,
        'server_traf_max' => DefaultQuantityFormatter::class,
        'server_traf95_max' => DefaultQuantityFormatter::class,
        'backup_du' => DefaultQuantityFormatter::class,
        'server_du' => DefaultQuantityFormatter::class,
        'hw_purchase' => DefaultQuantityFormatter::class,
        'ip_num' => IPNumQuantity::class,
        'monthly' => MonthlyQuantity::class,
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
    public function create(Bill $model): ?QuantityFormatterInterface
    {
        return $this->forBill($model);
    }

    public function forBill(Bill $bill): ?QuantityFormatterInterface
    {
        return $this->createByType($bill->type, Quantity::create($bill->unit, $bill->quantity), $bill);
    }

    public function forCharge(Charge $charge): ?QuantityFormatterInterface
    {
        return $this->createByType($charge->type, Quantity::create($charge->unit, $charge->quantity), $charge);
    }

    /** {@inheritdoc} */
    public function createByType(string $type, Quantity $quantity, $context = null): ?QuantityFormatterInterface
    {
        $type = $this->fixType($type);
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
