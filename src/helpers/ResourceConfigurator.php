<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\proxy\Resource;
use RuntimeException;
use Yii;

class ResourceConfigurator
{
    private string $gridClassName;

    private string $searchModelClassName;

    private string $modelClassName;

    private string $searchView;

    private string $toObjectUrl;

    private string $fullTypePrefix;

    private string $resourceModelClassName;

    private array $columns = [];

    private static self $configurator;

    protected function __construct()
    {
    }

    public function getGridClassName(): string
    {
        return $this->gridClassName;
    }

    public function setGridClassName(string $gridClassName): self
    {
        $this->gridClassName = $gridClassName;

        return $this;
    }

    public function getSearchModelClassName(): string
    {
        return $this->searchModelClassName;
    }

    public function setSearchModelClassName(string $searchModelClassName): self
    {
        $this->searchModelClassName = $searchModelClassName;

        return $this;
    }

    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }

    public function setModelClassName(string $modelClassName): self
    {
        $this->modelClassName = $modelClassName;

        return $this;
    }

    public function getSearchView(): string
    {
        return $this->searchView;
    }

    public function setSearchView(string $searchView): self
    {
        $this->searchView = $searchView;

        return $this;
    }

    public function getColumns(): array
    {
        $columnsWithLabels = [];
        foreach ($this->columns as $type) {
            $resourceModel = (new Resource(['type' => $type]))->buildResourceModel($this);
            $columnsWithLabels[$type] = $resourceModel->decorator()->displayTitle();
        }

        return $columnsWithLabels;
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getToObjectUrl(): string
    {
        return $this->toObjectUrl;
    }

    public function setToObjectUrl(string $toObjectUrl): self
    {
        $this->toObjectUrl = $toObjectUrl;

        return $this;
    }

    public function getFullTypePrefix(): string
    {
        return $this->fullTypePrefix ?? 'overuse';
    }

    public function setFullTypePrefix(string $fullTypePrefix): self
    {
        $this->fullTypePrefix = $fullTypePrefix;

        return $this;
    }

    public function getResourceModelClassName(): string
    {
        return $this->resourceModelClassName;
    }

    public function setResourceModelClassName(string $resourceModelClassName): self
    {
        $this->resourceModelClassName = $resourceModelClassName;

        return $this;
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new RunTimeException('Cannot unserialize singleton');
    }

    public static function build(): self
    {
        if (!isset(self::$configurator)) {
            self::$configurator = new self();
        }

        return self::$configurator;
    }

    public function getFilterColumns(): array
    {
        $columns = [];
        foreach ($this->getColumns() as $type => $label) {
            $columns['overuse,' . $type] = $label;
        }

        return $columns;
    }

    public function getTypes(): array
    {
        return array_keys($this->getColumns());
    }

    public function getModel($params = [])
    {
        return $this->createObject($this->getModelClassName(), $params);
    }

    public function getSearchModel($params = [])
    {
        return $this->createObject($this->getSearchModelClassName(), $params);
    }

    public function getResourceModel($params = [])
    {
        return $this->createObject($this->getResourceModelClassName(), $params);
    }

    private function createObject(string $className, $params = [])
    {
        return Yii::createObject(array_merge(['class' => $className], $params));
    }

    public function getModelName(): string
    {
        return call_user_func([$this->getModel(), 'modelName']);
    }
}
