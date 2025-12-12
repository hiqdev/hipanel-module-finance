<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;
use hipanel\grid\RefColumn;
use hipanel\grid\XEditableColumn;
use hipanel\helpers\Url;
use hipanel\models\Ref;
use hipanel\modules\client\grid\ContactGridView;
use hipanel\modules\finance\forms\GenerateInvoiceForm;
use hipanel\modules\finance\menus\RequisiteActionsMenu;
use hipanel\modules\finance\models\Requisite;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\helpers\Html;

/**
 *
 * @property-read array $staticColumns
 */
class RequisiteGridView extends ContactGridView
{
    public $resizableColumns = false;

    public function columns()
    {
        $currencies = array_keys(Ref::getList('type,currency'));
        $formatter = Yii::$app->formatter;
        $cellLabels = [
            'balance' => Yii::t('hipanel:finance', "Balance"),
            'debit' => Yii::t('hipanel:finance', "Debit"),
            'credit' => Yii::t('hipanel:finance', "Credit"),
        ];
        $labelColors = [
            'debit' => '#ECFDF5',
            'credit' => '#FEF2F2',
            'balance' => 'inherit',
        ];

        return array_merge(
            parent::columns(),
            $this->getStaticColumns(),
            $this->getCurrencyColumns($currencies, $cellLabels, $labelColors, $formatter),
            ['eur_balance' => $this->getEurBalanceColumn($formatter)],
            $this->getBalanceColumns($cellLabels, $labelColors, $formatter)
        );
    }

    private function getCurrencyColumns(array $currencies, array $cellLabels, array $labelColors, $formatter): array
    {
        $columns = [];
        foreach ($currencies as $currency) {
            $columns[$currency] = [
                'format' => 'html',
                'attribute' => 'balances',
                'filter' => false,
                'label' => Yii::t('hipanel:finance', strtoupper($currency)),
                'enableSorting' => false,
                'contentOptions' => [
                    'class' => 'no-padding',
                    'style' => 'width: 1%; white-space: nowrap;',
                ],
                'value' => function (Requisite $model) use ($currency, $cellLabels, $formatter, $labelColors): string {
                    $tags = [];
                    foreach ($cellLabels as $attribute => $label) {
                        $balance = $model->balances[$currency][$attribute] ?? null;
                        $color = $labelColors[$attribute] ?? null;
                        $tags[] = Html::tag(
                            'span',
                            $formatter->asCurrency($balance, $currency),
                            array_filter([
                                'title' => $label,
                                'style' => $color ? "background-color: $color;" : null,
                                'class' => 'text-right ' . ($attribute === 'balance' ? 'text-bold' : ''),
                            ])
                        );
                    }

                    return empty($tags) ? '' : Html::tag('span', implode('', $tags), ['class' => 'balance-cell']);
                },
                'exportedColumns' => ["export_{$currency}_credit", "export_{$currency}_debit", "export_{$currency}_balance"],
            ];
            foreach ($cellLabels as $attribute => $label) {
                $columns["export_{$currency}_$attribute"] = [
                    'label' => "$currency $attribute",
                    'value' => fn($model) => $this->plainSum($model->balances[$currency][$attribute] ?? null),
                ];
            }
        }

        return $columns;
    }

    private function getEurBalanceColumn($formatter): array
    {
        return [
            'format' => 'raw',
            'attribute' => 'eur_balance',
            'filter' => false,
            'enableSorting' => false,
            'label' => Yii::t('hipanel:finance', 'Converted to EUR balance'),
            'headerOptions' => [
                'style' => 'width: 1%; white-space: nowrap;',
            ],
            'contentOptions' => [
                'style' => 'text-align: center; vertical-align: middle;',
            ],
            'value' => fn(Requisite $model): ?string => !empty($model->balance['eur_balance']) ? Html::tag(
                'span',
                $formatter->asCurrency($model->balance['eur_balance'], 'eur')
            ) : null,
            'exportedValue' => fn($model): ?string => $this->plainSum($model->balance['eur_balance'] ?? null),
        ];
    }

