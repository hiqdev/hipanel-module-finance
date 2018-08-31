<?php

namespace hipanel\modules\finance\cart\storage;

use hipanel\components\SettingsStorage;
use hipanel\helpers\StringHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\caching\CacheInterface;
use yii\helpers\Json;
use yii\web\MultiFieldSession;
use yii\web\Session;
use yii\web\User;

/**
 * Class RemoteCartStorage
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RemoteCartStorage extends MultiFieldSession implements CartStorageInterface
{
    /**
     * @var string The cart name. Used to distinguish carts, if there are different carts stored.
     * E.g. site, panel and mobile-app cart.
     */
    public $sessionCartId;

    const CACHE_DURATION = 60 * 60; // 1 hour
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

    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session, User $user, SettingsStorage $settingsStorage, CacheInterface $cache, array $config = [])
    {
        $this->settingsStorage = $settingsStorage;
        $this->cache = $cache;
        $this->user = $user;
        $this->session = $session;

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        if (empty($this->sessionCartId)) {
            throw new InvalidConfigException('Parameter "sessionCartId" must be set in RemoteCartStorage');
        }
    }

    protected function read()
    {
        try {
            $this->data = $this->cache->getOrSet($this->getCacheKey(), function () {
                $remoteCartData = $this->settingsStorage->getBounded($this->getStorageKey());
                if ($remoteCartData === []) {
                    return $this->session;
                } elseif (!is_string($remoteCartData)) {
                    Yii::warning('Remote cart data is neither empty array nor string. See: ' . var_export($remoteCartData, true));
                }

                $localCartData = $this->session[$this->sessionCartId] ?? null;
                return $this->mergedCartData($remoteCartData, $localCartData);
            }, self::CACHE_DURATION);
        } catch (\Exception $exception) {
            Yii::error('Failed to read cart: ' . $exception->getMessage(), __METHOD__);
        }
    }

    /**
     * @param string $remoteData base64 encoded JSON of serialized remotely stored cart items.
     * @param string $localData local cart items array. Defaults to `null`, meaning no local data exists
     * @return array
     */
    private function mergedCartData($remoteData, $localData = null)
    {
        $decodedRemote = Json::decode(base64_decode($remoteData));

        $local = $localData ? unserialize($localData) : [];
        $remote = isset($decodedRemote[$this->sessionCartId])
            ? unserialize($decodedRemote[$this->sessionCartId])
            : [];

        /** @noinspection AdditionOperationOnArraysInspection */
        return [$this->sessionCartId => serialize($remote + $local)];
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
