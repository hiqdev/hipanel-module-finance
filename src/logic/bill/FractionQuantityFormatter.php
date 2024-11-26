<?php declare(strict_types=1);

namespace hipanel\modules\finance\logic\bill;

use DateTimeImmutable;
use hipanel\modules\finance\models\FractionAwareInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use hiqdev\php\units\yii2\formatters\IntlFormatter;
use InvalidArgumentException;
use Yii;

class FractionQuantityFormatter extends DefaultQuantityFormatter implements ContextAwareQuantityFormatter
{
    private FractionAwareInterface $model;

    public function __construct(
        Quantity $quantity,
        IntlFormatter $intlFormatter,
        readonly private string|FractionUnit|null $fractionUnit = null
    )
    {
        parent::__construct($quantity, $intlFormatter);
    }

    public function format(): string
    {
        $formatter = Yii::$app->formatter;
        if ($this->model->unit === 'hour') {
            $hours = $this->model->getQuantity();

            return Yii::t('hipanel:finance', '{d}', ['d' => $this->formatDurationWithoutSecondsAndMinutes($hours * 3600)]);
        }
        $hoursInCurrentMonth = (new DateTimeImmutable($this->model->getTime()))->format('t') * 24;
        $fractionOfMonth = $this->model->getFractionOfMonth();
        $units = $this->model->getQuantity() / ($fractionOfMonth != 0 ? $fractionOfMonth : 1);
        $hours = $fractionOfMonth * $hoursInCurrentMonth;

        $formattedUnites = match ($this->fractionUnit) {
            FractionUnit::SIZE => $formatter->asShortSize(Quantity::create($this->model->unit,
                $units)->convert(Unit::create('byte'))->getQuantity()),
            default => $units . ' ' . $this->fractionUnit,
        };

        return Yii::t('hipanel:finance', '{u} {d}', [
            'u' => $formattedUnites . ' &times; ',
            'd' => $this->formatDurationWithoutSecondsAndMinutes($hours * 3600),
        ]);
    }

    public function setContext(mixed $context): ContextAwareQuantityFormatter
    {
        if (!$context instanceof FractionAwareInterface) {
            throw new InvalidArgumentException('Context should be implemented FractionAwareInterface');
        }
        $this->model = $context;

        return $this;
    }

    public function formatDurationWithoutSecondsAndMinutes(int|float $seconds): string
    {
        $seconds = (int)$seconds;
        $days = intdiv($seconds, 86400);
        $months = intdiv($days, 30);
        $remainingDays = $days % 30;
        $hours = ($seconds % 86400) / 3600;

        $parts = [];
        if ($months > 0) {
            $parts[] = $months . ' ' . Yii::t('app', '{n, plural, =1{month} other{months}}', ['n' => $months]);
        }
        if ($remainingDays > 0) {
            $parts[] = $remainingDays . ' ' . Yii::t('app', '{n, plural, =1{day} other{days}}', ['n' => $remainingDays]);
        }
        if ($hours > 0) {
            $parts[] = $hours . ' ' . Yii::t('app', '{n, plural, =1{hour} other{hours}}', ['n' => $hours]);
        }

        return empty($parts) ? ' -- ' : implode(", ", $parts);
    }
}
