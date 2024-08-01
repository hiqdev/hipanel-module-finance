<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Exception;
use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\Plan;
use yii\base\Action;
use yii\web\UnprocessableEntityHttpException;

class LinkParentPlanAction extends Action
{
    public $success;
    public $view;

    public function run(int $id, int $client_id, string $type)
    {
        $parentId = $this->controller->request->post('Plan')['id'] ?? NULL;
        if (!is_null($parentId)) {
            try {
                Plan::perform('link-parent', [
                    'id' => $id,
                    'parent_id' => $parentId,
                ]);
            } catch (Exception $e) {
                throw new UnprocessableEntityHttpException($e->getMessage(), 0, $e);
            }
        }
        $client = Client::findOne(['id' => $client_id]);
        $model = Plan::findOne($id);
        $parentPlans = Plan::find()
            ->where(['client_id' => $client->getAttribute('account_owner_id'), 'type' => $type])
            ->limit(-1)
            ->all();
        $parentPlansData = [];
        foreach ($parentPlans as $parentPlan) {
            $parentPlansData[$parentPlan->id] = $parentPlan->name;
        }
        return $this->controller->renderAjax($this->view, [
            'model' => $model,
            'parentData' => $parentPlansData
        ]);
    }
}
