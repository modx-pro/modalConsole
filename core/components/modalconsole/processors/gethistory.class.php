<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleGetHistoryProcessor extends modalConsoleProcessor
{
    public function process() {
        $key =  $this->getProperty('key');
        $code = empty($key) ? $this->history->getLastItem() : $this->history->getItem($key);

        return $this->response(true, '', ['code' => "<?php\n" . $code, 'keys' => $this->history->getKeys()]);
    }
}

return 'modalConsoleGetHistoryProcessor';