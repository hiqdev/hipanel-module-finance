<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\modules\client\models\Contact;

/**
 * Class Requisite.
 *
 * @property Requisite[] $localizations
 * @property int|string $id
 * @property bool $gdpr_consent
 * @property bool $policy_consent
 */
class Requisite extends Contact
{
    /*
     * @return array the list of attributes for this record
     */
    use \hipanel\base\ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'client_id', 'recipient_id'], 'required', 'on' => ['reserve-number']],
        ]);
    }

    public function isRequisite()
    {
        return (boolean) $this->is_requisite;
    }
}
