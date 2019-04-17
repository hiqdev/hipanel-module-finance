<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\menus;

use hipanel\modules\finance\models\FakeGroupingSale;
use hipanel\modules\finance\models\FakeSharedSale;
use hipanel\modules\finance\models\Sale;
use Yii;

/**
 * Class TariffActionsMenu.
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
        switch ($this->model->tariff_type) {
            case Sale::SALE_TYPE_SERVER:
            case Sale::SALE_TYPE_SWITCH:
                return array_filter([
                    [
                        'type' => 'default',
                        'label' => Yii::t('hipanel.finance.price', 'Main prices'),
                        'icon' => 'fa-plus',
                    ],
                    [
                        'type' => 'services',
                        'label' => Yii::t('hipanel.finance.price', 'Additional services'),
                        'icon' => 'fa-codiepie',
                    ],
                    !$this->model instanceof FakeGroupingSale && !$this->model instanceof FakeSharedSale ? [
                        'type' => 'parts',
                        'label' => Yii::t('hipanel.finance.price', 'Hardware prices'),
                        'icon' => 'fa-hdd-o',
                    ] : null,
                ]);
            case Sale::SALE_TYPE_PCDN:
            case Sale::SALE_TYPE_VCDN:
                return [
                    [
                        'type' => 'default',
                        'label' => Yii::t('hipanel.finance.price', 'Main prices'),
                        'icon' => 'fa-plus',
                    ],
                ];
        }

        return [];
    }
}
