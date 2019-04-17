<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use hipanel\assets\AceEditorAsset;
use hipanel\modules\finance\models\Price;
use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class FormulaInput.
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
        $this->registerAceEditor();
    }

    public function run()
    {
        $this->registerClientScript();

        $this->field->label(
            $this->model->getAttributeLabel('formula') . ' '
            . Html::button('', [
                'class' => 'fa fa-question-circle text-info formula-help-modal',
                'data-toggle' => 'modal',
                'data-target' => $this->getHelpModalSelector(),
                'tabindex' => -1,
                'onClick' => new \yii\web\JsExpression(<<<JS
$($(this).data('target')).data('input', $(this).closest('.form-group').find('.formula-input'))
JS
                ),
            ]));

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

    private function registerAceEditor()
    {
        AceEditorAsset::register($this->view);
        $this->view->registerCss('.form-group .ace_editor { min-height: 34px; padding: 0; }');
        $this->view->registerJs(<<<'JS'
$('.formula-input').each(function () {
    var JavaScriptMode = ace.require("ace/mode/javascript").Mode;

    var shadow = $(this).clone();
    shadow.hide().insertAfter($(this));

    var editor = ace.edit($(this).attr('id'));
    shadow.data('ace-editor', editor);

    editor.session.setMode(new JavaScriptMode());
    editor.session.$worker.call("changeOptions", [{es3: true, asi: true}]);
    editor.setOptions({
        maxLines: 15,
        wrap: true,
        autoScrollEditorIntoView: true,
        highlightActiveLine: false,
        highlightGutterLine: false,
    });
    editor.renderer.$cursorLayer.element.style.opacity = 0;

    editor.on('blur', function () {
        editor.setOptions({
            highlightActiveLine: false,
            highlightGutterLine: false
        });
        editor.renderer.$cursorLayer.element.style.opacity = 0;
    });
    editor.on('focus', function () {
        editor.setOptions({
            highlightActiveLine: true,
            highlightGutterLine: true
        });
        editor.renderer.$cursorLayer.element.style.opacity = 1;
    });
    editor.on('change', function () {
        shadow.val(editor.getValue());
    });
});
JS
        );
    }
}
