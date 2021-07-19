<?php

declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\models\Requisite;
use hipanel\modules\finance\helpers\BillImportFromFileHelper;
use yii\base\Model;
use Yii;

class BillImportFromFileForm extends Model
{
    public $file;

    public $type;

    public $requisite_id;

    public function rules()
    {
        return [
            [['file', 'requisite_id'], 'required'],
            ['requisite_id', 'integer'],
            ['file', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => ['csv'], 'maxSize' => 1 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('hipanel:finance', 'File from the payment system'),
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
        ];
    }

    public function getTypes(): array
    {
        return array_keys((new BillImportFromFileHelper())->getRequisitesTypes());
    }
}
