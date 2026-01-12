<?php declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\widgets\TagsManager;
use ReflectionClass;
use Yii;

trait AmountColumns
{
    protected function amountColumns(): array
    {
        return [
            'client' => [
                'format' => 'raw',
                'exportedColumns' => ['client', 'client_tags'],
                'value' => function (Bill|Charge $model) {
                    $clientColumn = (new ReflectionClass(ClientColumn::class))->newInstanceWithoutConstructor();

                    return nl2br(implode("\n", [
                        $clientColumn->getValue($model, Yii::$app->user),
                        TagsManager::widget(['model' => $model->customer, 'forceReadOnly' => true]),
                    ]));
                },
            ],
            'net_amount' => [
                'attribute' => 'net_amount',
                'headerOptions' => ['class' => 'text-right'],
                'value' => fn(Bill|Charge $model): string => $model->net_amount ? $this->formatter->asCurrency(
                    $model->net_amount,
                    $model->currency
                ) : '',
                'enableSorting' => false,
                'filter' => false,
                'exportedValue' => fn($charge) => $this->plainSum($charge->net_amount),
            ],
            'eur_amount' => [
                'attribute' => 'eur_amount',
                'headerOptions' => ['class' => 'text-right'],
                'value' => fn($charge): string => $charge->eur_amount ? $this->formatter->asCurrency($charge->eur_amount, 'eur') : '',
                'enableSorting' => false,
                'filter' => false,
                'exportedValue' => fn($charge) => $this->plainSum($charge->eur_amount),
            ],
            'rate' => [
                'attribute' => 'rate',
                'enableSorting' => false,
                'filter' => false,
            ],
        ];
    }

    protected function plainSum($sum): float|string
    {
        if (!is_string($sum)) return '';
        if (empty($sum)) return 0.0;

        return (float)$sum;
    }
}
