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
use hipanel\actions\VariantsAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\models\query\ChargeQuery;
use hipanel\modules\finance\providers\BillTypesProvider;
use hipanel\modules\finance\widgets\ChargeFinanceSummaryTable;
use hipanel\widgets\SynchronousCountEnabler;
use yii\base\Event;
use yii\grid\GridView;

/**
 * Class ChargeController
 * @package hipanel\modules\finance\controllers
 */
class ChargeController extends CrudController
{
    private BillTypesProvider $billTypesProvider;

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

                    return [
                        'billTypesList' => $this->billTypesProvider->getTypes(),
                        'clientTypes' => $this->getClientTypes(),
                        ];
                },
                'responseVariants' => [
                  IndexAction::VARIANT_SUMMARY_RESPONSE => static function (VariantsAction $action): string {
                      $dataProvider = $action->parent->getDataProvider();
                      $defaultSummary = (new SynchronousCountEnabler($dataProvider, fn(GridView $grid): string => $grid->renderSummary()))();

                      return $defaultSummary . ChargeFinanceSummaryTable::widget([
                          'currencies' => $action->controller->getCurrencyTypes(),
                          'allModels' => $dataProvider->query->andWhere(['groupby' => 'sum_by_currency'])->all(),
                        ]);
                  },
                ],
            ],
        ]);
    }


    private function getTypesAndGroups(): array
    {
        return $this->billTypesProvider->getGroupedList();
    }

    private function getClientTypes(): array
    {
        return $this->getRefs('type,client', 'hipanel:client');
    }
}
