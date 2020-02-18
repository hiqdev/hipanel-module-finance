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
use hipanel\actions\RenderAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\models\query\ChargeQuery;
use hipanel\modules\finance\providers\BillTypesProvider;
use yii\base\Event;

/**
 * Class ChargeController
 * @package hipanel\modules\finance\controllers
 */
class ChargeController extends CrudController
{
    /**
     * @var BillTypesProvider
     */
    private $billTypesProvider;

    public function __construct($id, $module, BillTypesProvider $billTypesProvider, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->billTypesProvider = $billTypesProvider;
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access-bill' => [
                'class' => EasyAccessControl::class,
                'actions' => [
                    '*' => 'bill.charges.read',
                ],
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
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
                'data' => function (RenderAction $action, array $data): array {
                    [$billTypes, $billGroupLabels] = $this->getTypesAndGroups();

                    return compact('billTypes', 'billGroupLabels');
                },
            ],
        ]);
    }

    /**
     * @return array
     */
    private function getTypesAndGroups()
    {
        return $this->billTypesProvider->getGroupedList();
    }
}
