<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleLoadFileProcessor extends modalConsoleProcessor{
    
    public function process() {
        $filename = basename(trim($this->getProperty('file','')));
        
        if (empty($filename)) {
            return $this->failure($this->modx->lexicon('modalconsole_err_file_ns'));
        }
        if (!$path = realpath($this->modx->getOption('modalconsole_files_path', NULL, $this->modx->getOption('core_path') . 'components/modalconsole/files/'))) {
            return $this->failure($this->modx->lexicon('modalconsole_err_path_nf'));
        }
        
        $file = $path. DIRECTORY_SEPARATOR . $filename;
        
        $code = '';
        if (file_exists($file)) {
            $code = @file_get_contents($file);
        } else {
            return $this->failure($this->modx->lexicon('modalconsole_err_file_nf'));
        }

        return $this->success($code);
    }
}

return 'modalConsoleLoadFileProcessor';