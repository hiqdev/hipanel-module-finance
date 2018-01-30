<?php

namespace hipanel\modules\finance\menus;

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

        $buttons[] = [
            'label' => Yii::t('hipanel', 'Default prices'),
            'icon' => 'fa-pencil',
            'url' => ['@tariff/update', 'id' => $this->model->id],
            'visible' => Yii::$app->user->can('plan.update'),
        ];

        return $buttons;
    }
}
