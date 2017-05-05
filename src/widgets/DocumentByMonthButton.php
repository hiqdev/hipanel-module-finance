<?php

namespace hipanel\modules\finance\widgets;

class DocumentByMonthButton extends \yii\base\Widget
{
    public $model;

    public $action;

    public $type;

    public $prepend;

    public $append;

    public $modalHeader;

    public $modalHeaderColor = '';

    public $buttonLabel;

    public function run()
    {
        $dt = new \DateTime();
        $this->model->month = $dt->format('Y-m'); // Set default value

        return $this->render('DocumentByMonthButton', [
            'model' => $this->model,
            'action' => $this->action,
            'type' => $this->type,
            'append' => $this->append,
            'prepend' => $this->prepend,
            'modalHeader' => $this->modalHeader,
            'modalHeaderColor' => $this->modalHeaderColor,
            'buttonLabel' => $this->buttonLabel,
            'dt' => $dt,
        ]);
    }
}
