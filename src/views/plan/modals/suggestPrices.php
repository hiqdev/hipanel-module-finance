<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\PriceSuggestionRequestForm;
use hipanel\modules\finance\widgets\combo\target\SwitchLicenceCombo;
use hipanel\modules\finance\widgets\combo\TargetCombo;
use hipanel\modules\finance\widgets\combo\TemplatePlanCombo;
use hipanel\modules\server\widgets\combo\HubCombo;
use hipanel\modules\server\widgets\combo\ServerCombo;
use hiqdev\combo\StaticCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Plan $plan
 * @var PriceSuggestionRequestForm $model
 * @var bool $withSwitchLicense
 */
?>

<?php $form = ActiveForm::begin(['id' => 'create-prices', 'action' => ['@price/suggest'], 'method' => 'GET']) ?>

<fieldset>

<?= $form->field($model, 'plan_id')->hiddenInput()->label(false) ?>

<?php
// TODO: think about splitting to multiple files, if files becomes monstrous.
// $this->render("../{$model->type}/suggestPricesModal", compact('form', 'model', 'plan'));
?>

<?php if (in_array($plan->type, [
    Plan::TYPE_SERVER,
    Plan::TYPE_PRIVATE_CLOUD,
], true)): ?>
    <?php if ($model->isObjectPredefined()) : ?>
        <?= $form->field($model, 'object_id')->hiddenInput()->label(false) ?>
    <?php else : ?>
        <?= $form->field($model, 'object_id')->widget(ServerCombo::class, ['primaryFilter' => 'name_like']) ?>
    <?php endif ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
        'object_input_type' => $model->isObjectPredefined() ? null : 'server/server',
    ]) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'default' => Yii::t('hipanel.finance.suggestionTypes', 'default'),
            'services' => Yii::t('hipanel.finance.suggestionTypes', 'services'),
            'parts' => Yii::t('hipanel.finance.suggestionTypes', 'parts'),
        ],
    ]) ?>
<?php elseif (in_array($plan->type, [
    Plan::TYPE_VPS,
    Plan::TYPE_SNAPSHOT,
    Plan::TYPE_VOLUME,
    Plan::TYPE_STORAGE,
    Plan::TYPE_PRIVATE_CLOUD_BACKUP,
    Plan::TYPE_VCDN,
    Plan::TYPE_VIDECDN,
    Plan::TYPE_MANAGED_KUBERNETES_CLUSTER,
    Plan::TYPE_LOAD_BALANCER,
], true)) : ?>
    <?php if ($model->isObjectPredefined()) : ?>
        <?= $form->field($model, 'object_id')->hiddenInput()->label(false) ?>
    <?php else : ?>
        <?= $form->field($model, 'object_id')->widget(TargetCombo::class) ?>
    <?php endif ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
        'object_input_type' => $model->isObjectPredefined() ? null : 'target/name',
    ]) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => ['default' => Yii::t('hipanel.finance.suggestionTypes', 'default')],
    ]) ?>
<?php elseif ($plan->type === Plan::TYPE_SWITCH): ?>
    <?php if ($model->isObjectPredefined()) : ?>
        <?= $form->field($model, 'object_id')->hiddenInput()->label(false) ?>
    <?php else : ?>
        <?php $this->registerJs(/** @lang JavaScript */ ';(() => {
          $("#switch-object a").one("click", function (evt) {
            const url = ["suggest-prices-modal?id=" + ' . $plan->id . '];
            if (evt.currentTarget.getAttribute("href") === "#switch_license") {
              url.push("switch_license");
            }
            $("#create-prices").css({ opacity: "0.5" });
            $("#create-prices fieldset").prop("disabled", true);
            $.get(url.join("&"), function( data ) {
              $( "#create-prices-modal .modal-body" ).html( data );
            });
          });
        })();') ?>
        <div id="switch-object">
          <ul class="nav nav-tabs" role="tablist">
            <li class="<?= !$withSwitchLicense ? 'active' : '' ?>">
                <a href="#switch" role="tab" data-toggle="tab"><?= Yii::t('hipanel:finance', 'Switch') ?></a>
            </li>
            <li class="<?= $withSwitchLicense ? 'active' : '' ?>">
                <a href="#switch_license" role="tab" data-toggle="tab"><?= Yii::t('hipanel:finance', 'Switch Licence') ?></a>
            </li>
          </ul>
          <div class="tab-content" style="margin-top: 2rem;">
              <?php if ($withSwitchLicense) : ?>
                  <div role="tabpanel" class="tab-pane active" id="switch_license">
                      <?= $form->field($model, 'object_id')->widget(SwitchLicenceCombo::class)->label(Yii::t('hipanel:finance',
                          'Switch Licence Target')) ?>
                      <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
                          'plan_id' => $plan->id,
                          'object_input_type' => $model->isObjectPredefined() ? null : 'target/name',
                      ]) ?>
                  </div>
              <?php else : ?>
                  <div role="tabpanel" class="tab-pane active" id="switch">
                      <?= $form->field($model, 'object_id')->widget(HubCombo::class)->label(Yii::t('hipanel:finance', 'Switch object')) ?>
                      <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
                          'plan_id' => $plan->id,
                          'object_input_type' => $model->isObjectPredefined() ? null : 'server/hub',
                      ]) ?>
                  </div>
              <?php endif ?>
          </div>
        </div>
    <?php endif ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'default' => Yii::t('hipanel.finance.suggestionTypes', 'default'),
            'services' => Yii::t('hipanel.finance.suggestionTypes', 'services'),
            'parts' => Yii::t('hipanel.finance.suggestionTypes', 'parts'),
        ],
    ]) ?>
