<?php
declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillFromPricesForm;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use yii\web\BadRequestHttpException;

class CreateFromPricesAction extends BillManagementAction
{
    public string $billObjectClass = 'device';

    public function run(): string
    {
        $model = new BillFromPricesForm();
        $priceIds = $this->controller->request->post('selection', []);
        [$billTypes, $billGroupLabels] = $this->billTypesProvider->getGroupedList();
        $prices = Price::find()->select(['*', 'main_object_id'])->joinWith(['object'])->where(['id_in' => $priceIds])->limit(-1)->all();
        $pricesByObjectId = ArrayHelper::index($prices, null, 'main_object_id');
        if ($this->controller->request->isAjax) {
            return $this->controller->renderAjax('modals/create-from-prices', [
                'model' => $model,
                'billTypes' => $billTypes,
                'billGroupLabels' => $billGroupLabels,
                'prices' => $prices,
            ]);
        }
        if ($this->controller->request->isPost && !empty($priceIds)) {
            $model->load($this->controller->request->post());
            $sales = Sale::find()->where(['object_ids' => array_keys($pricesByObjectId), 'tariff_id' => reset($prices)->plan_id])->limit(-1)->all();
            $indexedSales = ArrayHelper::index($sales, 'object_id');
            $bills = [];
            foreach ($pricesByObjectId as $mainObjectId => $prices) {
                $bills[] = $model->createBillWithCharges($indexedSales[$mainObjectId]->buyer_id, $mainObjectId, $this->billObjectClass, $prices);
            }
            $billForms = BillForm::createMultipleFromBills($bills, $this->scenario);
            $this->createCollection();
            $this->collection->set($billForms);

            return $this->controller->render('create', [
                'models' => $this->collection->getModels(),
                'billTypes' => $billTypes,
                'billGroupLabels' => $billGroupLabels,
            ]);
        }
        
        throw new BadRequestHttpException('unknown error while creating invoice');
    }
}
