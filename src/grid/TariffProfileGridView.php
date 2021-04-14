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
use hipanel\modules\finance\menus\ProfileActionsMenu;
use hipanel\modules\finance\models\Tariff;
use hipanel\modules\finance\models\TariffProfile;
use hiqdev\yii2\menus\grid\MenuColumn;
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
                'value' => function (TariffProfile $model) {
                    if (empty($model->name) || $model->isDefault()) {
                        return Yii::t('hipanel.finance.tariffprofile', 'Default');
                    }

                    return $model->name;
                },
            ],
            'tariff_names' => [
                'filter' => false,
                'format' => 'html',
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
                'format' => 'html',
                'value' => function (TariffProfile $model) {
                    if (empty($model->domain)) {
                        return '';
                    }

                    return $this->tariffLink($model->domain, $model->tariff_names[$model->domain]);
                },
            ],
            'certificate_tariff' => [
                'attribute' => 'certificate',
                'format' => 'html',
                'value' => function (TariffProfile $model) {
                    if (empty($model->certificate)) {
                        return '';
                    }

                    return $this->tariffLink($model->certificate, $model->tariff_names[$model->certificate]);
                },
            ],
            'svds_tariff' => [
                'attribute' => 'svds',
                'format' => 'html',
                'value' => function (TariffProfile $model) {
                    if (empty($model->tariffs)) {
                        return '';
                    }

                    if (empty($model->tariffs[Tariff::TYPE_XEN])) {
                        return '';
                    }

                    foreach ($model->tariffs[Tariff::TYPE_XEN] as $id => $name) {
                        $links[$id] = $this->tariffLink($id, $name);
                    }

                    return implode(', ', $links);
                },
            ],
            'ovds_tariff' => [
                'attribute' => 'ovds',
                'format' => 'html',
                'value' => function (TariffProfile $model) {
                    if (empty($model->tariffs)) {
                        return '';
                    }

                    if (empty($model->tariffs[Tariff::TYPE_OPENVZ])) {
                        return '';
                    }

                    foreach ($model->tariffs[Tariff::TYPE_OPENVZ] as $id => $name) {
                        $links[$id] = $this->tariffLink($id, $name);
                    }

                    return implode(', ', $links);
                },
            ],
            'server_tariff' => [
                'attribute' => 'server',
                'format' => 'html',
                'value' => function (TariffProfile $model) {
                    if (empty($model->tariffs)) {
                        return '';
                    }

                    if (empty($model->tariffs[Tariff::TYPE_SERVER])) {
                        return '';
                    }

                    foreach ($model->tariffs[Tariff::TYPE_SERVER] as $id => $name) {
                        $links[$id] = $this->tariffLink($id, $name);
                    }

                    return implode(', ', $links);
                },
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => ProfileActionsMenu::class,
            ],
        ]);
    }

    protected function tariffLink($id, $name)
    {
        return Html::a($name, ['@plan/view', 'id' => $id]);
    }
}
