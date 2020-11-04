<?php

namespace hipanel\modules\finance\cart;

use hipanel\modules\finance\models\CalculableModelInterface;
use \yii\base\Widget;

/**
 * Interface RelatedPositionInterface represent a related position can be bought along with the main (root)
 * position in the shopping cart
 */
interface RelatedPositionInterface
{
    public function getWidget(): Widget;

    public function createRelatedPosition(): CalculableModelInterface;

    public function render(): string;
}
