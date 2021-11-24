<?php

use hipanel\modules\finance\models\TariffProfile;
use yii\helpers\Html;

/** @var string $client */
/** @var int $client_id */
/** @var TariffProfile $model */

$this->title = Yii::t('hipanel.finance.tariffprofile', 'Update seller profile: {0}', $client);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel.finance.tariffprofile', 'Tariff profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->getTitle()), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('model', 'client_id', 'client')) ?>
