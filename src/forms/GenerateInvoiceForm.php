<?php
declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use yii\base\Model;

class GenerateInvoiceForm extends Model
{
    public ?int $id = null;
    public ?int $object_id = null;
    public ?int $client_id = null;
    public ?int $seller_id = null;
    public ?int $receiver_id = null;
    public ?int $sender_id = null;
    public ?string $type = null;
    public ?string $validity_start = null;
    public ?string $validity_end = null;
    public ?string $filename = null;
    public string|array|null $data = null;
    public bool $save = false;

    public function rules()
    {
        return [
            [['id', 'object_id', 'client_id', 'seller_id', 'sender_id'], 'integer'],
            [['type', 'validity_start', 'validity_end', 'filename'], 'string'],
            ['data', 'safe'],
            ['save', 'boolean'],
        ];
    }
}
