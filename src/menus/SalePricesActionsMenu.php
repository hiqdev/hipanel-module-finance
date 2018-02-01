<?php

namespace hipanel\modules\finance\menus;

use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\Sale;
use Yii;

/**
 * Class TariffActionsMenu
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class SalePricesActionsMenu extends \hiqdev\yii2\menus\Menu
{
    /**
     * @var \hipanel\modules\finance\models\Sale
     */
    public $model;

    public function items()
    {
        return $this->getGenerationButtons();
    }

    protected function getGenerationButtons()
    {
        $buttons = [];

        foreach ($this->suggestionTypesByObject() as $config) {
            $buttons[] = [
                'label' => $config['label'],
                'icon' => $config['icon'],
                'url' => $this->suggestionLink($config['type']),
                'visible' => Yii::$app->user->can('plan.update'),
            ];
        }

        return $buttons;
    }

    protected function suggestionLink($type)
    {
        return ['@price/suggest', 'plan_id' => $this->model->tariff_id, 'object_id' => $this->model->object_id, 'type' => $type];
    }

    protected function suggestionTypesByObject()
    {
        if ($this->model instanceof FakeSale && empty($this->model->tariff_type)) {
            return [
                [
                    'type' => 'default',
                    'label' => Yii::t('hipanel.finance.price', 'Main prices'),
                    'icon' => 'fa-plus',
                ],
            ];
        }

        switch ($this->model->tariff_type) {
            case Sale::SALE_TYPE_SERVER:
                return [
                    [
                        'type' => 'default',
                        'label' => Yii::t('hipanel.finance.price', 'Main prices'),
                        'icon' => 'fa-plus',
                    ],
                    [
                        'type' => 'parts',
                        'label' => Yii::t('hipanel.finance.price', 'Part prices'),
                        'icon' => 'fa-hdd-o',
                    ]
                ];
        }

        return [];
    }
}
