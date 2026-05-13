<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\helpers\ArrayHelper;
use hipanel\models\Ref;
use hipanel\modules\finance\models\InstallmentPlan;
use hipanel\modules\finance\providers\BillTypesProvider;
use Yii;
use yii\base\Action;

class InstallmentPlanCreateBillAction extends Action
{
    public function run()
    {
        $ids       = Yii::$app->request->post('selection', []);
        $confirmed = (bool) Yii::$app->request->post('confirmed', false);

        $plans = !empty($ids)
            ? InstallmentPlan::find()
                ->where(['ids' => $ids, 'with_all_states' => 1])
                ->limit(-1)
                ->all()
            : [];

        $validPlans = array_values(array_filter(
            $plans,
            static fn(InstallmentPlan $plan): bool => in_array($plan->state, [
                InstallmentPlan::STATE_ONGOING,
                InstallmentPlan::STATE_INTERRUPTED,
            ], true) && $plan->part_id && $plan->left_sum > 0,
        ));

        if (empty($validPlans)) {
            return $this->controller->renderPartial('modals/_create-bill', ['groups' => [], 'validIds' => []]);
        }

        $groups = [];
        foreach ($validPlans as $plan) {
            $key = $plan->client_id . '|' . $plan->currency;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'client'    => $plan->client,
                    'client_id' => $plan->client_id,
                    'currency'  => $plan->currency,
                    'total_sum' => 0.0,
                    'count'     => 0,
                    'plans'     => [],
                ];
            }
            $groups[$key]['total_sum'] += (float) $plan->left_sum;
            $groups[$key]['count']++;
            $groups[$key]['plans'][] = $plan;
        }

        $validIds = array_map(static fn(InstallmentPlan $p): int => (int) $p->id, $validPlans);

        if (!$confirmed) {
            return $this->controller->renderPartial('modals/_create-bill', compact('groups', 'validIds'));
        }

        /** @var BillTypesProvider $provider */
        $provider = Yii::$container->get(BillTypesProvider::class);
        $provider->keepUnusedTypes();
        $typeIndex = ArrayHelper::map($provider->getTypes(), 'name', 'id');

        $billFType = 'other,hw_purchase';
        $billType = 'hw_purchase';
        $billTypeId = (string) ($typeIndex[$billFType] ?? $typeIndex[$billType] ?? '');
        $chargeTypeId = (string) ($typeIndex[$billType] ?? $typeIndex[$billFType] ?? '');
        $now = date('Y-m-d H:i:s');

        $billsData = [];
        foreach ($groups as $groupMeta) {
            $totalQuantity = 0;
            $charges = array_map(static function (InstallmentPlan $p) use ($now, $billType, $chargeTypeId, &$totalQuantity): array {
                $totalQuantity += 1;
                return [
                    'id'         => 'fake_id', // required for DynamicForm to display charges
                    'class'      => 'part',
                    'object_id'  => (string) $p->part_id,
                    'name'       => (string) $p->serialno,
                    'sum'        => number_format((float) $p->left_sum, 2, '.', ''),
                    'quantity'   => '1',
                    'unit'       => 'items',
                    'type'       => (string) $billType,
                    'type_id'    => (string) $chargeTypeId,
                    'time'       => (string) $now,
                    'label'      => (string) ($p->serialno . ($p->model ? ' / ' . $p->model : '')),
                ];
            }, $groupMeta['plans']);

            $billsData[] = [
                'attributes' => [
                    'client_id'  => (string) $groupMeta['client_id'],
                    'client'     => (string) $groupMeta['client'],
                    'currency'   => (string) $groupMeta['currency'],
                    'type'       => (string) $billType,
                    'ftype'      => (string) $billFType,
                    'type_id'    => (string) $billTypeId,
                    'sum'        => number_format(-1 * (float) $groupMeta['total_sum'], 2, '.', ''),
                    'quantity'   => (string) $totalQuantity,
                    'unit'       => 'items',
                    'time'       => (string) $now,
                    'label'      => Yii::t('hipanel:finance', 'Installment payment'),
                ],
                'charges' => $charges,
            ];
        }

        $preloadKey = Yii::$app->security->generateRandomString(16);
        Yii::$app->session->set('bill_preload_' . $preloadKey, $billsData);

        return $this->controller->redirect(['@bill/create', '_preload' => $preloadKey]);
    }
}
