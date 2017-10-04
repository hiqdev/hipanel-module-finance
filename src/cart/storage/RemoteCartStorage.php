<?php

namespace hipanel\modules\finance\cart\storage;

use hipanel\components\SettingsStorage;
use hipanel\helpers\StringHelper;
use Yii;
use yii\base\NotSupportedException;
use yii\caching\CacheInterface;
use yii\helpers\Json;
use yii\web\MultiFieldSession;
use yii\web\User;

/**
 * Class RemoteCartStorage
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RemoteCartStorage extends MultiFieldSession implements CartStorageInterface
{
    const CACHE_DURATION = 60*60; // 1 hour
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var array
     */
    protected $oldData = [];
    /**
     * @var SettingsStorage
     */
    private $settingsStorage;
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user, SettingsStorage $settingsStorage, CacheInterface $cache, array $config = [])
    {
        parent::__construct($config);

        $this->settingsStorage = $settingsStorage;
        $this->cache = $cache;
        $this->user = $user;
    }

    protected function read()
    {
        try {
            $this->data = $this->cache->getOrSet($this->getCacheKey(), function () {
                $data = $this->settingsStorage->getBounded($this->getStorageKey());
                if ($data === []) {
                    return [];
                }

                return Json::decode(base64_decode($data));
            }, self::CACHE_DURATION);
        } catch (\Exception $exception) {
            Yii::error('Failed to read cart: ' . $exception->getMessage(), __METHOD__);
        }
    }

    protected function getCacheKey()
    {
        return [__CLASS__, $this->user->getId(), session_id()];
    }

    protected function getStorageKey()
    {
        return StringHelper::basename(__CLASS__);
    }

    protected function write()
    {
        if ($this->data === $this->oldData) {
            return;
        }

        try {
            $this->cache->set($this->getCacheKey(), $this->data);
            $this->settingsStorage->setBounded($this->getStorageKey(), base64_encode(Json::encode($this->data)));
        } catch (\Exception $exception) {
            Yii::error('Failed to save cart: ' . $exception->getMessage(), __METHOD__);
        }
    }

    /**
     * @var bool
     */
    private $_isActive;
    public function getIsActive()
    {
        return $this->_isActive;
    }

    /** {@inheritdoc} */
    public function open()
    {
        if ($this->getIsActive()) {
            return;
        }

        $this->read();
        $this->oldData = $this->data;
        $this->_isActive = true;
        $this->registerShutdownFunction();
    }

    /** {@inheritdoc} */
    public function offsetExists($offset)
    {
        $this->open();

        return isset($this->data[$offset]);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset)
    {
        $this->open();

        return $this->data[$offset] ?? null;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $item)
    {
        $this->open();

        $this->data[$offset] = $item;
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset)
    {
        $this->open();
        unset($this->data[$offset]);
    }

    /**
     * @throws NotSupportedException
     * @void
     */
    private function throwShouldNotBeCalledException()
    {
        throw new NotSupportedException('Remote cart storage extends yii\web\Session, but it is not a session actually. This method should be never called.');
    }

    /** {@inheritdoc} */
    public function regenerateID($deleteOldSession = false)
    {
        $this->throwShouldNotBeCalledException();
    }

    /** {@inheritdoc} */
    public function readSession($id)
    {
        return $this->throwShouldNotBeCalledException();
    }

    /** {@inheritdoc} */
    public function writeSession($id, $data)
    {
        return $this->throwShouldNotBeCalledException();
    }

    /** {@inheritdoc} */
    public function destroySession($id)
    {
        return $this->throwShouldNotBeCalledException();
    }

    /** {@inheritdoc} */
    public function gcSession($maxLifetime)
    {
        return $this->throwShouldNotBeCalledException();
    }

    private function registerShutdownFunction()
    {
        register_shutdown_function(\Closure::fromCallable([$this, 'write']));
    }
}
