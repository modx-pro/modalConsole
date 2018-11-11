<?php
require_once dirname(dirname(__FILE__)) . '/repository.class.php';

abstract class modalConsoleProcessor extends modProcessor
{
    public $permission = 'console';
    public $code;
    public $limit;
    protected $cachePath;
    /** @var  modalConsoleHistoryRepository $history History repository */
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
        $this->cachePath = $this->modx->getOption('modalconsole_core_path', NULL, $this->modx->getOption('core_path') . 'cache/') . $this->getUserFolder('modal_console/');
        if (!class_exists('modalConsoleHistoryRepository')) {
            $this->modx->log(1, 'Class "modalConsoleHistoryRepository" is not found!');
            return 'Class "modalConsoleHistoryRepository" is not found!';
        }
        $this->history = new modalConsoleHistoryRepository($this->cachePath, $this->limit);

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