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

use hipanel\helpers\Url;
use Yii;

class RequisiteDetailMenu extends \hipanel\menus\AbstractDetailMenu
{
    public $model;

    public function items()
    {
        $items = RequisiteActionsMenu::create([
            'model' => $this->model,
        ])->items();

        if (Yii::getAlias('@document', false)) {
            $items[] = [
                'label' => Yii::t('hipanel:client', 'Documents'),
                'icon' => 'fa-paperclip',
                'url' => ['@contact/attach-documents', 'id' => $this->model->id],
            ];
        }

        unset($items['view']);

        return $items;
    }
}
