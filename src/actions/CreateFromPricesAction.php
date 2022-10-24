<?php
declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Exception;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillFromPricesForm;
use hipanel\modules\finance\helpers\PriceChargesEstimator;
use hipanel\modules\finance\helpers\LightPriceChargesEstimator;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\providers\BillTypesProvider;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Session;
use RuntimeException;
use hiqdev\hiart\ResponseErrorException;

class CreateFromPricesAction extends BillManagementAction
{
    public string $billObjectClass = 'device';

    private Session $session;

    public function __construct($id, Controller $controller, BillTypesProvider $billTypesProvider, Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $billTypesProvider, $config);
        $this->session = $session;
    }

    public function run()
    {
        try {
            $form = new BillFromPricesForm();
            $priceIds = $this->controller->request->post('selection', []);
            if (empty($priceIds)) {
                throw new BadRequestHttpException('No prices selected');
            }
            [$billTypes, $billGroupLabels] = $this->billTypesProvider->getGroupedList();
            $billTypesList = $this->billTypesProvider->getTypesList();
            $prices = Price::find()->select(['*', 'main_object_id'])->joinWith(['object'])->withFormulaLines()->where(['id_in' => $priceIds])->limit(-1)->all();
            $pricesByObjectId = ArrayHelper::index($prices, null, 'main_object_id');
            if ($this->controller->request->isAjax) {
                return $this->controller->renderAjax('modals/create-from-prices', [
                    'model' => $form,
                    'billTypes' => $billTypes,
                    'billGroupLabels' => $billGroupLabels,
                    'billTypesList' => $billTypesList,
                    'prices' => $prices,
                ]);
            }
            if ($this->controller->request->isPost && $form->load($this->controller->request->post())) {
                $form->validate();
                if ($form->hasErrors()) {
                    $errors = $form->getFirstErrors();
                    throw  new RuntimeException(reset($errors));
                }
                $planId = reset($prices)->plan_id;
                $calculations = $this->getCalculations($planId, $form->time);
                $sales = Sale::find()->where(['object_ids' => array_keys($pricesByObjectId), 'tariff_id' => $planId])->limit(-1)->all();
                if (empty($sales)) {
                    throw new BadRequestHttpException('Apparently the details belonging to the object(s) have not been sold yet');
                }
                $indexedSales = ArrayHelper::index($sales, 'object_id');
                $bills = [];
                foreach ($pricesByObjectId as $mainObjectId => $prices) {
                    if (isset($indexedSales[$mainObjectId])) {
                        $bills[] = $form->createBillWithCharges($indexedSales[$mainObjectId]->buyer_id, $mainObjectId, $this->billObjectClass, $prices, $calculations);
                    }
                }
                if (empty($bills)) {
                    throw  new RuntimeException('Not a single payment has been generated');
                }
                $billForms = BillForm::createMultipleFromBills($bills, $this->scenario);
                $this->createCollection();
                $this->collection->set($billForms);

                return $this->controller->render('create', [
                    'models' => $this->collection->getModels(),
                    'billTypes' => $billTypes,
                    'billTypesList' => $billTypesList,
                    'billGroupLabels' => $billGroupLabels,
                ]);
            }
            throw new BadRequestHttpException('unknown error while creating invoice');
        } catch (Exception $e) {
            $this->session->setFlash('error', $e->getMessage());

            return $this->controller->goBack();
        }
    }

    private function getCalculations(int $planId, string $time): array
    {
        $times = [$time];
        try {
            $values = Plan::perform('calculate-values', [
                'id' => $planId,
                'times' => $times,
                'panel' => true,
            ]);

            $calculator = Yii::$container->get(LightPriceChargesEstimator::class, [$values]);
            $calculations = $calculator->calculateForPeriods($times);
        } catch (ResponseErrorException $e) {
            return [];
        }

        return reset($calculations)['targets'] ?? [];
    }
}
