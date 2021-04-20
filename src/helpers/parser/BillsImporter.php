<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\forms\BillImportFromFileForm;
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
    private BillImportFromFileForm $fileForm;

    private ParserInterface $parser;

    public function __construct(BillImportFromFileForm $fileForm)
    {
        $this->fileForm = $fileForm;
        $this->parser = $this->createParser($fileForm->type, $fileForm->file);
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

    private function createParser(string $type, UploadedFile $file): ParserInterface
    {
        $map = [
            'deposit,epayservice' => ePayServiceParser::class,
            'deposit,paxum' => PaxumParser::class,
            'deposit,cardpay_dwgg' => CardPayParser::class,
            'deposit,paypal' => PayPalParser::class,
//            'deposit,dwgg_transferwise' => TransferWiseParser::class, // todo: add this parser
        ];
        if (!isset($map[$type])) {
            throw new NoParserAppropriateType(Yii::t('hipanel:finance', 'No parser appropriate type'));
        }

        return new $map[$type]($file);
    }

    private function createBill(ParserInterface $parser): Bill
    {
        $bill = new Bill(['scenario' => Bill::SCENARIO_CREATE]);
        $bill->client = $parser->getClient();
        $bill->type = $this->fileForm->type;
        $bill->time = $parser->getTime();
        $bill->currency = $parser->getCurrency();
        $bill->unit = $parser->getUnit();
        $bill->quantity = $parser->getQuantity();
        $bill->sum = $parser->getSum();
        $bill->txn = $parser->getTxn();
        $bill->label = $parser->getLabel();
        $charges = $this->createCharges($parser);
        $bill->populateRelation('charges', $charges);

        return $bill;
    }

    private function createCharges(ParserInterface $parser): array
    {
        $charges = [];
        if ($parser->getFee() !== null) {
            $charges[] = new Charge([
                'id' => 'fake_id',
                'type' => $this->fileForm->type, // todo: clarify fee type
                'sum' => number_format((float)$parser->getFee(), 2),
                'unit' => $parser->getUnit(),
                'currency' => $parser->getCurrency(),
                'time' => $parser->getTxn(),
                'quantity' => 1,
            ]);
        }

        return $charges;
    }

    private function resolveClients(array $bills): array
    {
        $clients = Client::find()->where(['logins' => ArrayHelper::getColumn($bills, 'client')])->limit(-1)->all();
        $clientsMap = array_combine(ArrayHelper::getColumn($clients, 'login'), ArrayHelper::getColumn($clients, 'id'));
        foreach ($bills as $bill) {
            $bill->client_id = $clientsMap[$bill->client] ?? null;
        }

        return array_filter($bills, static fn($bill) => $bill->client_id !== null);
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
