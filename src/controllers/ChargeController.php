<?php

/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\models\query\ChargeQuery;
use yii\base\Event;

class ChargeController extends CrudController
{

//    public function behaviors()
//    {
//        return array_merge(parent::behaviors(), [
//            'access-bill' => [
//                'class' => EasyAccessControl::class,
//                'actions' => [
////                    '*'                     => 'bill.read',
//                ],
//            ],
//        ]);
//    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'on beforePerform' => function (Event $event) {
                    /** @var ChargeQuery $query */
                    $query = $event->sender->getDataProvider()->query;
                    $query->withCommonObject()->withLatestCommonObject();
                },
            ],
        ]);
    }
}
