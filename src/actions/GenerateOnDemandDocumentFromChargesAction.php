<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\modules\finance\forms\PrepareOnDemandDocumentForm;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\models\Purse;
use Yii;
use yii\base\Action;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use Exception;

/**
 * Charge flow: user selects individual charges from the grid.
 * Non-AJAX: loads and displays selected charges grouped by client+currency.
 * AJAX: sends charge_ids[] directly to pursePrepareOnDemandDocument.
 */
class GenerateOnDemandDocumentFromChargesAction extends Action
{
    private Session $session;

    public function __construct($id, Controller $controller, Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->session = $session;
    }

    public function run(): mixed
    {
        if ($this->controller->request->isAjax) {
            $this->controller->response->format = Response::FORMAT_JSON;
            try {
                $chargeIds = array_values(array_filter(array_map(
                    'intval',
                    (array) $this->controller->request->post('charge_ids', [])
                )));
                $type = (string) $this->controller->request->post('type', '');
                $date = $this->controller->request->post('date') ?: null;

                return Purse::perform('prepare-on-demand-document', [
                    'charge_ids' => $chargeIds,
                    'type'       => $type,
                    'date'       => $date,
                ]);
            } catch (Exception $e) {
                return ['errorMessage' => $e->getMessage()];
            }
        }

        $chargeIds = $this->controller->request->post('selection', []);
        if (empty($chargeIds)) {
            $this->session->setFlash('error', Yii::t('hipanel:finance', 'No charges selected'));

            return $this->controller->redirect(['index']);
        }

        $charges = Charge::find()->where(['ids' => $chargeIds, 'limit' => 'ALL'])->withIncludedInDocuments()->all();

        $chargeGroups = [];
        foreach ($charges as $charge) {
            $key = ($charge->client ?? '?') . ':' . strtoupper($charge->currency ?? '');
            $chargeGroups[$key]['client']   ??= $charge->client ?? '?';
            $chargeGroups[$key]['currency'] ??= strtoupper($charge->currency ?? '');
            $chargeGroups[$key]['charges'][]  = $charge;
        }

        $form      = new PrepareOnDemandDocumentForm();
        $form->ids = implode(',', $chargeIds);

        return $this->controller->render('@hipanel/modules/finance/views/charge/generate-on-demand-document', [
            'model'        => $form,
            'chargeGroups' => $chargeGroups,
        ]);
    }
}
