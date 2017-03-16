<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\modules\client\widgets\combo\ContactCombo;
use hiqdev\xeditable\widgets\ComboXEditable;
use Yii;

class PurseGridView extends \hipanel\grid\BoxedGridView
{
    public static function defaultColumns()
    {
        return [
            'balance' => [
                'class' => 'hipanel\modules\finance\grid\BalanceColumn',
            ],
            'credit' => CreditColumn::resolveConfig(),
            'invoices' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'invoice',
            ],
            'acceptances' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'acceptance',
            ],
            'contracts' => [
                'class' => DocumentsColumn::class,
                'type' => 'contract',
            ],
            'probations' => [
                'class' => DocumentsColumn::class,
                'type' => 'probation',
            ],
            'ndas' => [
                'class' => DocumentsColumn::class,
                'type' => 'nda',
            ],
            'taxes' => [
            ],
            'contact' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel:finance', 'Contact'),
                'value' => function ($model) {
                    $organization = $model->contact->organization;
                    $result = $organization . ($organization ? ' / ' : '') . $model->contact->name;

                    if (!Yii::$app->user->can('manage')) {
                        return $result;
                    }

                    return ComboXEditable::widget([
                        'model' => $model,
                        'attribute' => 'contact_id',
                        'value' => $result,
                        'pluginOptions' => [
                            'url' => ['@purse/update-contact', 'id' => $model->id],
                        ],
                        'combo' => [
                            'class' => ContactCombo::class,
                            'filter' => [
                                'client_id' => ['format' => $model->id],
                            ],
                            'current' => [
                                $model->contact_id => $result,
                            ],
                            'pluginOptions' => [
                                'select2Options' => [
                                    'width' => '40rem',
                                ],
                            ],
                        ],
                    ]);
                },
            ],
            'requisite' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel:finance', 'Payment details'),
                'value' => function ($model) {
                    $organization = $model->requisite->organization;
                    $result = $organization . ($organization ? ' / ' : '') . $model->requisite->name;

                    if (!Yii::$app->user->can('manage')) {
                        return $result;
                    }

                    return ComboXEditable::widget([
                        'model' => $model,
                        'attribute' => 'requisite_id',
                        'value' => $result,
                        'pluginOptions' => [
                            'url' => ['@purse/update-requisite', 'id' => $model->id],
                        ],
                        'combo' => [
                            'class' => ContactCombo::class,
                            'filter' => [
                                'client_id' => ['format' => $model->seller_id],
                            ],
                            'current' => [
                                $model->requisite_id => $result,
                            ],
                            'pluginOptions' => [
                                'select2Options' => [
                                    'width' => '40rem',
                                ],
                            ],
                        ],
                    ]);
                },
            ],
        ];
    }
}
