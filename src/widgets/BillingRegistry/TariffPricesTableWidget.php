<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\BillingRegistry;

use hiqdev\billing\registry\behavior\ResourceDecoratorBehavior;
use hiqdev\billing\registry\behavior\ResourceDecoratorBehaviorNotDeclaredException;
use hiqdev\billing\registry\behavior\ResourceDecoratorBehaviorNotFoundException;
use hiqdev\billing\registry\product\Aggregate;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorBehaviorSearch;
use hiqdev\billing\registry\Type\TypeSemantics;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use ReflectionClass;
use yii\base\Widget;
use yii\helpers\Html;

class TariffPricesTableWidget extends Widget
{
    private const DEFAULT_REPRESENTATION = 'Hosting services fee from || date';

    public TariffTypeDefinitionInterface $tariff;
    private BillingRegistryServiceInterface $registryService;
    private array $representationsByClassAndPriceType = [];
    public string $tariffTypeNameSlug = '';

    public function __construct(
        BillingRegistryServiceInterface $registryService,
        private ResourceDecoratorBehaviorSearch $resourceDecoratorBehaviorSearch,
        private TypeSemantics $typeSemantics,
        $config = []
    ) {
        parent::__construct($config);
        $this->registryService = $registryService;
        $this->representationsByClassAndPriceType = $this->buildRepresentationsArray();
    }

