<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\BillingRegistry;

use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\invoice\RepresentationInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use yii\base\Widget;
use yii\helpers\Html;

class TariffPricesTableWidget extends Widget
{
    /**
     * @var string https://git.hiqdev.com/hiqdev/hiapi-legacy/blob/master/src/lib/billing/documents/Invoice/MonthlyInvoiceBuilder.php#L77-77
     */
    private const DEFAULT_REPRESENTATION = 'Hosting services fee from || date';

    public TariffTypeDefinitionInterface $tariff;
    private BillingRegistryServiceInterface $registryService;
    /**
     * @var array<class-string<RepresentationInterface>, array<string, RepresentationInterface>>
     */
    private array $representationsByClassAndPriceType = [];

    public function __construct(BillingRegistryServiceInterface $registryService, $config = [])
    {
        parent::__construct($config);

        $this->registryService = $registryService;
        $this->representationsByClassAndPriceType = $this->buildRepresentationsArray();
    }

    public function init()
    {
        parent::init();
        if ($this->tariff === null) {
            throw new \InvalidArgumentException('Definition must be provided');
        }

        $this->view->registerCss(<<<CSS
.representation-details {
    width: 50%;
}
CSS
        );
    }

    /**
     * @return array<class-string<RepresentationInterface>, array<string, RepresentationInterface[]>>
     */
    private function buildRepresentationsArray(): array
    {
        $result = [];

        foreach ($this->registryService->getRepresentationsByType(RepresentationInterface::class) as $representation) {
            $className = (new \ReflectionClass($representation))->getShortName();
            $result[$className][$representation->getType()->getName()] = $representation;
        }

        return $result;
    }

    public function run()
    {
        $content = Html::tag('h3', 'Brief information');
        $content .= $this->renderTable();

        return Html::tag('div', $content, ['class' => 'tariff-prices-info']);
    }

    protected function renderTable()
    {
        $headers = ['Price Type', 'Unit', 'Quantity Formatter', 'Invoice Representation'];
        $headerRow = Html::tag('tr', implode('', array_map(fn($h) => Html::tag('th', $h), $headers)));
        $thead = Html::tag('thead', $headerRow);

        $rows = '';

        foreach ($this->tariff->withPrices()->getIterator() as $priceType) {
            $rows .= $this->renderTableRow($priceType);
        }
        $tbody = Html::tag('tbody', $rows);

        return Html::tag('table', $thead . $tbody, ['class' => 'table table-striped table-bordered']);
    }

    protected function renderTableRow(PriceTypeDefinitionInterface $priceType)
    {
        $cells = [
            $this->renderPriceTypeCell($priceType),
            $this->renderUnitCell($priceType),
            $this->renderQuantityFormatterCell($priceType),
            $this->renderRepresentationsCell($priceType),
        ];

        return Html::tag('tr', implode('', $cells));
    }

    protected function renderPriceTypeCell(PriceTypeDefinitionInterface $priceType)
    {
        $content = '';

        $shortClass = (new \ReflectionClass($priceType))->getShortName();
        $content .= Html::tag('span', $shortClass, ['class' => 'text-muted']);
        $content .= Html::tag('br');
        $content .= Html::tag('strong', Html::encode($priceType->type()->getName()));
        $content .= Html::tag('br');
        $content .= Html::encode($priceType->getDescription());

        return Html::tag('td', $content);
    }

    protected function renderUnitCell($priceType)
    {
        return Html::tag('td', Html::encode($priceType->getUnit()->name()));
    }

    protected function renderQuantityFormatterCell(PriceTypeDefinitionInterface $priceType)
    {
        $content = $this->formatQuantityFormatter($priceType);

        return Html::tag('td', $content);
    }

    protected function formatQuantityFormatter(PriceTypeDefinitionInterface $priceType)
    {
        $quantityFormatter = $priceType->getQuantityFormatterDefinition();

        if ($quantityFormatter === null) {
            return Html::tag('em', '‼️ No quantity formatter');
        }

        if ($quantityFormatter->formatterClass() !== null) {
            return $this->formatClassBasedQuantityFormatter($priceType, $quantityFormatter);
        }

        return Html::encode($quantityFormatter->getFractionUnit()->label);
    }

    protected function formatClassBasedQuantityFormatter(PriceTypeDefinitionInterface $priceType, $quantityFormatter)
    {
        $reflection = new \ReflectionClass($quantityFormatter->formatterClass());
        $formatterName = $reflection->getShortName();

        $quantity = $this->generateRandomQuantity($priceType);
        $example = $priceType->createQuantityFormatter(
            new FractionQuantityData($quantity, date('c'), 1)
        );

        return Html::tag('i', '[' . Html::encode($formatterName) . ']: ') . $example->format();
    }

    protected function renderRepresentationsCell($priceType)
    {
        $content = '';

        foreach ($this->representationsByClassAndPriceType as $className => $representations) {
            if (isset($representations[$priceType->type()->getName()])) {
                $representation = $representations[$priceType->type()->getName()];
                $reflection = new \ReflectionClass($representation);
                $content .= $this->renderRepresentationDetails($reflection->getShortName(), $representation->getSql());
            }
        }

        if ($content === '') {
            $content = Html::tag('em', '♦️ No custom representation');
            $content .= Html::tag('br');
            $content .= $this->renderRepresentationDetails('Default', self::DEFAULT_REPRESENTATION);
        }

        return Html::tag('td', $content);
    }

    protected function renderRepresentationDetails(string $className, string $representation)
    {
        $summary = Html::tag('summary', "↓&nbsp;" . Html::encode($className));
        $pre = Html::tag('pre', Html::encode($representation));

        return Html::tag('details', $summary . $pre, ['class' => 'representation-details']);
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
}
