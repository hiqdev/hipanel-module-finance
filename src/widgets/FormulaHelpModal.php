<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\providers\FormulaExamplesProvider;
use yii\base\Widget;
use yii\web\View;

/**
 * Class FormulaHelpModal
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