<?php elseif ($plan->type === Plan::TYPE_TEMPLATE): ?>
    <?= $form->field($model, 'object_id')->hiddenInput(['value' => $model->plan_id])->label(false) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'model_groups' => Yii::t('hipanel.finance.suggestionTypes', 'model_groups'),
            'dedicated_server' => Yii::t('hipanel.finance.suggestionTypes', 'dedicated_server'),
            'switch' => Yii::t('hipanel.finance.suggestionTypes', 'switch'),
            'v_cdn' => Yii::t('hipanel.finance.suggestionTypes', 'v_cdn'),
            'p_cdn' => Yii::t('hipanel.finance.suggestionTypes', 'p_cdn'),
            'anycastcdn' => Yii::t('hipanel.finance.suggestionTypes', 'anycast_cdn'),
            'referral' => Yii::t('hipanel.finance.suggestionTypes', 'referral'),
            'vps' => Yii::t('hipanel.finance.suggestionTypes', 'vps'),
            'snapshot' => Yii::t('hipanel.finance.suggestionTypes', 'snapshot'),
            'volume' => Yii::t('hipanel.finance.suggestionTypes', 'volume'),
            'storage' => Yii::t('hipanel.finance.suggestionTypes', 'storage'),
            'private_cloud_backup' => Yii::t('hipanel.finance.suggestionTypes', 'private_cloud_backup'),
            'private_cloud' => Yii::t('hipanel.finance.suggestionTypes', 'private_cloud'),
            'managed_kubernetes_cluster' => Yii::t('hipanel.finance.suggestionTypes', 'managed_kubernetes_cluster'),
            'load_balancer' => Yii::t('hipanel.finance.suggestionTypes', 'load_balancer'),
            'calculator_public_cloud' => Yii::t('hipanel.finance.suggestionTypes', 'calculator_public_cloud'),
            'calculator_private_cloud' => Yii::t('hipanel.finance.suggestionTypes', 'calculator_private_cloud'),
            'calculator_storage' => Yii::t('hipanel.finance.suggestionTypes', 'calculator_storage'),
        ],
    ]) ?>
<?php elseif (in_array($plan->type, [Plan::TYPE_PCDN, Plan::TYPE_ANYCASTCDN, Plan::TYPE_VIDECDN], true)): ?>
    <?php if ($model->isObjectPredefined()) : ?>
        <?= $form->field($model, 'object_id')->hiddenInput()->label(false) ?>
    <?php else : ?>
        <?= $form->field($model, 'object_id')->widget(ServerCombo::class, [
            'primaryFilter' => 'name_like',
            /// XXX Looks like managers need CDN tariffs to be applicable to any servers
            /// 'filter' => ['type' => ['format' => $plan->type === Plan::TYPE_PCDN ? 'cdnpix' : 'cdn']],
        ]) ?>
    <?php endif ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
        'object_input_type' => $model->isObjectPredefined() ? null : 'server/server',
    ]) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'default' => Yii::t('hipanel.finance.suggestionTypes', 'default'),
        ],
    ]) ?>
<?php elseif (in_array($plan->type, [Plan::TYPE_CERTIFICATE, Plan::TYPE_DOMAIN], true)): ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
    ]) ?>
    <?php $form->action = ['@plan/create-prices', 'id' => $plan->id] ?>
<?php elseif ($plan->type === Plan::TYPE_HARDWARE): ?>
    <?= $form->field($model, 'object_id')->widget(ClientCombo::class) ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, ['plan_id' => $plan->id]) ?>
<?php elseif ($plan->type === Plan::TYPE_REFERRAL): ?>
    <?= Html::activeHiddenInput($model, 'type', ['value' => Plan::TYPE_REFERRAL]) ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, ['plan_id' => $plan->id]) ?>
<?php elseif ($plan->type === Plan::TYPE_CALCULATOR): ?>
    <?= Html::activeHiddenInput($model, 'type') ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, ['plan_id' => $plan->id]) ?>
<?php else: ?>
    <p class="text-center bg-warning"
       style="padding: 1rem;"><?= Yii::t('hipanel.finance.plan', 'This plan doesn\'t support detailed prices') ?></p>
<?php endif ?>

<?= Html::submitButton(Yii::t('hipanel.finance.price', 'Proceed to creation'), [
    'class' => 'btn btn-block btn-success',
]) ?>
</fieldset>

<?php ActiveForm::end() ?>
