<?php

class modalConsoleHistory
{
    /** @var xPDOCacheManager */
    public $cacheManager;
    /** @var array */
    public $items = [];
    /** @var string */
    protected $cachePath;
    /** @var string */
    protected $userFolder;
    /** @var integer */
    protected $limit;

    public function __construct($cacheManager, array $config)
    {
        $this->cacheManager = $cacheManager;
        $this->cachePath = $config['cachePath'] . $config['userFolder'];
        $this->userFolder = $config['userFolder'];
        $this->limit = $config['limit'];
        $this->load();
    }

    public function load()
    {
        $file = $this->cachePath . 'history.cache.php';
        if (is_file($file)) {
            $this->items = include $file;
        }
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getItem($key, $default = '')
    {
        return isset($this->items[$key]) ? $this->items[$key] : $default;
    }

    public function addItem($key, $code)
    {
        $this->deleteItem($key);
        $this->items[$key] = $code;
        if ($this->count() > $this->limit) {
            $diff = $this->count() - $this->limit;
            $this->items = array_slice($this->items, $diff);
        }
        return $this;
    }

    public function deleteItem($key)
    {
        if ($this->hasItem($key)) {
            unset($this->items[$key]);
        }
        return $this;
    }

    public function hasItem($key)
    {
        return isset($this->items[$key]);

    }

    public function getLastItem($default = '')
    {
        end($this->items);
        return $this->getItem(key($this->items), $default);
    }

    public function getKeys()
    {
        return array_keys($this->items);
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function count()
    {
        return count($this->items);
    }

    public function save($force = false)
    {
        if ($this->limit > 0 || $force) {
            $options = array(
                xPDO::OPT_CACHE_KEY => $this->getUserFolder(),
            );
            $items = $this->getItems();
            return $this->getCacheManager()->set('history', $items, 0, $options);
        }
        return false;
    }

    public function clear()
    {
        $options = array(
            xPDO::OPT_CACHE_KEY => $this->getUserFolder(),
        );
        $this->items = [];
        return $this->getCacheManager()->set('history', $this->items, 0, $options);
    }

    public function getUserFolder()
    {
        return $this->userFolder;
    }

    public function getCachePath()
    {
        return $this->cachePath;
    }

    public function getCacheManager()
    {
        return $this->cacheManager;
    }
}