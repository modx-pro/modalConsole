<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleGetFilesProcessor extends modalConsoleProcessor
{
    public function process() {
        $path = $this->modx->getOption('modalconsole_core_path', NULL, $this->modx->getOption('core_path') . 'components/modalconsole/').'files';
        $files = array();
        
        $this->incl($path, '', $files);
        
        return $this->success($files);
    }
    
    protected function incl($path, $dir = '', array & $files){
        foreach(glob($path . DIRECTORY_SEPARATOR. $dir . DIRECTORY_SEPARATOR. '*') as $val){
            $filename = basename($val);
            
            if(is_dir($val)){
                $this->incl($path, $dir . DIRECTORY_SEPARATOR . basename($val), $files);
            }
            else if(preg_match('/\.php$/', $val)){
                $filename = $dir . DIRECTORY_SEPARATOR . basename($val);
                $files[] = trim($filename, '/');
            }
        }
    }
}

return 'modalConsoleGetFilesProcessor';