    public function init()
    {
        parent::init();
        if ($this->tariff === null) {
            throw new \InvalidArgumentException('Tariff definition must be provided');
        }

        $this->view->registerCss(<<<CSS
.tariff-prices-info {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #333;
}

.price-type-card {
    background-color: #fff;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    margin-bottom: 25px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.price-type-card-header {
    background-color: #f8f9fa;
    padding: 12px 18px;
    border-bottom: 1px solid #e3e3e3;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
}

.price-type-card-header h4 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 600;
    color: #212529;
}

.price-type-card-header .price-type-class {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: normal;
    margin-left: 8px;
}

.price-type-card .table {
    margin-bottom: 0;
}

.price-type-card .table td {
    padding: 10px 15px;
    vertical-align: top;
}

.price-type-card .property-name {
    font-weight: 500;
    width: 25%;
    color: #495057;
}

.representation-details {
    margin-bottom: 8px;
}
.representation-details:last-child {
    margin-bottom: 0;
}

.representation-details summary {
    cursor: pointer;
    padding: 5px 8px;
    background-color: #f1f3f5;
    border-radius: 3px;
    font-weight: 500;
    margin-bottom: 5px;
    outline: none;
    display: list-item;
}
.representation-details summary:hover {
    background-color: #e9ecef;
}

.representation-details pre {
    background-color: #fdfdfe;
    border: 1px solid #ced4da;
    padding: 10px;
    border-radius: 4px;
    font-size: 0.875em;
    color: #212529;
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-all;
}

.text-muted {
    color: #6c757d !important;
}
.small {
    font-size: 85%;
}

.decorator-status-error, .formatter-status-error, .representation-status-none {
    color: #dc3545;
    font-style: italic;
}
.decorator-status-not-used {
    color: #ffc107;
    font-style: italic;
}
.representation-status-default {
}

CSS
        );
    }

    private function buildRepresentationsArray(): array
    {
        $result = [];
        foreach ($this->registryService->getRepresentationsByType(RepresentationInterface::class) as $representation) {
            $className = (new ReflectionClass($representation))->getShortName();

            // Ensure unique key for representations of the same class for different price types
            $result[$className][$representation->getType()->getName()] = $representation;
        }

        return $result;
    }

    public function run()
    {
        $content = Html::tag('h3', 'Price Configuration Details', ['style' => 'margin-bottom: 20px;']);

        foreach ($this->tariff->withPrices()->getIterator() as $priceType) {
            $content .= $this->renderPriceTypeCard($priceType);
        }

        return Html::tag('div', $content, ['class' => 'tariff-prices-info']);
    }

    protected function renderPriceTypeCard(PriceTypeDefinitionInterface $priceType)
    {
        $priceTypeNameSlug = $this->slugify($priceType->type()->getName());
        $cardId = 'price-type--' . $this->tariffTypeNameSlug . '--' . $priceTypeNameSlug;

        $headerContent = $this->renderPriceTypeCardHeader($priceType);
        $header = Html::tag('div', $headerContent, ['class' => 'price-type-card-header']);

        $tableRows = [
            $this->renderPropertyRow('Description', $this->renderPriceTypeDescription($priceType)),
            // Changed "Type" to "Description" for clarity
            $this->renderPropertyRow('Decorator', $this->renderDecoratorInfo($priceType)),
            $this->renderPropertyRow('MeasuredWith Class', $this->renderMeasuredWithInfo($priceType)),
            $this->renderPropertyRow('Behaviors', $this->renderBehaviorsInfo($priceType)),
            $this->renderPropertyRow('Unit & Agg.', $this->renderUnitAndAggregationInfo($priceType)),
            $this->renderPropertyRow('Quantity Formatter', $this->renderQuantityFormatterInfo($priceType)),
            $this->renderPropertyRow('Invoice Representation', $this->renderRepresentationsInfo($priceType)),
        ];

        $tbody = Html::tag('tbody', implode('', $tableRows));
        $table = Html::tag('table', $tbody, ['class' => 'table table-hover']); // table-hover for subtle row hover

        return Html::tag('div', $header . $table, [
            'class' => 'price-type-card',
            'id' => $cardId
        ]);
    }

    protected function renderPriceTypeCardHeader(PriceTypeDefinitionInterface $priceType)
    {
        $priceTypeName = Html::encode($priceType->type()->getName());
        $priceTypeClass = Html::tag(
            'span',
            '(' . (new ReflectionClass($priceType))->getShortName() . ')',
            ['class' => 'price-type-class']
        );

        return Html::tag('h4', $priceTypeName . ' ' . $priceTypeClass);
    }

    protected function renderPropertyRow(string $propertyName, string $propertyValue)
    {
        $nameCell = Html::tag('td', Html::encode($propertyName), ['class' => 'property-name']);
        $valueCell = Html::tag('td', $propertyValue);

        return Html::tag('tr', $nameCell . $valueCell);
    }

    protected function renderPriceTypeDescription(PriceTypeDefinitionInterface $priceType)
    {
        $description = $priceType->getDescription();

        return Html::encode(empty($description) ? 'N/A' : $description);
    }

    protected function renderDecoratorInfo(PriceTypeDefinitionInterface $priceType)
    {
        $content = '';
        try {
            $decorator = $this->resourceDecoratorBehaviorSearch->find(
                $this->registryService,
                $this->typeSemantics->localName($priceType->type())
            );
            $shortClass = (new ReflectionClass($decorator->class))->getShortName();
            $content = "👒 " . Html::encode($shortClass);
        } catch (ResourceDecoratorBehaviorNotFoundException $e) {
            $content = Html::tag('span', "‼️ No decorator, exception was thrown",
                ['class' => 'decorator-status-error']);
        } catch (ResourceDecoratorBehaviorNotDeclaredException $e) {
            $content = Html::tag('span', "🔸 Not used in UI (or no specific decorator needed)",
                ['class' => 'decorator-status-not-used']);
        }

        return $content;
    }

    protected function renderMeasuredWithInfo(PriceTypeDefinitionInterface $priceType): string
    {
        return Html::tag('span', 'Not implemented yet.', [
            'class' => 'text-muted',
        ]);
    }

    private array $doNotRenderBehaviors = [
        ResourceDecoratorBehavior::class, // We render this one separately
    ];

    protected function renderBehaviorsInfo(PriceTypeDefinitionInterface $priceType)
    {
        $behaviors = [];
        $behaviorCollection = $priceType->withBehaviors();
        
        foreach ($behaviorCollection as $behavior) {
            if (in_array(get_class($behavior), $this->doNotRenderBehaviors, true)) {
                continue;
            }

            $behaviorClass = (new ReflectionClass($behavior))->getShortName();
            $behaviorDescription = $behavior->description();
            
            $behaviorHtml = Html::tag('div', 
                Html::tag('strong', Html::encode($behaviorClass)) . ': ' . $behaviorDescription,
                ['style' => 'margin-bottom: 5px;']
            );
            $behaviors[] = $behaviorHtml;
        }

        if (empty($behaviors)) {
            return Html::tag('span', '–', ['class' => 'text-muted']);
        }

        $behaviorsList = implode('', $behaviors);

        return Html::tag('div', $behaviorsList, ['style' => 'margin-top: 5px;']);
    }

    protected function renderUnitAndAggregationInfo(PriceTypeDefinitionInterface $priceType)
    {
        $unit = Html::encode($priceType->getUnit()->name());
        try {
            $aggregation = $priceType->getAggregate();
            $aggSymbol = match ($aggregation) {
                Aggregate::sum => '∑',
                Aggregate::last => 'Last',
                Aggregate::max => 'Max',
                Aggregate::count => '⠿ Count',
                Aggregate::one => '1 One',
                default => 'Unknown'
            };

            return Html::encode("{$aggSymbol} ({$unit})");
        } catch (\Exception $e) {
            return Html::tag('span', '‼️ EXCEPTION: no aggregation',
                    ['class' => 'decorator-status-error']) . " ({$unit})";
        }
    }

    protected function renderQuantityFormatterInfo(PriceTypeDefinitionInterface $priceType)
    {
        return $this->formatQuantityFormatter($priceType);
    }

    protected function formatQuantityFormatter(PriceTypeDefinitionInterface $priceType)
    {
        $quantityFormatter = $priceType->getQuantityFormatterDefinition();

        if ($quantityFormatter === null) {
            return Html::tag('em', '‼️ No quantity formatter defined', ['class' => 'formatter-status-error']);
        }

        if ($quantityFormatter->formatterClass() !== null) {
            return $this->formatClassBasedQuantityFormatter($priceType, $quantityFormatter);
        }

        return Html::tag('span', Html::encode($quantityFormatter->getFractionUnit()->label));
    }

    protected function formatClassBasedQuantityFormatter(PriceTypeDefinitionInterface $priceType, $quantityFormatter)
    {
        $reflection = new ReflectionClass($quantityFormatter->formatterClass());
        $formatterName = $reflection->getShortName();

        $quantity = $this->generateRandomQuantity($priceType);
        $example = $priceType->createQuantityFormatter(
            new FractionQuantityData($quantity, date('c'), 1)
        );

        return Html::tag('code', '[' . Html::encode($formatterName) . ']') . ': ' . $example->format();
    }

    protected function renderRepresentationsInfo(PriceTypeDefinitionInterface $priceType)
    {
        $content = '';
        $foundCustom = false;

        foreach ($this->representationsByClassAndPriceType as $className => $representations) {
            if (isset($representations[$priceType->type()->getName()])) {
                $representation = $representations[$priceType->type()->getName()];
                $content .= $this->renderRepresentationDetails($className, $representation->getSql());
                $foundCustom = true;
            }
        }

        if (!$foundCustom) {
            $noCustomMsg = Html::tag('span', '♦️ No custom representation. Using default.',
                ['class' => 'representation-status-none']);
            $defaultRep = $this->renderRepresentationDetails('Default Representation', self::DEFAULT_REPRESENTATION,
                true);
            $content = $noCustomMsg . Html::tag('br') . $defaultRep;
        }

        return $content;
    }

    protected function renderRepresentationDetails(
        string $summaryTitle,
        string $representationSql,
        bool $isOpen = false
    ) {
        $summary = Html::tag('summary', "↓&nbsp;" . Html::encode($summaryTitle));
        $pre = Html::tag('pre', Html::encode($representationSql));

        $options = ['class' => 'representation-details'];
        if ($isOpen) {
            $options['open'] = true;
        }

        return Html::tag('details', $summary . $pre, $options);
    }

    private function generateRandomQuantity(PriceTypeDefinitionInterface $priceType): Quantity
    {
        $measure = $priceType->getUnit()->createExternalUnit()->getMeasure();

        $quantityValue = match ($measure) {
            'item' => rand(1, 20),
            'bit' => rand(920 * 1024 * 1024, 15 * 1024 * 1024 * 1024), // between 920MB and 15GB
            'bps' => rand(95 * 1024 * 1024, 400 * 1024 * 1024), // between 95Mbps and 400Mbps
            'min' => rand(1, 4) * 3600, // between 1 and 4 hours in seconds
            'power' => rand(100, 1000), // between 100W and 1000W
            default => 1
        };

        $quantity = Quantity::create($priceType->getUnit()->name(), $quantityValue);
        if ($measure === 'bit') {
            $quantity = $quantity->convert(Unit::create('gb'));
        } elseif ($measure === 'bps') {
            $quantity = $quantity->convert(Unit::create('mbps'));
        }

        return $quantity;
    }

    protected function slugify(string $string): string
    {
        $string = preg_replace('/[^a-zA-Z0-9\s-]/u', '', mb_strtolower((string)$string));
        $string = preg_replace('/[\s-]+/', '-', $string);

        return trim($string, '-');
    }

}

