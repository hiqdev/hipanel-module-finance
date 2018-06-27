<?php
namespace hipanel\modules\finance\tests\_support\Page\plan;

use hipanel\tests\_support\AcceptanceTester;
use hipanel\tests\_support\Page\Authenticated;

abstract class Plan extends Authenticated
{
    protected $name;

    protected $type;

    protected $client;

    protected $currency;

    protected $note;

    protected $id;

    public function __construct(AcceptanceTester $I, $fields = null)
    {
        parent::__construct($I);

        if ($fields) {
            $this->name = $fields['name'];
            $this->type = $fields['type'];
            $this->client = $fields['client'];
            $this->currency = $fields['currency'];
            $this->note = $fields['note'];
        }
    }

}
