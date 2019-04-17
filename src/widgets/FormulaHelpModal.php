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

use hipanel\modules\finance\providers\FormulaExamplesProvider;
use yii\base\Widget;
use yii\web\View;

/**
 * Class FormulaHelpModal.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FormulaHelpModal extends Widget
{
    /**
     * @var bool
     */
    private $wasRendered = false;

    public function run()
    {
        if (!$this->wasRendered) {
            $this->view->registerJs(<<<JS
$('#{$this->getId()}').data('onPasteRequested', function() {
    let template = $(this).siblings('kbd').text();
    let input = $(this).closest('.modal').modal('hide').data('input');
    let editor = input.data('ace-editor');
    let session = editor.session;

    let text = template;
    if (session.getLength() > 1 || session.getLine(0).length > 0) {
        text = '\\n' + text;
    }

    session.insert({
       row: session.getLength(),
       column: 0
    }, text)
    editor.selection.moveCursorToPosition({row: session.getLength(), column: 0});
    editor.selection.selectLine();
    setTimeout(() => editor.focus(), 500);
});
JS
            );
            $this->view->on(View::EVENT_END_BODY, function () {
                echo $this->render('formulaHelpModal');
            });
            $this->wasRendered = true;
        }

        return $this->getId();
    }

    public function formulaExamplesProvider(): FormulaExamplesProvider
    {
        return new FormulaExamplesProvider();
    }
}
