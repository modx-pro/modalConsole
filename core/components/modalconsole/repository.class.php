<?php

class modalConsoleHistoryRepository
{
    /** @var array */
    public $items = [];
    /** @var string */
    protected $cachePath;
    /** @var integer */
    protected $limit;

    public function __construct($path, $limit)
    {
        $this->cachePath = $path;
        $this->limit = $limit;
        $file = $path . 'history.cache.php';
        if (is_file($file)) {
            $this->items = include $file;
        }
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
    }

    public function deleteItem($key)
    {
        if ($this->hasItem($key)) {
            unset($this->items[$key]);
        }
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
}