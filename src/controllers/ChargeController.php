<?php declare(strict_types=1);

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
use hipanel\actions\RedirectAction;
use hipanel\actions\RenderAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\SwitchAction;
use hipanel\actions\VariantsAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\query\ChargeQuery;
use hipanel\modules\finance\providers\BillTypesProvider;
use hipanel\modules\finance\widgets\ChargeFinanceSummaryTable;
use hipanel\widgets\DataProviderGridRenderer;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

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
                    'update' => 'bill.update',
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
                    $query
                        ->withCommonObject()
                        ->withLatestCommonObject()
                        ->withRootChargeType();
                },
                'data' => fn(RenderAction $action, array $data): array => [
                    'billTypesList' => $this->billTypesProvider->getTypes(),
                    'clientTypes' => $this->getClientTypes(),
                ],
                'responseVariants' => [
                    IndexAction::VARIANT_SUMMARY_RESPONSE => static function (VariantsAction $action): string {
                        $dataProvider = $action->parent->getDataProvider();
                        $defaultSummary = (new DataProviderGridRenderer($dataProvider))->renderSummary();

                        return $defaultSummary . ChargeFinanceSummaryTable::widget([
                                'currencies' => $action->controller->getCurrencyTypes(),
                                'allModels' => $dataProvider->query->andWhere(['groupby' => 'sum_by_currency', 'hide_child_charges' => true]
                                )->all(),
                            ]);
                    },
                ],
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel:finance', 'Charge(s) has been successfully updated'),
                'POST html' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function (RedirectAction $action) {
                            $billId = $action->controller->request->get('bill_id');
                            if ($billId) {
                                return ['@bill/view', 'id' => $billId];
                            }

                            return ['@bill/index'];
                        },
                    ],
                ],
                'collectionLoader' => function (SwitchAction $action) {
                    $formName = $action->collection->getModel()->formName();
                    $data = $action->controller->request->post($formName);
                    $action->collection->load($data[0]);
                },
                'data' => function ($action, $data) {

                    $billIds = array_unique(array_values(ArrayHelper::getColumn($data['models'], 'bill_id')));

                    if (count($billIds) !== 1) {
                        throw new InvalidConfigException('Can not update multiple bills');
                    }

                    $billID = reset($billIds);

                    $bill = Bill::findOne($billID);
                    $chargesSum = array_sum(ArrayHelper::getColumn($data['models'], 'sum'));
                    $bill->sum -= $chargesSum * -1;

                    $data['bill'] = $bill;
                    $data['billTypesList'] = $this->billTypesProvider->getTypes();
                    $data['allowedTypes'] = [];

                    return $data;
                },
            ],
            'view' => [
                'class' => ViewAction::class,
                'GET html' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function ($action) {
                            $charge = $action->parent->collection->first;

                            return ['@bill/view', 'id' => $charge->bill_id, '#' => $charge->id];
                        },
                    ],
                ],
            ],
        ]);
    }

    private function getClientTypes(): array
    {
        return $this->getRefs('type,client', 'hipanel:client');
    }
}
