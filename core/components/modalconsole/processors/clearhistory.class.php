<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleClearHistoryProcessor extends modalConsoleProcessor
{
    public function process() {
        $result = $this->history->clear();
        return $this->response($result, $result ? '' : 'Clear history error!');
    }
}

return 'modalConsoleClearHistoryProcessor';