<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic\bill;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\providers\BillTypesProvider;
use hipanel\modules\server\models\Consumption;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;
use InvalidArgumentException;

/**
 * Class QuantityFormatterFactory.
 */
final class QuantityFormatterFactory implements QuantityFormatterFactoryInterface
{
    /**
     * @var array maps bill type to a QuantityFormatter
     * // TODO: use DI to configure
     */
    private $types = [
        'monthly'           => MonthlyQuantity::class,

        'monthly,rack_unit' => RackUnitQuantity::class,
        'ip_num'            => IPNumQuantity::class,
        'support_time'      => SupportTimeQuantity::class,
        'devops_support'    => SupportTimeQuantity::class,
        'referral'          => DefaultQuantityFormatter::class,
        'server_traf_max'   => DefaultQuantityFormatter::class,
        'vps_traf_max'      => DefaultQuantityFormatter::class,
        'server_traf95_max' => DefaultQuantityFormatter::class,
        'cdn_traf_max'      => DefaultQuantityFormatter::class,
        'cdn_traf95_max'    => DefaultQuantityFormatter::class,
        'cdn_cache95'       => DefaultQuantityFormatter::class,
        'backup_du'         => DefaultQuantityFormatter::class,
        'server_du'         => DefaultQuantityFormatter::class,
        'storage_du'        => DefaultQuantityFormatter::class,
        'storage_du95'      => DefaultQuantityFormatter::class,
        'win_license'       => DefaultQuantityFormatter::class,
        'hw_purchase'       => DefaultQuantityFormatter::class,
        'server_ssd'        => DefaultQuantityFormatter::class,
        'server_sata'       => DefaultQuantityFormatter::class,
        'power'             => DefaultQuantityFormatter::class,
        'drenewal'          => DomainRenewalQuantity::class,
    ];

    public function __construct(private readonly IntlFormatter $intlFormatter, private readonly BillTypesProvider $billTypesProvider)
    {
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

        throw new InvalidArgumentException('Create is not supported for the passed model');
    }

    /**
     * @param Bill|BillForm $bill
     * @return QuantityFormatterInterface|null
     */
    public function forBill($bill): ?QuantityFormatterInterface
    {
        $qty = Quantity::create($bill->unit, $bill->quantity);

        return $this->createByType($bill->type, $qty, $bill);
    }

    public function forCharge(Charge $charge): ?QuantityFormatterInterface
    {
        $qty = Quantity::create($charge->unit, $charge->quantity);

        return $this->createByType($charge->ftype ?? $charge->type, $qty, $charge);
    }

    public function forConsumption(Consumption $consumption): ?QuantityFormatterInterface
    {
        $qty = Quantity::create($consumption->unit, $consumption->quantity);

        return $this->createByType($consumption->type, $qty, $consumption);
    }

    /** {@inheritdoc} */
    public function createByType(?string $type, Quantity $quantity, $context = null): ?QuantityFormatterInterface
    {
        if (empty($type)) {
            $types = ArrayHelper::index($this->billTypesProvider->getTypes(), 'id');
            $type = isset($types[$context->id]) ? $types[$context->id]->name : null;
        }
        if ($type !== null && !isset($this->types[$type])) {
            if (strpos($type, 'monthly,') === 0) {
                $type = 'monthly';
            } else {
                $type = $this->fixType($type);
            }
        }

        if (isset($this->types[$type])) {
            $className = $this->types[$type];
            /** @var QuantityFormatterInterface $formatter */
            $formatter = new $className($quantity, $this->intlFormatter);

            if ($formatter instanceof ContextAwareQuantityFormatter) {
                $formatter->setContext($context);
            }

            return $formatter;
        }

        return null;
    }

    private function fixType(string $type): ?string
    {
        if (str_contains($type, ',')) {
            $types = explode(',', $type);

            return end($types);
        }

        return $type;
    }
}
