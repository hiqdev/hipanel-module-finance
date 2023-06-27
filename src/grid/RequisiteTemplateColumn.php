<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\DataColumn;
use hipanel\helpers\StringHelper;
use hipanel\modules\finance\forms\GenerateInvoiceForm;
use hipanel\modules\finance\models\Requisite;
use Yii;
use yii\helpers\Html;

class RequisiteTemplateColumn extends DataColumn
{
    public $format = 'raw';
    public $attribute = 'templates';

    public function init()
    {
        parent::init();
        $this->label = Yii::t('hipanel:finance', 'Templates');
        $this->visible = Yii::$app->user->can('document.read');
    }

    public function getDataCellValue($model, $key, $index)
    {
        /** @var Requisite $model */
        $documents = array_filter($model->getDocumentsByTypes(), fn($document) => !empty($document->templateid));
        $html[] = Html::beginTag('dl');
        $user = Yii::$app->user;
        foreach ($documents as $document) {
            $form = new GenerateInvoiceForm();
            $html[] = Html::tag('dt', StringHelper::mb_ucfirst($document->title));
            $templateName = implode(" ", [
                $document->template_name ?? $document->templateid,
                $user->can('test.beta') ? "<span class='text-monospace text-muted'>$document->templateid</span>" : "",
            ]);
            $html[] = Html::tag('dd',
                Html::a(Yii::t('hipanel', $templateName), '@document/generate-document', [
                    'target' => '_blank',
                    'data' => [
                        'method' => 'POST',
                        'params' => [
                            "{$form->formName()}[id]" => $document->id,
                        ],
                    ],
                ])
            );
        }
        $html[] = Html::endTag('dl');

        return implode('', $html);
    }
}
