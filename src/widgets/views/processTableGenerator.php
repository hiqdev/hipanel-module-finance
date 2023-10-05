<?php

/** @var integer $id */
/** @var array $statistic */
?>

<?php foreach ($statistic as $name => $data) : ?>
    <div class="progress-group">
        <span class="progress-text" style="text-transform: uppercase;"><?= $name ?></span>
        <span class="progress-number"><b><?= $data['count'] ?></b>/<?= $data['total'] ?></span>
        <div class="progress-description text-muted">
            <ol class="breadcrumb" style="margin: 0; padding: 0; background-color: transparent;">
                <li><?= Yii::t('hipanel:finance', 'Started at: {0,time}', [$data['startedAt'] ?? 0]) ?></li>
                <li><?= Yii::t('hipanel:finance', 'Update at: {0,time}', [$data['updatedAt'] ?? 0]) ?></li>
                <li class="active"><?= Yii::t('hipanel:finance', 'Status: {0}', [$data['status']]) ?></li>
            </ol>
        </div>
        <div class="progress sm">
            <div class="progress-bar progress-bar-green"
                 style="width: <?= $data['total'] > 0 ? number_format($data['count'] / $data['total'] * 100) : 0 ?>%"></div>
        </div>
    </div>
<?php endforeach ?>