    private function getBalanceColumns(array $cellLabels, array $labelColors, $formatter): array
    {
        $columns = [];
        foreach ($cellLabels as $attribute => $label) {
            $columns[$attribute] = [
                'format' => 'raw',
                'attribute' => 'balance',
                'filter' => false,
                'label' => $label,
                'contentOptions' => [
                    'style' => "width: 1%; white-space: nowrap; background-color: $labelColors[$attribute]",
                    'class' => 'text-right ' . ($attribute === 'balance' ? 'text-bold' : ''),
                ],
                'value' => function (Requisite $model) use ($attribute, $formatter): string {
                    if (!isset($model->balance[$attribute])) {
                        return '';
                    }
                    $balance = $model->balance[$attribute];
                    $currency = $model->balance->currency ?? $model->balance['currency'] ?? 'usd';

                    return !empty($balance) ? Html::tag('span', $formatter->asCurrency($balance, $currency)) : '';
                },
                'exportedValue' => function (Requisite $model) use ($attribute) {
                    return $this->plainSum($model->balance[$attribute]);
                },
            ];
        }

        return $columns;
    }

    private function getStaticColumns(): array
    {
        return [
            'name' => [
                'class' => MainColumn::class,
                'filterAttribute' => 'name_ilike',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id])
                        . "<br>"
                        . Html::encode($model->organization);
                },
                'exportedColumns' => ['export_name', 'export_organization'],
            ],
            'export_name' => [
                'label' => Yii::t('hipanel', 'Name'),
                'value' => fn($model) => $model->name,
            ],
            'export_organization' => [
                'label' => Yii::t('hipanel', 'Organization'),
                'value' => fn($model) => $model->organization,
            ],
            'serie' => [
                'class' => XEditableColumn::class,
                'pluginOptions' => [
                    'url' => Url::to('@requisite/set-serie'),
                ],
                'contentOptions' => ['style' => 'width: 1%; white-space: nowrap;'],
                'filterOptions' => ['class' => 'narrow-filter'],
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => RequisiteActionsMenu::class,
                'contentOptions' => ['style' => 'width: 1%; white-space: nowrap;'],
            ],
            'invoice_name' => [
                'class' => RefColumn::class,
                'filter' => false,
                'format' => 'raw',
                'i18nDictionary' => 'hipanel:finance',
                'value' => function ($model) {
                    return Html::encode($model->invoice_name);
                },
            ],
            'templates' => [
                'class' => RequisiteTemplateColumn::class,
            ],
            'last_no' => [
                'label' => Yii::t('hipanel:finance', 'Last numbers'),
                'format' => 'raw',
                'filter' => false,
                'value' => function (Requisite $model) {
                    $value = '';
                    foreach ([
                                 'invoice_last_no',
                                 'sinvoice_last_no',
                                 'pinvoice_last_no',
                                 'payment_request_last_no',
                                 'spayment_request_last_no',
                                 'ppayment_request_last_no',
                             ] as $attr) {
                        if (empty($model->$attr)) {
                            continue;
                        }
                        $value .= Html::tag('p', Html::tag("b", "{$model->getAttributeLabel($attr)}: ") . ($model->$attr ?? ''));
                    }

                    return $value;
                },
            ],
        ];
    }

    public function plainSum($sum): float|string
    {
        if (!is_string($sum)) return '';
        if (empty($sum)) return 0.0;

        return (float)$sum;
    }

    public static function getRequisiteColumns(Requisite $requisite): array
    {
        $columns = [];
        $documents = $requisite->getDocumentsByTypes();
        $form = new GenerateInvoiceForm();
        foreach ($documents as $documentName => $document) {
            $columnName = "{$documentName}_name";
            $columns[$columnName] = [
                'attribute' => $columnName,
                'format' => 'raw',
                'value' => function (Requisite $model) use ($columnName, $form, $document) {
                    return Html::a(
                        Yii::t('hipanel:finance', $model->{$columnName}),
                        '@document/generate-document',
                        [
                            'data' => [
                                'method' => 'POST',
                                'params' => [
                                    "{$form->formName()}[id]" => $document->id,
                                ],
                            ],
                        ]

                    );
                },
            ];
        }

        return $columns;
    }
}
