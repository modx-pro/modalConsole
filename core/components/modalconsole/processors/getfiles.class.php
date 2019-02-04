<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleGetFilesProcessor extends modalConsoleProcessor
{
    public function process() 
    {
        if (!$path = realpath($this->modx->getOption('modalconsole_files_path', NULL, $this->modx->getOption('core_path') . 'components/modalconsole/files/'))) {
            return $this->response(false, $this->modx->lexicon('modalconsole_err_path_nf'));
        }

        $files = array();

        foreach(glob($path . '/*.php') as $file){
            $files[] = ['filename' => basename($file)];
        }
        
        return $this->outputArray($files);
    }
}

return 'modalConsoleGetFilesProcessor';