<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Price;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class PriceFields extends Widget
{
    public Price $model;
    public ActiveForm $form;
    public int $index;
    public array $currencyTypes = [];

    public function run()
    {
        return $this->render('priceFields', [
            'model' => $this->model,
            'form' => $this->form,
            'index' => $this->index,
            'currencyTypes' => $this->currencyTypes,
        ]);
    }
}
