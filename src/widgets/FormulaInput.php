<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Price;
use hiqdev\assets\autosize\AutosizeAsset;
use Yii;
use yii\helpers\BaseHtml;
use yii\web\NotAcceptableHttpException;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class FormulaInput
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FormulaInput extends InputWidget
{
    /** @var Price */
    public $model;

    /**
     * @var string
     */
    private $helpModalSelector;

    public function registerClientScript()
    {
        $this->registerHelpModal();
        AutosizeAsset::register($this->view);
        $this->view->registerJs("autosize($('.formula-input'));");
    }

    public function run()
    {
        $this->registerClientScript();

        return $this->render('formulaInput');
    }

    private function registerHelpModal(): void
    {
        $help = Yii::createObject(FormulaHelpModal::class);
        $this->helpModalSelector = '#' . $help->run();
    }

    public function formulaLinesCount()
    {
        return count($this->model->formulaLines());
    }

    /**
     * @return string
     */
    public function getHelpModalSelector(): string
    {
        return $this->helpModalSelector;
    }
}
