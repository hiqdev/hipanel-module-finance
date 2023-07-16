<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\helpers\ArrayHelper;
use hipanel\helpers\StringHelper;
use hipanel\models\Ref;
use hipanel\widgets\VueTreeSelectInput;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

class BillTypeVueTreeSelect extends VueTreeSelectInput
{
    /**
     * @var array<Ref> $billTypes - list of bill types Ref objects
     */
    public array $billTypes = [];
    public ?string $replaceAttribute = null;
    public bool $multiple = false;
    public array $allowedTypes = [];
    public array $deprecatedTypes = [];
    public ?TreeSelectBehavior $behavior = null;

    public function run(): string
    {
        $id = $this->getId();
        $options = $this->buildOptionsArray();
        $value = Html::getAttributeValue($this->model, $this->replaceAttribute ?? $this->attribute);
        if ($this->multiple) {
            $value = empty($value) ? [] : explode(',', $value);
        } else {
            $value = empty($value) ? null : $value;
        }
        $this->registerJs($id);
        $activeInput = Html::activeHiddenInput($this->model, $this->attribute, [
            'v-model' => 'value',
            'value'   => null,
            'data'    => [
                'value'   => $value,
                'options' => Json::encode($options),
            ],
        ]);

        return sprintf(/** @lang HTML */ '
            <div id="%s">
                <treeselect
                  :options="options"
                  :show-count="true"
                  :always-open="false"
                  :append-to-body="true"
                  :disable-branch-nodes="%s"
                  :multiple="%s"
                  value-consists-of="LEAF_PRIORITY"
                  delimiter=","
                  auto-select-ancestors="true"
                  search-nested
                  placeholder="%s"
                  v-model="value"
                  z-index="1100"
                  @select="typeChange"
                  @deselect="typeChange"
                  :before-clear-all="typeChange"
                >
                    <label slot="option-label" slot-scope="{ node, shouldShowCount, count, labelClassName, countClassName }" :class="labelClassName">
                        <span v-html="node.raw.deprecated ? s(node.label) : node.label"></span>
                        <span v-if="shouldShowCount" :class="countClassName">({{ count }})</span>
                    </label>
                    <div slot="value-label" slot-scope="{ node }" v-html="node.raw.treeLabel ?? node.raw.label"></div>
                </treeselect>
                %s
            </div>
        ',
            $id,
            $this->multiple ? 'false' : 'true', // disable/enable branch nodes
            var_export($this->multiple, true), // multiple
            $this->model->getAttributeLabel($this->getAttribute()), // set placeholder
            $activeInput
        );
    }

    public function registerJs(string $widgetId): void
    {
        $this->view->registerJs(
            sprintf(/** @lang JavaScript */ "
                ;(() => {
                    const container = $('#%s');
                    new Vue({
                        el: container.get(0),
                        components: {
                          'treeselect': VueTreeselect.Treeselect,
                        },
                        data: {
                            value: container.find('input[type=hidden]').data('value'),
                            options: container.find('input[type=hidden]').data('options')
                        },
                        methods: {
                          typeChange: function (node) {
                            this.value = typeof node === 'undefined' ? null : node.id;
                            this.\$nextTick(function () {
                              const el = this.\$el.querySelector('input:not(.vue-treeselect__input)');
                              $(el).trigger('change');
                            });
                          },
                          s(text) {
                              return '<s>' + text + '</s>';
                          }
                        }
                    });
                })();
                ",
                $widgetId
            )
        );
    }

    private function buildOptionsArray(): array
    {
        $types = ArrayHelper::index($this->billTypes, 'id');
        // Each type key is a string like "monthly,hardware" or "monthly,leasing,server"
        // We need to split it by comma and build a recursive array of options for vue-treeselect, where ID is a type name
        $options = [];
        foreach ($types as $id => $type) {
            if ($this->behavior === TreeSelectBehavior::Hidden && $this->isDeprecatedType($type->name)) {
                continue;
            }
            $typeParts = explode(',', $type->name);
            $currentLevel = &$options;
            foreach ($typeParts as $i => $typePart) {
                // skip last part, because it is a type name
                if ($i === count($typeParts) - 1) {
                    $currentLevel = &$currentLevel['children'][$typePart];
                    break;
                }
                if (!isset($currentLevel['children'][$typePart])) {
                    $currentLevel['children'][$typePart] = [
                        'id'       => $typePart,
                        'label'    => $typePart,
                        'children' => [],
                    ];
                }
                $currentLevel = &$currentLevel['children'][$typePart];
            }
            $currentLevel = [
                'id'         => (string)$id,
                'label'      => $type->label,
                'type'       => $type->name,
                'treeLabel'  => str_contains($type->name, ',') ? $this->findTreeLabel($type) : null,
                'isDisabled' => str_contains($type->name, 'delimiter'),
            ];
            if ($this->behavior === TreeSelectBehavior::Deprecated && $this->isDeprecatedType($type->name)) {
                $currentLevel['deprecated'] = true;
            }
        }

        // Remove all keys in children array recursively, because vue-treeselect expects only array of options
        $children = $options['children'] ?? [];
        $children = $this->filter($children);

        return $this->removeKeysRecursively(array_values($children));
    }

    private function findTreeLabel(Ref $type): ?string
    {
        $types = ArrayHelper::index($this->billTypes, 'name');
        $parts = [];
        $chunks = explode(',', $type->name);
        $key = '';
        foreach ($chunks as $part) {
            $key .= empty($key) ? $part : ',' . $part;
            if (isset($types[$key]) && $key !== $type->name) {
                $parts[$key] = Html::tag('span', StringHelper::truncate($this->fixLang($types[$key]->label), 10));
            }
        }
        if ($this->isDeprecatedType($type->name)) {
            $parts[] = Html::tag('s', $this->fixLang($types[$type->name]->label));
        } else {
            $parts[] = $this->fixLang($types[$type->name]->label);
        }

        return !empty($parts) ? implode("", $parts) : null;
    }

    private function fixLang(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        return Yii::$app->getI18n()->removeLegacyLangTags($text);
    }

    private function getAttribute(): string
    {
        return $this->replaceAttribute ?? $this->attribute;
    }

    private function isDeprecatedType(string $typeName): bool
    {
        return ($this->deprecatedTypes && in_array($typeName, $this->deprecatedTypes));
    }

    private function isDisabled(string $typeName): bool
    {
        if (str_contains($typeName, 'delimiter') || ($this->behavior === TreeSelectBehavior::Disabled && $this->isDeprecatedType($typeName))) {
            return true;
        }

        return false;
    }

    private function filter(array $children, array $remained = []): array
    {
        if (empty($children) || empty($this->allowedTypes)) {
            return $children;
        }
        foreach ($this->allowedTypes as $remainType) {
            foreach ($children as $typeName => $child) {
                if (isset($child['type']) && $child['type'] === $remainType) {
                    $remained[$typeName] = $child;
                } else if (isset($child['children'])) {
                    $remained = $this->filter($child['children'], $remained);
                }
            }
        }

        return $remained;
    }
}
