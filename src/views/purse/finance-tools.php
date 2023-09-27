<?php
/** @var array $statisticByTypes */

use yii\bootstrap\ActiveForm;

$this->title = Yii::t('hipanel:finance', 'Finance tools');
$this->params['subtitle'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Documents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <div class="box box-widget">
            <div class="box-header with-border">
                <?php $form = ActiveForm::begin() ?>
                <div class="box-tools pull-left">
                    <button
                        type="submit"
                        class="btn btn-default btn-box-tool"
                        formmethod="GET"
                        formaction="/finance/purse/generate-all"
                    >
                        <?= Yii::t('hipanel:document', 'Generate documents') ?>
                    </button>
                    <button
                        type="submit"
                        class="btn btn-default btn-box-tool"
                        formmethod="GET"
                        formaction="/finance/purse/calculate-costprice"
                    >
                        <?= Yii::t('hipanel:finance', 'Calculate costprice') ?>
                    </button>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <div class="overlay" style="display: none;">
            </div>
        </div>
    </div>
</div>