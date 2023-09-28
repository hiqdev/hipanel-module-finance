<?php

use hipanel\modules\server\grid\ServerGridView;
use hipanel\modules\server\models\Server;
use yii\helpers\Html;

/** @var Server $mainObject */

$this->render('_device', ['mainObject' => $mainObject]);

