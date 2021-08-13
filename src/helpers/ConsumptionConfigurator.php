<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\ClientResource;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use hipanel\modules\finance\models\ServerResource;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetResource;
use hipanel\modules\server\models\Server;
use yii\db\ActiveRecordInterface;
use Yii;

final class ConsumptionConfigurator
{
    public function getColumns(string $class): array
    {
        return $this->getConfigurationByClass($class)['columns'];
    }

    public function getGroups(string $class): array
    {
        $groups = [];
        $columns = $this->getColumns($class);
        foreach ($this->getConfigurationByClass($class)['groups'] as $group) {
            $groups[] = $group;
            foreach ($group as $item) {
                $columns = array_diff($columns, [$item]);
            }
        }
        foreach ($columns as $column) {
            $groups[] = [$column];
        }

        return $groups;
    }

    public function getGroupsWithLabels(string $class): array
    {
        $groups = [];
        $columnsWithLabels = $this->getColumnsWithLabels($class);
        foreach ($this->getGroups($class) as $i => $group) {
            foreach ($group as $j => $type) {
                $groups[$i][$type] = $columnsWithLabels[$type];
            }
        }

        return $groups;
    }

    public function getColumnsWithLabels(string $class): array
    {
        $result = [];
        foreach ($this->getColumns($class) as $column) {
            $decorator = $this->getDecorator($class, $column);
            $result[$column] = $decorator->displayTitle();
        }

        return $result;
    }

    public function getClassesDropDownOptions(): array
    {
        return ArrayHelper::getColumn($this->getConfigurations(), 'label');
    }

    public function getAllPossibleColumns(): array
    {
        $columns = [];
        foreach ($this->getConfigurations() as $configuration) {
            $columns = array_merge($configuration['columns'], $columns);
        }

        return array_unique($columns);
    }

    public function getAllPossibleColumnsWithLabels(): array
    {
        $result = [];
        foreach ($this->getConfigurations() as $class => $configuration) {
            $columns = $configuration['columns'];
            foreach ($columns as $column) {
                $decorator = $this->getDecorator($class, $column);
                $result[$column] = $decorator->displayTitle();
            }
        }

        return $result;
    }

    public function getDecorator(string $class, string $type): ResourceDecoratorInterface
    {
        $config = $this->getConfigurationByClass($class);
        $config['resourceModel']->type = $type;
        /** @var ResourceDecoratorInterface $decorator */
        $decorator = $config['resourceModel']->decorator();

        return $decorator;
    }

    public function buildResourceModel(ActiveRecordInterface $resource)
    {
        $config = $this->getConfigurationByClass($resource->class);
        $config['resourceModel']->setAttributes([
            'type' => $resource->type,
            'unit' => $resource->unit,
            'quantity' => $resource->getAmount(),
        ]);

        return $config['resourceModel'];
    }

    public function fillTheOriginalModel(Consumption $consumption)
    {
        $configuration = $this->getConfigurationByClass($consumption->class);
        $configuration['model']->setAttributes($consumption->mainObject, false);

        return $configuration['model'];
    }

    /**
     * @param string $class
     * @return array{label: string, columns: array, group: array, model: ActiveRecordInterface, resourceModel: ActiveRecordInterface}
     */
    private function getConfigurationByClass(string $class): array
    {
        return $this->getConfigurations()[$class];
    }

    private function getConfigurations(): array
    {
        return [
            'device' => [
                'label' => Yii::t('hipanel:finance', 'Server resources'),
                'columns' => ['server_traf', 'server_traf_in', 'server_traf95', 'server_traf95_in', 'ip_num'],
                'groups' => [['server_traf', 'server_traf_in'], ['server_traf95', 'server_traf95_in']],
                'model' => $this->createObject(Server::class),
                'resourceModel' => $this->createObject(ServerResource::class),
            ],
            'anycastcdn' => [
                'label' => Yii::t('hipanel:finance', 'Anycast CDN resources'),
                'columns' => [
                    'server_traf_max',
                    'server_traf95',
                    'server_traf95_max',
                    'server_du',
                    'server_ssd',
                    'server_sata',
                    'cdn_traf',
                    'cdn_traf_max',
                    'storage_du',
                ],
                'groups' => [['server_traf', 'server_traf_in'], ['server_traf95', 'server_traf95_in'], ['cdn_traf', 'cdn_traf_max']],
                'model' => $this->createObject(Target::class),
                'resourceModel' => $this->createObject(TargetResource::class),
            ],
            'videocdn' => [
                'label' => Yii::t('hipanel:finance', 'Video CDN resources'),
                'columns' => [
                    'server_traf',
                    'server_traf_max',
                    'server_traf95',
                    'server_traf95_max',
                    'server_du',
                    'server_ssd',
                    'server_sata',
                ],
                'groups' => [['server_traf', 'server_traf_in'], ['server_traf95', 'server_traf95_in']],
                'model' => $this->createObject(Target::class),
                'resourceModel' => $this->createObject(TargetResource::class),
            ],
            'client' => [
                'label' => Yii::t('hipanel:finance', 'Client resources'),
                'columns' => ['referral'],
                'groups' => [],
                'model' => $this->createObject(Client::class),
                'resourceModel' => $this->createObject(ClientResource::class),
            ],
        ];
    }

    private function createObject(string $className, array $params = []): object
    {
        return Yii::createObject(array_merge(['class' => $className], $params));
    }
}
