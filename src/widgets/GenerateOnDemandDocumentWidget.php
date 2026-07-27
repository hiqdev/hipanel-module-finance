<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\assets\GenerateOnDemandDocumentAsset\GenerateOnDemandDocumentAsset;
use hipanel\modules\finance\forms\PrepareOnDemandDocumentForm;
use Yii;
use yii\base\Widget;

class GenerateOnDemandDocumentWidget extends Widget
{
    public PrepareOnDemandDocumentForm $model;

    /** @var array [key => ['client' => string, 'currency' => string, 'charges' => Charge[]]] */
    public array $chargeGroups = [];

    public string $formAction;
    public string $saveUrl;
    public string $previewUrl;

    public function run(): string
    {
        $this->view->title = Yii::t('hipanel:finance', 'Generate on-demand document');
        $this->view->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        GenerateOnDemandDocumentAsset::register($this->view);

        return $this->render('generate-on-demand-document', [
            'model'        => $this->model,
            'chargeGroups' => $this->chargeGroups,
            'formAction'   => $this->formAction,
            'saveUrl'      => $this->saveUrl,
            'previewUrl'   => $this->previewUrl,
        ]);
    }
}
