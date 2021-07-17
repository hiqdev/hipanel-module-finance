<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\forms\BillImportFromFileForm;
use hipanel\modules\finance\helpers\BillImportFromFileHelper;
use hipanel\modules\finance\helpers\parser\parsers\CardPayParser;
use hipanel\modules\finance\helpers\parser\parsers\ePayServiceParser;
use hipanel\modules\finance\helpers\parser\parsers\ParserInterface;
use hipanel\modules\finance\helpers\parser\parsers\PaxumParser;
use hipanel\modules\finance\helpers\parser\parsers\PayPalParser;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use Money\Currency;
use Money\Money;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class BillsImporter
{
    private ParserInterface $parser;

    private BillImportFromFileHelper $requisiteToTypes;

    public function __construct(BillImportFromFileForm $fileForm)
    {
        $requisite_id = $fileForm->requisite_id ? (int) $fileForm->requisite_id : null;
        $this->requisiteToTypes = new BillImportFromFileHelper($requisite_id);
        $this->parser = $this->createParser($this->requisiteToTypes->getRequisiteType(), $fileForm->file);
    }

    public function __invoke(): array
    {
        $bills = [];
        foreach ($this->parser->getRows() as $parser) {
            $bills[] = $this->createBill($parser);
        }
        if (empty($bills)) {
            return $bills;
        }
        $bills = $this->resolveClients($bills);

        return $this->filterExisting(array_splice($bills, 0, 20));
    }

    public function getClientSubstrings(): ?array
    {
        return $this->requisiteToTypes->getClientSubstrings();
    }

    private function createParser(string $type, UploadedFile $file): ParserInterface
    {
        $map = [
            'epayservice' => ePayServiceParser::class,
            'paxum' => PaxumParser::class,
            'cardpay_dwgg' => CardPayParser::class, // TODO: create and left only type cardpay
            'paypal' => PayPalParser::class,
//            'dwgg_transferwise' => TransferWiseParser::class, // todo: add this parser
        ];
        if (!isset($map[$type])) {
            throw new NoParserAppropriateType(Yii::t('hipanel:finance', 'No parser appropriate type'));
        }

        return new $map[$type]($file, $this);
    }

    private function createBill(ParserInterface $parser): Bill
    {
        $bill = new Bill(['scenario' => Bill::SCENARIO_CREATE]);
        $bill->client = $parser->getClient();
        $bill->type = $this->requisiteToTypes->getDepositType();
        $bill->time = $parser->getTime();
        $bill->currency = $parser->getCurrency();
        $bill->unit = $parser->getUnit();
        $bill->quantity = $parser->getQuantity();
        $bill->sum = $parser->getNet();
        $bill->txn = $parser->getTxn();
        $bill->label = $parser->getLabel();
        $bill->requisite_id = $this->requisiteToTypes->getRequisiteID();
        $bill = $this->resolveClient($bill);
        $charges = $this->createCharges($parser, $bill);
        $bill->populateRelation('charges', $charges);

        return $bill;
    }

    private function createCharges(ParserInterface $parser, Bill $bill): array
    {
        foreach (['deposit', 'fee'] as $attribute) {
            $charges[] = new Charge([
                'id' => "fake_id_{$attribute}",
                'object_id' => $bill->client_id,
                'type' => $attribute === 'fee'
                    ? $this->requisiteToTypes->getFeeType()
                    : $this->requisiteToTypes->getDepositType(),
                'sum' => -1 * number_format((float) ($attribute === 'fee' ? $parser->getFee() : $parser->getSum()), 2),
                'unit' => $parser->getUnit(),
                'currency' => $parser->getCurrency(),
                'time' => $parser->getTime(),
                'quantity' => 1,
            ]);
        }

        return $charges;
    }

    private function resolveClients(array $bills): array
    {
        return array_filter($bills, static fn($bill) => $bill->client_id !== null);
    }

    private function resolveClient(Bill $bill): Bill
    {
        $client = Yii::$app->cache->getOrSet([__CLASS__, __METHOD__, $bill->client], function() use ($bill) {
                return Client::find()->where(['login' => $bill->client])->one();
        }, 3600);

        $bill->client_id = $client->id ?? null;

        return $bill;
    }

    private function filterExisting(array $bills): array
    {
        $exists = Bill::batchPerform('find-multiple', ArrayHelper::toArray($bills));

        return array_filter($bills, static function (Bill $bill) use ($exists): bool {
            $leave = true;
            foreach ($exists as $row) {
                if (isset($row['txn']) && $row['txn'] === $bill->txn) {
                    $leave = false;
                    break;
                }
                $currency = new Currency($bill->currency);
                $billSum = new Money($bill->sum * 100, $currency);
                $rowSum = new Money($row['sum'], $currency);
                if ($bill->client_id === $row['client_id'] && $billSum->equals($rowSum)) {
                    $leave = false;
                    break;
                }
            }

            return $leave;
        });
    }
}
