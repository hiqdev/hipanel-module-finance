<?php

declare(strict_types=1);


namespace hipanel\modules\finance\actions;

use Generator;
use hipanel\components\I18N;
use hipanel\modules\finance\helpers\LightPriceChargesEstimator;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hiqdev\yii2\export\exporters\ExporterFactoryInterface;
use hiqdev\yii2\export\exporters\ExportType;
use hiqdev\yii2\export\models\ExportJob;
use Yii;
use yii\base\Action;
use yii\i18n\Formatter;
use yii\web\Response;

/**
 * Class ExportPlanPricesAction exports the prices of a plan to a file.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class ExportPlanPricesAction extends Action
{
    private const array COLUMNS = [
        'Object name',
        'Sale time',
        'Buyer',
        'Object name',
        'Label',
        'Price type',
        'Included amount',
        'Unit',
        'Price',
        'Currency',
        'Formula',
        'Price with formula',
    ];

    private ?ExportJob $exportJob = null;
    private ?Formatter $formatter = null;

    public function __construct($id, $controller, readonly private ExporterFactoryInterface $exporterFactory, array $config = [])
    {
        parent::__construct($id, $controller, $config);
    }

    public function run(int $id): Response
    {
        $exporter = $this->exporterFactory->build(ExportType::CSV);
        $this->formatter = $exporter::applyExportFormatting();

        $exportJobKey = md5(implode('', [$this->controller->request->getAbsoluteUrl(), Yii::$app->user->id, time()]));
        $this->exportJob = ExportJob::findOrCreate($exportJobKey);
        $exporter->setExportJob($this->exportJob);

        $saver = $this->exportJob->getSaver();
        $exporter->exportToFile($saver->getFilePath(), [
            'data' => fn() => $this->generateRows($id),
        ]);

        return $this->controller->response->sendFile($saver->getFilePath(), $saver->getFilename());
    }

    public function __destruct()
    {
        $this->exportJob->delete();
    }

    /**
     * @param int $id
     * @return array{
     *     0: Sale[],
     *     1: Price[][],
     *     2: array
     * }
     */
    private function getPricesAndEstimates(int $id): array
    {
        $plan = Plan::find()
                    ->withPrices()
                    ->withSales()
                    ->byId($id)
                    ->andWhere(['show_deleted' => 1])
                    ->one();
        $grouper = new PlanInternalsGrouper($plan);
        [$salesByObject, $pricesByMainObject] = $grouper->group();

        $calculations = Plan::perform("calculate-values", [
            'times' => ["now"],
            'id' => $plan->id,
            'panel' => true,
        ]);
        $calculator = Yii::$container->get(LightPriceChargesEstimator::class, [$calculations]);
        $estimates = $calculator->calculateForPeriods(["now"]);
        $estimate = reset($estimates);

        return [$salesByObject, $pricesByMainObject, $estimate];
    }

    private function generateRows(int $id): Generator
    {
        yield self::COLUMNS;

        [$salesByObject, $pricesByMainObject, $estimate] = $this->getPricesAndEstimates($id);

        foreach ($pricesByMainObject as $mainObject => $prices) {
            $sale = $salesByObject[$mainObject] ?? null;
            foreach ($prices as $price) {
                yield [
                    $sale?->object,
                    $sale?->time,
                    $sale?->buyer,
                    I18N::removeLegacyLangTags($price->object->name),
                    I18N::removeLegacyLangTags($price->object->label),
                    $price->type,
                    $price->isOveruse()
                        ? $this->formatter->asDecimal($price->quantity)
                        : null,
                    $price->unit,
                    $this->formatter->asDecimal($price->price),
                    $price->currency,
                    str_replace("\n", '; ', $price->formula ?? ''),
                    $this->formatter->asDecimal(
                        $estimate['targets'][$price->object_id][$price->type]['sum'] ?? null
                    ),
                ];
            }
        }
    }
}
