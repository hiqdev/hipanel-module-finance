<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\actions\Action;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Sale;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use Exception;
use RuntimeException;

final class ChangeBuyerAction extends Action
{
    public string $view = 'modals/change-buyer';
    private readonly Session $session;

    public function __construct(
        $id,
        Controller $controller,
        Session $session,
        array $config = []
    )
    {
        parent::__construct($id, $controller, $config);
        $this->session = $session;
    }

    public function run(): Response|string
    {
        try {
            $model = new Sale(['scenario' => 'change-buyer']);
            $selection = $this->controller->request->post('selection');
            $formData = $this->controller->request->post('Sale');
            if (empty($selection) && !empty($formData)) {
                $payload = [];
                $sales = ArrayHelper::index($this->getSales($formData), 'id');
                foreach ($formData as $datum) {
                    if (isset($datum['id'])) {
                        foreach ($datum['id'] as $id) {
                            $sale = $sales[$id];
                            $payload[$id] = $this->fillWith($sale, $datum);
                        }
                    } else {
                        foreach ($datum as $row) {
                            $sale = $sales[$row['id']];
                            $payload[$row['id']] = $this->fillWith($sale, $row);
                        }
                    }
                }
                try {
                    Sale::batchPerform('change-buyer', $payload);
                } catch (Exception $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());

                    return $this->controller->redirect(Url::to(['@sale/index']));
                }

                $this->session->setFlash('success', Yii::t('hipanel:finance', 'Object\'s buyer has been changed'));

                return $this->controller->redirect(Url::to(['@sale/index']));
            }
            if (empty($selection)) {
                throw new RuntimeException('No sales selected');
            }
            $sales = $this->getSales($selection);

            return $this->controller->renderAjax($this->view, [
                'model' => $model,
                'salesByTariffType' => ArrayHelper::index($sales, null, 'tariff_type'),

            ]);
        } catch (Exception $e) {
            $this->session->setFlash('error', $e->getMessage());

            return $this->controller->redirect($this->controller->request->referrer);
        }
    }

    private function fillWith(Sale $sale, array $datum): array
    {
        return [
            'object_id' => $sale->object_id,
            'tariff_id' => $datum['tariff_id'],
            'buyer_id' => $datum['buyer_id'],
            'time' => $datum['time'],
            'object_type' => $sale->object_type,
            'object_name' => $sale->object,
        ];
    }

    private function getSales(array $data): array
    {
        $ids = [];
        if (ArrayHelper::isIndexed($data)) {
            $ids = array_values($data);
        } else if (1) {
            foreach ($data as $datum) {
                if (isset($datum['id']) && !empty($datum['id'])) {
                    $ids = array_merge($ids, $datum['id']);
                } else {
                    foreach ($datum as $model) {
                        if (isset($model['id']) && !empty($model['id'])) {
                            $ids[] = $model['id'];
                        }
                    }
                }
            }
        }

        return Sale::find()->where(['id_in' => array_unique($ids)])->limit(-1)->all();
    }
}
