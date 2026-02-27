<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\components\User;
use hipanel\grid\BoxedGridView;
use hipanel\modules\stock\widgets\combo\PartnoCombo;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\stock\widgets\combo\LocationsCombo;
use hipanel\modules\finance\models\Installment;
use Yii;
use yii\helpers\Html;

class InstallmentGridView extends BoxedGridView
{
    private User $user;

    public function init()
    {
        parent::init();
        $this->user = Yii::$app->user;
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
            'serial' => [
                'label' => Yii::t('hipanel:stock', 'Serial'),
                'filterOptions' => ['class' => 'narrow-filter'],
                'filterAttribute' => 'serial_ilike',
                'format' => 'raw',
                'value' => fn($model) => Html::a(Html::encode($model->serial), ['@part/view', 'id' => $model->id], ['class' => 'text-bold']),
            ],
            'model' => [
                'filterAttribute' => 'model_like',
                'filter' => function ($column, $model, $attribute) {
                    return PartnoCombo::widget([
                        'model' => $model,
                        'attribute' => $attribute,
                        'formElementSelector' => 'td',
                    ]);
                },
                'format' => 'raw',
                'label' => Yii::t('hipanel:stock', 'Part No.'),
                'value' => static function (Installment $model): string {
                    $partNo = Html::encode($model->model);
                    if (Yii::$app->user->can('model.read')) {
                        return Html::a($partNo, ['@model/view', 'id' => $model->model_id], [
                            'data' => ['toggle' => 'tooltip'],
                            'title' => Html::encode(sprintf(
                                "%s %s",
                                Yii::t('hipanel:stock', $model->model_type_label),
                                Yii::t('hipanel:stock', $model->model_brand_label),
                            )),
                        ]);
                    }

                    return $partNo;
                },
            ],
            'device' => [
                'filterAttribute' => 'device_like',
                'format' => 'raw',
                'value' => static function (Installment $model) {
                    return Html::tag('b', Html::encode($model->device), ['style' => 'margin-left:1em']);
                },
            ],
        ]);
    }
}
