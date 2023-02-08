<?php
declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use DateTimeImmutable;
use DOMAttr;
use DOMDocument;
use yii\base\Model;
use Yii;
use yii\web\UploadedFile;

class CdbExportForm extends Model
{
    public ?UploadedFile $file = null;

    public function rules()
    {
        return [
            [['file'], 'required'],
            [
                'file', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => ['csv'],
                'maxSize' => 1 * 1024 * 1024,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('hipanel:finance', 'CSV File with employees'),
        ];
    }

    public function convert(): string|bool
    {
        $ts = new DateTimeImmutable();
        $employees = array_map('str_getcsv', file($this->file->tempName));
        array_walk($employees, static function (&$r) use ($employees) {
            $r = array_combine($employees[0], $r);
        });
        array_shift($employees); # remove column header
        $firstRow = reset($employees);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $document = $dom->createElement('Document');
        $document->setAttributeNode(new DOMAttr('xsi:schemaLocation',
            'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03 pain.001.001.03.xsd'));
        $document->setAttributeNode(new DOMAttr('xmlns', 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03'));
        $document->setAttributeNode(new DOMAttr('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance'));
        $customer = $dom->createElement('CstmrCdtTrfInitn');
        $header = $dom->createElement('GrpHdr');
        $customer->appendChild($header);

        // Header
        $header->appendChild($dom->createElement('MsgId', $firstRow['MsgId']));
        $header->appendChild($dom->createElement('CreDtTm', $ts->format("Y-m-d\\TH:i:s")));
        $header->appendChild($dom->createElement('NbOfTxs', (string)count($employees)));
        $header->appendChild($dom->createElement('CtrlSum', (string)array_sum(array_column($employees, 'InstdAmt'))));
        $InitgPty = $dom->createElement('InitgPty');
        $InitgPty->appendChild($dom->createElement('Nm', $firstRow['Nm1']));
        $header->appendChild($InitgPty);

        foreach ($employees as $employee) {
            unset($employee['CreDtTm']);
            $rowIsNotEmpty = array_sum(array_map(static fn($cell) => $cell !== '', $employee)) === count($employee);
            if (!$rowIsNotEmpty) {
                break;
            }

            $info = $dom->createElement('PmtInf');

            // Info
            $info->appendChild($dom->createElement('PmtInfId', $employee['PmtInfId']));
            $info->appendChild($dom->createElement('PmtMtd', $employee['PmtMtd']));
            $PmtTpInf = $dom->createElement('PmtTpInf');
            $SvcLvl = $dom->createElement('SvcLvl');
            $SvcLvl->appendChild($dom->createElement('Cd', $employee['Cd']));
            $PmtTpInf->appendChild($SvcLvl);
            $info->appendChild($PmtTpInf);
            $info->appendChild($dom->createElement('ReqdExctnDt', $ts->modify('+1 day')->format('Y-m-d')));
            $info->appendChild($dom->createElement('Dbtr'));
            $DbtrAcct = $dom->createElement('DbtrAcct');
            $Id = $dom->createElement('Id');
            $Id->appendChild($dom->createElement('IBAN', $employee['IBAN1']));
            $DbtrAcct->appendChild($Id);
            $info->appendChild($DbtrAcct);
            $DbtrAgt = $dom->createElement('DbtrAgt');
            $FinInstnId = $dom->createElement('FinInstnId');
            $FinInstnId->appendChild($dom->createElement('BIC', $employee['BIC1']));
            $DbtrAgt->appendChild($FinInstnId);
            $info->appendChild($DbtrAgt);
            $info->appendChild($dom->createElement('ChrgBr', $employee['ChrgBr']));
            $CdtTrfTxInf = $dom->createElement('CdtTrfTxInf');
            $PmtId = $dom->createElement('PmtId');
            $PmtId->appendChild($dom->createElement('EndToEndId', $employee['EndToEndId']));
            $CdtTrfTxInf->appendChild($PmtId);
            $Amt = $dom->createElement('Amt');
            $InstdAmt = $dom->createElement('InstdAmt', $employee['InstdAmt']);
            $InstdAmt->setAttributeNode(new DOMAttr('Ccy', 'EUR'));
            $Amt->appendChild($InstdAmt);
            $CdtTrfTxInf->appendChild($Amt);
            $CdtrAgt = $dom->createElement('CdtrAgt');
            $FinInstnId = $dom->createElement('FinInstnId');
            $FinInstnId->appendChild($dom->createElement('BIC', $employee['BIC2']));
            $CdtrAgt->appendChild($FinInstnId);
            $CdtTrfTxInf->appendChild($CdtrAgt);
            $Cdtr = $dom->createElement('Cdtr');
            $Cdtr->appendChild($dom->createElement('Nm', $employee['Nm2']));
            $PstlAdr = $dom->createElement('PstlAdr');
            $PstlAdr->appendChild($dom->createElement('StrtNm', $employee['StrtNm']));
            $PstlAdr->appendChild($dom->createElement('TwnNm', $employee['TwnNm']));
            $PstlAdr->appendChild($dom->createElement('AdrLine', $employee['AdrLine']));
            $Cdtr->appendChild($PstlAdr);
            $CdtTrfTxInf->appendChild($Cdtr);
            $CdtrAcct = $dom->createElement('CdtrAcct');
            $Id = $dom->createElement('Id');
            $Id->appendChild($dom->createElement('IBAN', $employee['IBAN2']));
            $CdtrAcct->appendChild($Id);
            $CdtTrfTxInf->appendChild($CdtrAcct);
            $RmtInf = $dom->createElement('RmtInf');
            $RmtInf->appendChild($dom->createElement('Ustrd', $employee['Ustrd']));
            $CdtTrfTxInf->appendChild($RmtInf);


            $info->appendChild($CdtTrfTxInf);
            $customer->appendChild($info);
        }
        $document->appendChild($customer);
        $dom->appendChild($document);

        return $dom->saveXML();
    }
}
