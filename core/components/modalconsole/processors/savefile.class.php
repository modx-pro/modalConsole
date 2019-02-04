<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleSaveFileProcessor extends modalConsoleProcessor
{
    public function process()
    {
        $code = trim($this->getProperty('code',''));
        $fileName = basename(trim($this->getProperty('filename','')), '.php');

        if (empty($fileName)) {
            return $this->response(false, $this->modx->lexicon('modalconsole_err_file_ns'));
        }
        $path = realpath($this->modx->getOption('modalconsole_files_path', NULL, $this->modx->getOption('core_path') . 'components/modalconsole/files/'));
        $file = $path . DIRECTORY_SEPARATOR . $fileName . '.php';

        if ($code) {
            if (!file_put_contents($file, $code)) {
                return $this->response(false, $this->modx->lexicon('modalconsole_err_save_file'));
            }
        }
        
        return $this->response(true, '', ['filename' => $fileName]);
    }
}

return 'modalConsoleSaveFileProcessor';