<?php

/**
 * @var array
 * @var \hipanel\widgets\AdvancedSearch $search
 */
use hipanel\modules\client\widgets\combo\ClientCombo;

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client_id')->widget(ClientCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('state')->dropDownList($states) ?>
</div>
