<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\assets\VueTreeselectAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

class BillTypeTreeselect extends InputWidget
{
    /**
     * @var array<string, string> $billTypes - list of bill types, where key – is name and value – is label
     */
    public array $billTypes = [];

    public function run()
    {
        VueTreeselectAsset::register($this->view);
        $id = $this->getId();

        $optionsJson = Json::encode($this->buildOptionsArray());
        $value = Json::htmlEncode(Html::getAttributeValue($this->model, $this->attribute));
        $this->view->registerJs(<<<JS
new Vue({
  el: '#$id',
  data: { 
      value: $value,
      options: $optionsJson 
  }
})
JS
            , View::POS_READY);

        $activeInput = Html::activeHiddenInput($this->model, $this->attribute, ['v-model' => 'value']);

        return <<<HTML
<div id="$id">
  $activeInput
  <treeselect v-model="value" :options="options" :disable-branch-nodes="true" :show-count="true" search-nested/>
</div>
HTML;
    }

    private function buildOptionsArray(): array
    {
        $types = $this->billTypes;
        // Each type key is a string like "monthly,hardware" or "monthly,leasing,server"
        // We need to split it by comma and build a recursive array of options for vue-treeselect, where ID is a type name
        $options = [];
        foreach ($types as $type => $label) {
            $typeParts = explode(',', $type);
            $currentLevel = &$options;
            foreach ($typeParts as $i => $typePart) {
                // skip last part, because it is a type name
                if ($i === count($typeParts) - 1) {
                    $currentLevel = &$currentLevel['children'][$typePart];
                    break;
                }
                if (!isset($currentLevel['children'][$typePart])) {
                    $currentLevel['children'][$typePart] = [
                        'id' => $typePart,
                        'label' => $typePart,
                        'children' => [],
                    ];
                }
                $currentLevel = &$currentLevel['children'][$typePart];
            }
            $currentLevel = [
                'id' => $type,
                'label' => $label,
            ];
        }

        // Remove all keys in children array recursively, because vue-treeselect expects only array of options
        $result = $this->removeKeysRecursively(array_values($options['children']));

        return $result;
    }

    private function removeKeysRecursively(array $items): array
    {
        foreach ($items as &$item) {
            if (isset($item['children'])) {
                $item['children'] = $this->removeKeysRecursively(array_values($item['children']));
            }
        }

        return $items;
    }
}
