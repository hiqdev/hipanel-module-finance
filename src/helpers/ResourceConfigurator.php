<?php

namespace hipanel\modules\finance\helpers;

use Closure;
use hipanel\modules\finance\models\proxy\Resource;
use RuntimeException;
use Yii;
use yii\grid\Column;
use yii\helpers\Html;

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

    private array $totalGroups = [];

    private ?Closure $totalGroupsTransformer = null;

    private static self $configurator;

    protected function __construct()
    {
    }

    public function renderGridView(array $options): string
    {
        $loader = ResourceHelper::getResourceLoader();
        $groups = $this->getTotalGroups();
        if (!empty($groups)) {
            $options['tableFooterRenderer'] = static function ($grid) use ($groups, $loader): string {
                $cells = $groupCells = [];
                foreach ($grid->columns as $column) {
                    /* @var $column Column */
                    $cells[] = $column->renderFooterCell();
                }
                $content = Html::tag('tr', implode('', $cells), $grid->footerRowOptions);
                if ($grid->filterPosition === $grid::FILTER_POS_FOOTER) {
                    $content .= $grid->renderFilters();
                }
                foreach ($groups as $group) {
                    $groupCells[] = Html::tag('td', $loader, ['colspan' => count($groups), 'data-type' => true, 'class' => 'text-bold text-center ' . implode('-', $group)]);
                }
                $content .= Html::tag('tr', implode('', $groupCells));

                return "<tfoot>\n" . $content . "\n</tfoot>";
            };
        }

        return call_user_func([$this->getGridClassName(), 'widget'], $options);
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

    public function getTotalGroups(): array
    {
        return $this->totalGroups;
    }

    public function setTotalGroups(array $totalGroups): self
    {
        $this->totalGroups = $totalGroups;

        return $this;
    }

    public function setTotalGroupsTransformer(?Closure $totalGroupsTransformer): self
    {
        $this->totalGroupsTransformer = $totalGroupsTransformer;

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

    public function getRawColumns(): array
    {
        return $this->columns;
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

    public function modifyTotalGroups(array $total): array
    {
        if (!is_callable($this->totalGroupsTransformer)) {
            return $total;
        }

        return call_user_func($this->totalGroupsTransformer, $total, $this->getTotalGroups());
    }
}
