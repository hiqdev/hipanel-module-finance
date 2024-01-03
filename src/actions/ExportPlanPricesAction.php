<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Box\Spout\Common\Type;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;
use hipanel\components\I18N;
use hipanel\modules\finance\helpers\LightPriceChargesEstimator;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hiqdev\yii2\export\models\SaveManager;
use Yii;
use yii\base\Action;

/**
 * Class ExportPlanPricesAction exports the prices of a plan to a file.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class ExportPlanPricesAction extends Action
{
    private const COLUMNS = [
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

    private ?SaveManager $saver;

    public function run(int $id)
    {
        $format = Type::CSV; // TODO: Support other formats?
        $this->saver = new SaveManager(md5($this->controller->request->getAbsoluteUrl()));

        $writer = $this->createWriter($format);
        $this->fillWriter($id, $writer);
        $writer->close();

        $filename = implode('.', ['report_' . time(), $format]);

        return $this->controller->response->sendFile($this->saver->getFilePath(), $filename);
    }

    public function __destruct()
    {
        $this->saver?->delete();
    }

    private function createWriter(string $format): WriterInterface
    {
        $writer = WriterEntityFactory::createWriter($format);
        $writer->openToFile($this->saver->getFilePath());

        return $writer;
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

    private function fillWriter(int $id, WriterInterface $writer): void
    {
        $rows = [self::COLUMNS];

        [$salesByObject, $pricesByMainObject, $estimate] = $this->getPricesAndEstimates($id);

        foreach ($pricesByMainObject as $mainObject => $prices) {
            $sale = $salesByObject[$mainObject] ?? null;
            foreach ($prices as $price) {
                $rows[] = [
                    $sale?->object,
                    $sale?->time,
                    $sale?->buyer,
                    I18N::removeLegacyLangTags($price->object->name),
                    I18N::removeLegacyLangTags($price->object->label),
                    $price->type,
                    $price->isOveruse() ? $price->quantity : null,
                    $price->unit,
                    $price->price,
                    $price->currency,
                    str_replace("\n", '; ', $price->formula ?? ''),
                    $estimate['targets'][$price->object_id][$price->type]['sum'] ?? null,
                ];
            }
        }

        $rows = array_map(fn($row) => WriterEntityFactory::createRowFromArray($row), $rows);
        $writer->addRows($rows);
    }
}
