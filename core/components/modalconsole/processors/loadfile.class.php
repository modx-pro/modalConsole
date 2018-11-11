<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleLoadFileProcessor extends modalConsoleProcessor{
    
    public function process() {
        $file = trim($this->getProperty('file',''));
        
        if (empty($file)) return $this->failure($this->modx->lexicon('modalconsole_err_file_ns'));
        
        $path = $this->modx->getOption('modalconsole_files_path', NULL, $this->modx->getOption('core_path') . 'components/modalconsole/files/');
        
        $f = $path . $file; 
        
        $code = '';
        if (file_exists($f)) {
            $code = @file_get_contents($f);
        } else {
            return $this->failure($this->modx->lexicon('modalconsole_err_file_nf'));
        }

        return $this->success($code);
    }
}

return 'modalConsoleLoadFileProcessor';