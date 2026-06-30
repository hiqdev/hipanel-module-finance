<?php

declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use Yii;
use yii\base\Model;

final class PrepareOnDemandDocumentForm extends Model
{
    /** Comma-separated bill or charge IDs depending on context */
    public ?string $ids = null;
    public string $type = 'invoice';
    public ?string $date = null;

    public function attributeLabels(): array
    {
        return [
            'type' => Yii::t('hipanel:finance', 'Document type'),
            'date' => Yii::t('hipanel:finance', 'Date'),
        ];
    }

    public function rules(): array
    {
        return [
            [['ids', 'type'], 'required'],
            [['ids', 'type', 'date'], 'string'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }
}
