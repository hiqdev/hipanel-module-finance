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
use hipanel\grid\DataColumn;
use hipanel\modules\finance\models\TariffProfile;
use Yii;
use yii\helpers\Html;

class TariffProfileGridView extends BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'name' => [
                'class' => 'hipanel\grid\MainColumn',
                'filterAttribute' => 'name_like',
                'note' => null,
                'value' => fn(TariffProfile $model): string => Html::a($model->getTitle(), ['@tariffprofile/view', 'id' => $model->id], ['class' => 'text-bold']),
            ],
            'tariff_names' => [
                'filter' => false,
                'format' => 'raw',
                'value' => function (TariffProfile $model) {
                    if (empty($model->tariffs)) {
                        return '';
                    }

                    foreach ($model->tariffs as $type => $values) {
                        if (empty($values)) {
                            continue;
                        }

                        $links = [];
                        foreach ($values as $id => $name) {
                            $links[$id] = $this->tariffLink($id, $name);
                        }
                        $tariffs[$type] = $model->getAttributeLabel($type) . ': ' . implode(', ', $links);
                    }

                    return implode('<br>', $tariffs);
                },
            ],
            'domain_tariff' => [
                'attribute' => 'domain',
                'format' => 'raw',
                'value' => function (TariffProfile $model) {
                    if (empty($model->domain)) {
                        return '';
                    }

                    return $this->tariffLink($model->domain, $model->tariff_names[$model->domain]);
                },
            ],
            'certificate_tariff' => [
                'attribute' => 'certificate',
                'format' => 'raw',
                'value' => function (TariffProfile $model) {
                    if (empty($model->certificate)) {
                        return '';
                    }

                    return $this->tariffLink($model->certificate, $model->tariff_names[$model->certificate]);
                },
            ],
            'actions' => [
                'class' => DataColumn::class,
                'format' => 'html',
                'value' => fn($model) => Html::a(Yii::t('hipanel', 'Update'), ['@tariffprofile/update', 'id' => $model->id], ['class' => 'btn btn-default btn-sm']),
            ],
        ], $this->getTariffColumns());
    }

    protected function tariffLink($id, string $name): string
    {
        return Html::a(Html::encode($name), ['@plan/view', 'id' => $id]);
    }

    private function getTariffColumns(): array
    {
        $model = new TariffProfile();
        $columns = [];
        foreach ($model->getTariffTypes() as $type) {
            $columns[$type . '_tariff'] = [
                'attribute' => $type,
                'format' => 'raw',
                'value' => function (TariffProfile $model) use ($type) {
                    if (empty($model->tariffs)) {
                        return '';
                    }

                    if (empty($model->tariffs[$type])) {
                        return '';
                    }

                    foreach ($model->tariffs[$type] as $id => $name) {
                        $links[$id] = $this->tariffLink($id, $name);
                    }

                    return implode(', ', $links ?? []);
                },
            ];
        }

        return $columns;
    }
}
