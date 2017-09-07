<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use yii\base\InvalidConfigException;

/**
 * Class Purchase.
 */
abstract class AbstractPurchase extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /**
     * @var AbstractCartPosition
     */
    public $position;

    /**
     * @var array result of purchase execution
     */
    protected $_result;

    public function getResult()
    {
        return $this->_result;
    }

    /** {@inheritdoc} */
    public static function tableName()
    {
        throw new InvalidConfigException('Method "tableName" must be declared');
    }

    /**
     * @var string operation to be performed, e.g.: Renew, Transfer, Registration
     */
    public static function operation()
    {
        throw new InvalidConfigException('Method "operation" must be declared');
    }

    /** {@inheritdoc} */
    public function init()
    {
        parent::init();

        $this->calculation_id = $this->position->getId();
        $this->amount = $this->position->getQuantity();
    }

    /**
     * Executes the purchase.
     * Calls proper API commands to purchase the product.
     * @throws ErrorPurchaseException in case of failed purchase
     * @return true if the item was purchased successfully
     */
    public function execute()
    {
        if ($this->validate()) {
            $this->_result = static::perform(static::operation(), $this->getAttributes());
            return true;
        }

        return false;
    }

    public function renderNotes()
    {
        return '';
    }

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['calculation_id', 'object', 'client', 'type', 'currency', 'item'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @return string[]
     */
    public function getPurchasabilityRules()
    {
        return [];
    }
}
