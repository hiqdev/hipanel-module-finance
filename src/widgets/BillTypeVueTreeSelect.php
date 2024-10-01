<?php declare(strict_types=1);

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
        $value = Html::getAttributeValue($this->model, $this->replaceAttribute ?? $this->attribute);
        [$options, $adjustmentOptions] = $this->prepareOptions();
        if ($this->multiple) {
            $value = empty($value) ? [] : explode(',', $value);
        } else {
            $value = empty($value) ? null : $value;
        }
        $this->registerJs($id, $value);
        $activeInput = Html::activeHiddenInput($this->model, $this->attribute, [
            'v-model' => 'value',
            'value' => null,
            'data' => [
                'value' => $value,
                'options' => Json::encode($options),
                'adjustment-options' => Json::encode($adjustmentOptions),
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
                  :auto-select-ancestors="true"
                  value-consists-of="LEAF_PRIORITY"
                  delimiter=","
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
                    <div slot="after-list">
                        <div class="checkbox" style="%s">
                            <button 
                              type="button"
                              :class="{ \'btn btn-default btn-sm btn-block\': true, \'active\': adjustmentOnly }"
                              @click="toggleAdjustemnt"
                            >
                                <span v-if="adjustmentOnly">
                                    <i class="glyphicon glyphicon-eye-close"></i>
                                    Hide adjustment
                                </span>
                                <span v-else>
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                    Show adjustment
                                </span>
                            </button>
                        </div>
                    </div>
                </treeselect>
                %s
            </div>
        ',
            $id,
            $this->multiple ? 'false' : 'true', // disable/enable branch nodes
            var_export($this->multiple, true), // multiple
            $this->model->getAttributeLabel($this->getAttribute()), // set placeholder
            $this->splitTypesByAdjustment() ? 'display: block; padding: 0 1em' : 'display: none;',
            $activeInput
        );
    }

    public function registerJs(string $widgetId, $value): void
    {
        $isAdjustment = $this->isAdjustment($value) ? 'true' : 'false';
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
                            adjustmentOnly: %s,
                            options: [],
                        },
                        watch: {
                          adjustmentOnly(value) {
                            this.toggleOptions(value);
                          }
                        },
                        mounted() {
                          this.toggleOptions(%s);
                        },
                        methods: {
                          toggleAdjustemnt: function () {
                            this.value = null;
                            this.adjustmentOnly = !this.adjustmentOnly;
                          },
                          typeChange: function (node) {
                            this.value = typeof node === 'undefined' ? null : node.id;
                            this.\$nextTick(function () {
                              const el = this.\$el.querySelector('input:not(.vue-treeselect__input)');
                              $(el).trigger('change');
                            });
                          },
                          toggleOptions: function (showAdjustments) {
                            const input = container.find('input[type=hidden]');
                            if (showAdjustments === true) {
                              this.options = input.data('adjustment-options');
                            } else  {
                              this.options = input.data('options');
                            }
                          },
                          s(text) {
                              return '<s>' + text + '</s>';
                          }
                        }
                    });
                })();
                ",
                $widgetId,
                $isAdjustment,
                $isAdjustment,
            )
        );
    }

    private function buildOptionsArray(array $types): array
    {
        $types = ArrayHelper::index($types, 'id');
        // Each type key is a string like "monthly,hardware" or "monthly,installment"
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
                        'id' => $typePart,
                        'label' => $typePart,
                        'children' => [],
                    ];
                }
                $currentLevel = &$currentLevel['children'][$typePart];
            }
            $currentLevel = [
                'id' => (string)$id,
                'label' => $type->label,
                'type' => $type->name,
                'treeLabel' => str_contains($type->name, ',') ? $this->findTreeLabel($type) : null,
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
        return ($this->deprecatedTypes && in_array($typeName, $this->deprecatedTypes, true));
    }

    private function isDisabled(string $typeName): bool
    {
        if (str_contains($typeName,
                'delimiter') || ($this->behavior === TreeSelectBehavior::Disabled && $this->isDeprecatedType($typeName))) {
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

    private function isAdjustment($typeId): bool
    {
        if (!is_int($typeId)) {
            return false;
        }
        $types = ArrayHelper::index($this->billTypes, 'id');
        $type = $types[$typeId];
        if ($type === null) {
            return false;
        }

        return str_starts_with($type->name, 'adjustment');
    }

    private function prepareOptions(): array
    {
        $types = ArrayHelper::index($this->billTypes, 'id');
        if (!$this->splitTypesByAdjustment()) {
            $options = $this->buildOptionsArray($types);

            return [$options, []];
        }
        $adjustmentTypes = array_filter($types, fn(Ref $ref) => $this->isAdjustment($ref->id));
        $typesWithoutAdjustments = array_diff_key($types, $adjustmentTypes);
        $options = $this->buildOptionsArray($typesWithoutAdjustments);
        $adjustmentOptions = $this->buildOptionsArray($adjustmentTypes);

        return [$options, $adjustmentOptions];
    }

    private function splitTypesByAdjustment(): bool
    {
        return Yii::$app->user->can('owner-staff') && $this->behavior !== TreeSelectBehavior::Deprecated;
    }
}
