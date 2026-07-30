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
 * Bill flow (redesigned):
 *   GET/POST (non-AJAX): loads bills with all their charges via billsSearch (no document filters),
 *     groups charges by client+currency, renders the selection form.
 *   POST (AJAX): receives selected charge_ids + type + date, forwards directly to
 *     pursePrepareOnDemandDocument and returns the builder-eligible charges grouped by purse.
 */
class GenerateOnDemandDocumentAction extends Action
{
    private Session $session;

    public function __construct($id, Controller $controller, Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->session = $session;
    }

    public function run(): mixed
    {
        // AJAX: forward selected charge_ids to the purse builder
        if ($this->controller->request->isAjax) {
            $this->controller->response->format = Response::FORMAT_JSON;
            try {
                $chargeIds = array_values(array_filter(array_map(
                    'intval',
                    (array) $this->controller->request->post('charge_ids', [])
                )));
                $type = (string) $this->controller->request->post('type', '');
                $date = $this->controller->request->post('date') ?: null;

                if (empty($chargeIds) || $type === '') {
                    return ['errorMessage' => Yii::t('hipanel:finance', 'No charges or type selected')];
                }

                return Purse::perform('prepare-on-demand-document', [
                    'charge_ids' => $chargeIds,
                    'type'       => $type,
                    'date'       => $date,
                ]);
            } catch (Exception $e) {
                return ['errorMessage' => $e->getMessage()];
            }
        }

        // Initial page load: fetch bills with charges (no document-related filters)
        $billIds = $this->controller->request->post('selection', []);
        if (empty($billIds)) {
            $this->session->setFlash('error', Yii::t('hipanel:finance', 'No bills selected'));

            return $this->controller->redirect(['index']);
        }

        $charges = Charge::find()->where(['bill_ids' => $billIds])->withIncludedInDocuments()->limit(-1)->all();

        $chargeGroups = [];
        foreach ($charges as $charge) {
            $key = ($charge->client ?? '?') . ':' . strtoupper($charge->currency ?? '');
            $chargeGroups[$key]['client']   ??= $charge->client ?? '?';
            $chargeGroups[$key]['currency'] ??= strtoupper($charge->currency ?? '');
            $chargeGroups[$key]['charges'][]  = $charge;
        }

        return $this->controller->render('@hipanel/modules/finance/views/bill/generate-on-demand-document', [
            'model'        => new PrepareOnDemandDocumentForm(),
            'chargeGroups' => $chargeGroups,
        ]);
    }
}
