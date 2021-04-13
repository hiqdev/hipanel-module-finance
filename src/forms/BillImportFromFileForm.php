<?php

namespace hipanel\modules\finance\forms;

use Yii;
use yii\base\Model;

class BillImportFromFileForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => ['csv'], 'maxSize' => 1 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('hipanel:finance', 'File from the payment system')];
    }
}
