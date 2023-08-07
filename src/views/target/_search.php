<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\widgets\combo\TariffCombo;
use hipanel\widgets\AdvancedSearch;
use hipanel\widgets\TagsInput;

/**
 * @var $search AdvancedSearch
 */

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type')->dropDownList($search->model->types, ['prompt' => $search->model->getAttributeLabel('type')]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client_id')->widget(ClientCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('tariff_id')->widget(TariffCombo::class) ?>
</div>

<?php if (Yii::$app->user->can('plan.update')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('tags')->widget(TagsInput::class) ?>
    </div>
<?php endif ?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('show_deleted', ['options' => ['class' => 'form-group checkbox']])->checkbox() ?>
</div>
