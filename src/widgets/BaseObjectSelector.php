<?php

namespace hipanel\modules\finance\widgets;

use yii\bootstrap\Html;
use yii\bootstrap\InputWidget;

/**
 * Class BaseObjectSelector
 */
class BaseObjectSelector extends InputWidget
{
    public $object_name_attribute;

    public function init()
    {
        parent::init();
        $this->options['class'] = 'form-control';
        $this->options['readonly'] = true;
        $this->options['disabled'] = true;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // todo: Make object type dropdown field and object dropdown field
        $items = $this->model->isNewRecord ? [] : [$this->model->object_id => $this->model->{$this->object_name_attribute}];

        return Html::activeDropDownList($this->model, $this->attribute, $items, $this->options);
    }
}

