<?php

use hipanel\modules\finance\widgets\MonthRangePicker;
use hipanel\widgets\AdvancedSearch;

/**
 * @var $search AdvancedSearch
 */

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('class')->dropDownList($search->model->classes) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= MonthRangePicker::widget(['model' => $search->model]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name') ?>
</div>
