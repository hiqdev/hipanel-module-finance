<?php

/** @var integer $id */
/** @var array $statistic */
?>

<div class="table-responsive costprice-table">
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
            <tr>
                <td><?= $key?></td>
                <?php foreach ($data as $status => $value) : ?>
                    <?php if ($status !== 'name') : ?>
                    <tr>
                        <?php if (is_array($value)) {
                            $result = (!empty($value['date'])) ? $value['date'] : 0;
                        } else {
                            $result = $value;
                        }
                        ?>
                        <td><?= $status ?></td>
                        <td><?= $result ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
