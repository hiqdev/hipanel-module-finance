<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\forms\PrepareOnDemandDocumentForm;
use hipanel\modules\finance\widgets\GenerateOnDemandDocumentWidget;

/** @var PrepareOnDemandDocumentForm $model */
/** @var array $chargeGroups */
/** @var \yii\web\View $this */

echo GenerateOnDemandDocumentWidget::widget([
    'model'        => $model,
    'chargeGroups' => $chargeGroups,
    'formAction'   => Url::to(['@charge/generate-on-demand-document']),
    'saveUrl'      => Url::to(['@charge/save-on-demand-document']),
    'previewUrl'   => Url::to(['@charge/preview-on-demand-document']),
]);
