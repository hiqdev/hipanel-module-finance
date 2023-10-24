<?php

/** @var integer $id */
/** @var array $statistic */


$normalizeDate = static function (?array $date): ?string {
    if (isset($date['date'])) {
        return (new DateTime($date['date'], new DateTimeZone("UTC")))->format('U.u');
    }

    return null;
};

?>

<?php foreach ($statistic as $name => $data) : ?>
    <div class="progress-group">
        <span class="progress-text" style="text-transform: uppercase;"><?= $name ?></span>
        <span class="progress-number"><b><?= $data['count'] ?></b>/<?= $data['total'] ?></span>
        <div class="progress-description text-muted">
            <ol class="breadcrumb" style="margin: 0; padding: 0; background-color: transparent;">
                <?php if ($data['startedAt']) : ?>
                    <li>
                        <?= Yii::t('hipanel:finance',
                            'Started at: {0,date,yyyy-mm-dd HH:mm}',
                            [$normalizeDate($data['startedAt'])]) ?>
                    </li>
                <?php endif ?>
                <?php if ($data['updatedAt']) : ?>
                    <li>
                        <?= Yii::t('hipanel:finance',
                            'Update at: {0,date,yyyy-mm-dd HH:mm}',
                            [$normalizeDate($data['updatedAt'])]) ?>
                    </li>
                <?php endif ?>
                <?php if ($data['status']) : ?>
                    <li class="active">
                        <?= Yii::t('hipanel:finance', 'Status: {0}', [$data['status']]) ?>
                    </li>
                <?php endif ?>
            </ol>
        </div>
        <div class="progress sm">
            <div class="progress-bar progress-bar-green"
                 style="width: <?= $data['total'] > 0 ? number_format($data['count'] / $data['total'] * 100) : 0 ?>%"></div>
        </div>
    </div>
<?php endforeach ?>
