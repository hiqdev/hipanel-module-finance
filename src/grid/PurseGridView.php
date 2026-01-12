<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\modules\client\widgets\combo\ContactCombo;
use hipanel\modules\finance\widgets\combo\RequisitesCombo;
use hiqdev\xeditable\widgets\ComboXEditable;
use Yii;
use yii\helpers\Html;

class PurseGridView extends BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'balance' => [
                'class' => 'hipanel\modules\finance\grid\BalanceColumn',
            ],
            'credit' => CreditColumn::resolveConfig(),
            'invoices' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'invoice',
            ],
            'detailed_service_invoices' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'detailed_service_invoice',
            ],
            'serviceInvoices' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'service_invoice',
            ],
            'purchaseInvoices' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'purchase_invoice',
            ],
            'installmentInvoices' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'installment_invoice',
            ],
            'payment_requestInvoices' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'payment_request',
            ],
            'detailed_service_payment_requests' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'detailed_service_payment_request',
            ],
            'servicePaymentRequests' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'service_payment_request',
            ],
            'purchasePaymentRequests' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'purchase_payment_request',
            ],
            'installmentPaymentRequests' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'installment_payment_request',
            ],
            'paymentplanPaymentRequests' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'payment_plan_payment_request',
            ],
            'acceptances' => [
                'class' => MonthlyDocumentsColumn::class,
                'type' => 'acceptance',
            ],
            'internalinvoices' => [
                'class' => ActsDocumentsColumn::class,
                'type' => 'internal_invoice',
                'label' => Yii::t('hipanel:finance', 'Internal invoice'),
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
                    $organization = $model->contact->organization ?? '';
                    $result = Html::encode($organization . ($organization ? ' / ' : '') . $model->contact->name);

                    if (!Yii::$app->user->can('purse.update')) {
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
                    $result = '';
                    if (isset($model->requisite)) {
                        $organization = $model->requisite->organization;
                        $result = Html::encode($organization . ($organization ? ' / ' : '') . $model->requisite->name);
                    }

                    if (!Yii::$app->user->can('purse.update')) {
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
                            'class' => RequisitesCombo::class,
                            'filter' => [
                                'client_id' => Yii::$app->user->can('owner-staff') ? [] : ['format' => $model->seller_id],
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
        ]);
    }
}
