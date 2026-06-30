<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Purse;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use Exception;

/**
 * Saves an on-demand document for a single purse group.
 * Receives purse_id + charge_ids[] + type (+ optional period) from the Vue confirmation form.
 * Calls Purse::perform('generate-and-save-on-demand-document') and returns {link_to_document}.
 */
class SaveOnDemandDocumentAction extends Action
{
    private Session $session;

    public function __construct($id, Controller $controller, Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->session = $session;
    }

    public function run(): mixed
    {
        $this->controller->response->format = Response::FORMAT_JSON;

        try {
            $purseId   = (int) $this->controller->request->post('purse_id');
            $chargeIds = (array) $this->controller->request->post('charge_ids', []);
            $type      = (string) $this->controller->request->post('type', '');
            $date      = $this->controller->request->post('date') ?: null;
            $location  = $this->controller->request->post('location') ?: null;

            if (!$purseId) {
                throw new BadRequestHttpException('purse_id is required');
            }
            if (empty($chargeIds)) {
                throw new BadRequestHttpException('charge_ids are required');
            }
            if ($type === '') {
                throw new BadRequestHttpException('type is required');
            }

            $params = [
                'id'         => $purseId,
                'charge_ids' => array_values(array_filter(array_map('intval', $chargeIds))),
                'type'       => $type,
            ];
            if ($date !== null) {
                $params['date'] = $date;
            }
            if ($location !== null) {
                $params['location'] = $location;
            }

            $response = Purse::perform('generate-and-save-on-demand-document', $params);

            if (!empty($response['id'])) {
                $response['link_to_document'] = Url::to(['@document/view', 'id' => $response['id']]);
            }

            return $response;
        } catch (Exception $e) {
            return ['errorMessage' => $e->getMessage()];
        }
    }
}
