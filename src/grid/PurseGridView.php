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
            'taxes' => [],
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
