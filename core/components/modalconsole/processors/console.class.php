<?php
require_once dirname(dirname(__FILE__)) . '/modalConsoleHistory.php';

abstract class modalConsoleProcessor extends modProcessor
{
    public $permission = 'console';
    public $code;
    public $limit;
    protected $cachePath;
    /** @var  modalConsoleHistory $history History repository */
    protected $history;

    public function checkPermissions()
    {
        if(!$this->modx->hasPermission($this->permission)){
            return  false;
        }
        return true;
    }

    public function initialize()
    {
        $this->code = preg_replace('/^\s*<\?(php)?\s*/mi', '', $this->getProperty('code', ''));
        $this->code = trim($this->code);
        $this->limit = $this->getProperty('limit', $this->modx->getOption('modalconsole_history_limit', null, 20));
        $this->cachePath = $this->modx->getOption('modalconsole_core_path', NULL, $this->modx->getOption('core_path') . 'cache/');
        if (!class_exists('modalConsoleHistory')) {
            $errormsg = 'Class "modalConsoleHistory" is not found!';
            $this->modx->log(1, $errormsg);
            return $errormsg;
        }
        $this->history = new modalConsoleHistory($this->modx->getCacheManager(), ['cachePath' => $this->cachePath, 'userFolder' => $this->getUserFolder('modal_console/'), 'limit'=> $this->limit]);

        return true;
    }

    public function response($success, $message = '', $data = [])
    {
        $result = [
            'success' => $success,
            'message' => $message,
        ];
        return json_encode(array_merge($result, $data));
    }

    public function getUserFolder($prefix = '', $postfix = DIRECTORY_SEPARATOR)
    {
        return $prefix . md5('modalconsole' . $this->modx->user->id) . $postfix;
    }
}