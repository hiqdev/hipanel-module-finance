<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\widgets\combo\PlanCombo;
use hiqdev\combo\StaticCombo;
use Yii;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

class TariffComboWithTypeSuggester extends Widget
{
    public ActiveForm $form;
    public Model $model;
    public array $models;
    public string $tariffAttribute = 'tariff_id';
    public array $tariffComboOptions = [];
    public string $tariffComboType = 'plan/name';
    public string $suggestAttribute = 'class';
    public string $htmlTemplate = '<div class="row"><div class="col-md-6">{typeSelector}</div><div class="col-md-6">{tariffSelector}</div></div>';
    public bool $withLabels = true;
    private ?array $suggested = null;
    private ?array $selectedValues = null;
    private ?string $suggesterSelectorId = null;
    private static array $suggester = [
        // by classes
        'account' => ['account'],
        'anycastcdn' => ['anycastcdn'],
        'client' => ['client', 'referral'],
        'device' => ['server', 'switch'],
        'part' => ['hardware'],
        'private_cloud' => ['private_cloud'],
        'private_cloud_backup' => ['private_cloud_backup'],
        'snapshot' => ['snapshot'],
        'storage' => ['storage'],
        'videocdn' => ['vcdn', 'server'],
        'volume' => ['volume'],
        'vps' => ['vps'],
        // by server types
        'cdn' => ['vcdn', 'server'],
    ];

    public function init()
    {
        parent::init();
        $this->suggesterSelectorId = sprintf("suggester_selector_%s_%s", $this->id, mt_rand());
        $this->suggestTypes();
        $this->registerClientScript();
    }

    public function run()
    {
        return strtr($this->htmlTemplate, [
            '{typeSelector}' => $this->renderTypeSelector(),
            '{tariffSelector}' => $this->renderTariffSelector(),
        ]);
    }

    private function renderTypeSelector()
    {
        $fakeModel = new DynamicModel(['classes' => $this->selectedValues]);
        $fakeModel->addRule(['classes'], 'safe');
        $fakeModel->setAttributeLabel('classes', Yii::t('hipanel:finance', 'Tariff type suggester'));
        $raw = $this->flatten(self::$suggester);
        $data = array_combine($raw, $raw);

        return $this->form->field($fakeModel, 'classes')->widget(StaticCombo::class, [
            'data' => $data,
            'multiple' => true,
            'selectAllButton' => false,
            'id' => $this->suggesterSelectorId,
            'inputOptions' => [
                'id' => $this->suggesterSelectorId,
            ],
        ])->label($this->withLabels);
    }

    private function renderTariffSelector()
    {
        $options = array_merge(['tariffType' => $this->suggested], $this->tariffComboOptions);

        return $this->form
            ->field($this->model, $this->tariffAttribute)
            ->widget(PlanCombo::class, $options)->label($this->withLabels);
    }

    private function suggestTypes(): void
    {
        $suggested = [];
        $suggestGroup = array_unique(array_column($this->models, $this->suggestAttribute));
        foreach ($suggestGroup as $group) {
            if (isset(self::$suggester[$group])) {
                $suggested = [...$suggested, ...self::$suggester[$group]];
            }
        }
        $this->suggested = $suggested;
    }

    private function registerClientScript(): void
    {
        $js = ';(() => {';
        $js .= 'const suggesterId =' . Json::htmlEncode($this->suggesterSelectorId) . ';';
        $js .= '$("#" + suggesterId).val(' . Json::htmlEncode($this->suggested) . ').trigger("change");';
        $js .= 'function update(event) {
            var format = [], options = $(this).select2("data");
            var comboField = $("#" + suggesterId)
                .closest("form")
                .combo()
                .fields
                .filter(field => field.type === "' . $this->tariffComboType . '")[0];
            options.forEach(option => {
                format.push(option.id);
            });
            comboField.field.select2Options.ajax.filter.type_in.format = format;
        }';
        $js .= '$("#" + suggesterId).on("select2:select", update);';
        $js .= '$("#" + suggesterId).on("select2:unselect", update);';
        $js .= '})();';
        $this->view->registerJs($js);
    }

    private function flatten(array $array): array
    {
        $return = [];
        array_walk_recursive($array, static function ($a) use (&$return) {
            $return[] = $a;
        });

        return array_unique($return);
    }
}
