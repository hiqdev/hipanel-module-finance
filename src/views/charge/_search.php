<?php

/**
 * @var \hipanel\widgets\AdvancedSearch $search
 */

use hipanel\modules\finance\widgets\BillTypeFilter;

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('ftype')->widget(BillTypeFilter::class) ?>
</div>

<!--<div class="col-md-4 col-sm-6 col-xs-12">-->
<!--    --><?//= $search->field('no_ilike') ?>
<!--</div>-->
