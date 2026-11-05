<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\grid\RefColumn;
use hipanel\modules\finance\grid\presenters\price\PricePresenter;
use hipanel\modules\finance\grid\presenters\price\PricePresenterFactory;
use hipanel\modules\finance\grid\presenters\price\ProgressivePricePresenter;
use hipanel\modules\finance\menus\PriceActionsMenu;
use hipanel\modules\finance\helpers\RegionalPriceGrouper;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\RegionalPrice;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\bootstrap\Html;
use yii\data\ArrayDataProvider;

/**
 * Class PriceGridView.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceGridView extends BoxedGridView
{
    private const REGION_BLOCK_STYLE = 'padding: .5em 0;';
    private const REGION_BLOCK_BORDER = ' border-top: 1px solid rgba(128, 128, 128, .3);';
    public const REGION_ROW_STYLE = ' display: flex; justify-content: space-between; align-items: center; gap: .6em;';

    /**
     * Collapses the regional prices of one metric into one row.
     * Enable it only for a grid whose models are a flat list of prices.
     */
    public bool $groupRegionalPrices = false;

    public function __construct(
        readonly private PricePresenterFactory $presenterFactory,
        array $config = []
    )
    {
        parent::__construct($config);
    }

    public function init()
    {
        if ($this->groupRegionalPrices && $this->dataProvider instanceof ArrayDataProvider) {
            $this->dataProvider->allModels = RegionalPriceGrouper::group($this->dataProvider->allModels);
        }
        parent::init();
    }

    public function columns()
    {
        return array_merge(parent::columns(), [
            'checkbox' => [
                'class' => RegionalCheckboxColumn::class,
            ],
            'plan' => [
                'format' => 'raw',
                'filterAttribute' => 'plan_name_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'value' => fn(Price $model) => Html::a(Html::encode($model->plan->name), ['@plan/view', 'id' => $model->plan->id]),
            ],
            'price' => [
                'label' => Yii::t('hipanel.finance.price', 'Price'),
                'format' => 'raw',
                'value' => fn(Price $model) => $model instanceof RegionalPrice
                    ? $this->renderRegionalPrices($model)
                    : $this->presenterFactory->build($model::class)->renderPrice($model),
            ],
            'old_price' => [
                'label' => Yii::t('hipanel.finance.price', 'Old price'),
                'format' => 'raw',
                'value' => function (Price $model): string {
                    $presenter = $this->presenterFactory->build($model::class);

                    return $presenter
                        ->setPriceAttribute('old_price')
                        ->renderPrice($model);
                },
            ],
            'object->name_clear' => [
                'label' => Yii::t('hipanel', 'Object'),
                'value' => fn(Price $model) => $model->object->name ?: Yii::t('hipanel.finance.price', 'Any'),
            ],
            'object->name' => [
                'label' => Yii::t('hipanel', 'Object'),
                'format' => 'raw',
                'value' => function (Price $model) {
                    if (!$model->isRelationPopulated('object')) {
                        return Yii::t('hipanel.finance.price', 'Any');
                    }

                    $link = LinkToObjectResolver::widget([
                        'model' => $model->object,
                        'labelAttribute' => 'name',
                    ]);

                    return Yii::t('hipanel.finance.price', $link) ?: Yii::t('hipanel.finance.price', 'Any');
                },
            ],
            'object->name-any' => [
                'label' => Yii::t('hipanel', 'Object'),
                'value' => fn(Price $model) => Yii::t('hipanel.finance.price', 'Any'),
            ],
            'object->label' => [
                'class' => RefColumn::class,
                'attribute' => 'object_name_ilike',
                'label' => Yii::t('hipanel', 'Details'),
                'i18nDictionary' => 'hipanel.finance.plan',
                'value' => fn(Price $model) => $model->isRelationPopulated('object') ? $model->object->label : $model->main_object_name,
            ],
            'type' => [
                'class' => RefColumn::class,
                'label' => Yii::t('hipanel', 'Type'),
                'attribute' => 'type',
                'filterAttribute' => 'type',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'raw',
                'gtype' => 'type,bill',
                'findOptions' => [
                    'select' => 'name',
                    'pnames' => 'monthly,overuse',
                    'with_recursive' => 1,
                ],
                'value' => static fn($model) => BillType::widget(['model' => $model])
                    . ($model instanceof RegionalPrice ? ' ' . Html::tag('span', Yii::t('hipanel.finance.price', '(by region)'), ['class' => 'text-muted']) : ''),
            ],
            'unit' => [
                'class' => RefColumn::class,
                'attribute' => 'unit',
                'filterAttribute' => 'unit',
                'filterOptions' => ['class' => 'narrow-filter'],
                'gtype' => 'type,unit',
                'i18nDictionary' => 'hipanel.finance.units',
                'findOptions' => [
                    'with_recursive' => 1,
                    'select' => 'name_label',
                    'mapOptions' => ['from' => 'name'],
                ],
                'value' => fn(Price $model): ?string => $model->getUnitLabel(),
            ],
            'currency' => [
                'class' => RefColumn::class,
                'attribute' => 'currency',
                'filterAttribute' => 'currency',
                'filterOptions' => ['class' => 'narrow-filter'],
                'gtype' => 'type,currency',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => PriceActionsMenu::class,
            ],
            'info' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel', 'Details'),
                'value' => fn(Price $model) => $model instanceof RegionalPrice
                    ? $this->renderRegionalInfo($model)
                    : $this->presenterFactory->build($model::class)->renderInfo($model),
            ],
            'old_quantity' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel.finance.price', 'Old quantity'),
                'value' => fn(Price $model) => $this->presenterFactory->build($model::class)->renderInfo($model, 'old_quantity'),
            ],
            'value' => [
                'class' => ValueColumn::class,
                'visible' => Yii::$app->user->can('bill.charges.read'),
            ],
            'rate' => [
                'label' => Yii::t('hipanel.finance.price', 'Referral rate'),
                'attribute' => 'rate',
                'filter' => false,
            ],
        ]);
    }

    private function renderRegionalPrices(RegionalPrice $group): string
    {
        $blocks = [];
        foreach ($group->members as $region => $price) {
            $presenter = $this->presenterFactory->build($price::class);
            $badge = $this->renderRegionBadge($region);
            $tiers = $presenter instanceof ProgressivePricePresenter ? $presenter->renderTierLines($price) : null;
            if ($tiers !== null) { // one card per tier, the cards fill the cell's width
                $cards = array_map(
                    static fn(string $line): string => Html::tag('div', $line, ['style' => 'padding: .35em .6em; border-radius: 3px; background: rgba(128, 128, 128, .15);']),
                    $tiers
                );
                $body = Html::tag('div', implode('', $cards), [
                    'style' => 'display: grid; grid-template-columns: repeat(auto-fill, minmax(11em, 1fr)); gap: .4em; margin-top: .4em;',
                ]);
                $blocks[] = Html::tag('div', $badge . $body, ['style' => $this->regionBlockStyle($blocks === [])]);
                continue;
            }
            if ($presenter instanceof PricePresenter) {
                [$sum, $unit, $formula] = $presenter->renderPriceParts($price);
                $body = $sum . ' ' . $unit . $formula;
            } else {
                $body = $presenter->renderPrice($price);
            }
            $blocks[] = Html::tag('div', $badge . Html::tag('span', $body), [
                'style' => $this->regionBlockStyle($blocks === []) . self::REGION_ROW_STYLE,
            ]);
        }

        return implode('', $blocks);
    }

    public function renderRegionBadge(string $region): string
    {
        return Html::tag('span', Html::encode(strtoupper($region)), [
            'class' => 'label label-primary',
            'style' => 'padding: .3em .6em .2em;', // bootstrap pads the bottom more, which lifts capital letters
        ]);
    }

    /** Blocks are separated by a line, so the first one has no top border. */
    public function regionBlockStyle(bool $isFirst): string
    {
        return self::REGION_BLOCK_STYLE . ($isFirst ? '' : self::REGION_BLOCK_BORDER);
    }

    private function renderRegionalInfo(RegionalPrice $group): string
    {
        return $this->renderRegionalTable($group, function (Price $price): ?array {
            if (!$price->isOveruse()) {
                return null; // the dynamic quantity placeholder of a monthly price is not filled in grouped rows
            }

            return [
                Html::tag('span', Yii::t('hipanel:finance', '{amount,number}', ['amount' => $price->quantity]), [
                    'title' => Yii::t('hipanel.finance.price', 'Prepaid amount'),
                ]),
                Html::encode($price->getUnitLabel()),
            ];
        });
    }

    /**
     * Renders one table row per region: the region name and the cells made by $cells, so the columns line up.
     * The second column is right-aligned because it holds numbers.
     *
     * @param callable(Price): ?array $cells returns null to skip the region
     */
    private function renderRegionalTable(RegionalPrice $group, callable $cells): string
    {
        $rows = [];
        foreach ($group->members as $region => $price) {
            $regionCells = $cells($price);
            if ($regionCells === null) {
                continue;
            }
            array_unshift($regionCells, Html::tag('strong', Html::encode(strtoupper($region))));
            $tds = [];
            foreach ($regionCells as $column => $cell) {
                $tds[] = Html::tag('td', $cell, ['style' => 'padding: 0 .6em 0 0;' . ($column === 1 ? ' text-align: right;' : '')]);
            }
            $rows[] = Html::tag('tr', implode('', $tds));
        }

        return $rows === [] ? '' : Html::tag('table', implode('', $rows), ['style' => 'border-collapse: collapse; width: 100%;']);
    }
}
