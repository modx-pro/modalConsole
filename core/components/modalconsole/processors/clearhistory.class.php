<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleClearHistoryProcessor extends modalConsoleProcessor
{
    public function process() {
        $options = array(
            xPDO::OPT_CACHE_KEY => $this->getUserFolder('modal_console/'),
        );
        $items = [];
        $this->modx->getCacheManager()->set('history', $items, 0, $options);

        return $this->response(true);
    }
}

return 'modalConsoleClearHistoryProcessor';