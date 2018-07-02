<?php
namespace hipanel\modules\finance\tests\_support\Page\plan;

use hipanel\tests\_support\AcceptanceTester;
use hipanel\tests\_support\Page\Authenticated;

abstract class Plan extends Authenticated
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $client;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var string
     */
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
