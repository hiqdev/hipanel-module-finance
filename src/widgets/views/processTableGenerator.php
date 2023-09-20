<?php

/** @var integer $id */
/** @var array $statistic */
?>

<div class="table-responsive">
    <table id="<?= $id ?>" class="table no-margin">
        <thead>
        <tr>
            <th><?= Yii::t('hipanel:document', 'Process') ?></th>
            <th><?= Yii::t('hipanel:document', 'Status') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($statistic['mask'])) : ?>
            <?php foreach ($statistic['mask'] as $key => $data) : ?>
                <?php
                    $result = json_decode($data, true);
                    foreach ($result as $processStatus => $value) {
                        $printData =  ($processStatus === 'in_progress') ? $processStatus . ': ' . $value : $processStatus;
                    }
                ?>
                <tr>
                    <td><?= explode(':', $key)[2] ?></td>
                    <td><?= $printData ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
