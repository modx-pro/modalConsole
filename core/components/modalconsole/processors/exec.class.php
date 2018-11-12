<?php
require_once __DIR__ . '/console.class.php';

class modalConsoleExecProcessor extends modalConsoleProcessor
{
    public $code;
    protected $queries = [];
    protected $queryTime = [];
    protected $memory = [];
    protected $memoryPeak = [];
    protected $time = [];

    public function process()
    {
        $this->code = preg_replace('/^\s*<\?(php)?\s*/mi', '', $this->getProperty('code', ''));
        error_reporting(E_ALL);
        ini_set("display_errors", true);
        $modx = $this->modx;

        ob_start();
        $this->snapshot('before');
        /*try {
            $result = eval($this->code);
        } catch (ParseError $e) {
            return $this->response(false, $e->getMessage());
        }*/
        $result = eval($this->code);
        $this->snapshot('after');
        $output = ob_get_contents();
        ob_end_clean();

        if ($result === false) {
            return $this->response(false, error_get_last());
        } elseif (!empty($result)) {
            $output = $result;
        }

        if ($this->getProperty('save', false)) {

            $this->history->addItem(md5($this->code), $this->code)->save();
        }

        return $this->response(true, '', array_merge(['output' => $output, 'keys' => $this->history->getKeys()], ['profile' => $this->prepareResult()]));
    }

    public function snapshot($key)
    {
        $this->queries[$key] = isset($this->modx->executedQueries) ? $this->modx->executedQueries : 0;
        $this->queryTime[$key] = $this->modx->queryTime;
        $this->memoryPeak[$key] = memory_get_peak_usage(true);
        $this->memory[$key] = memory_get_usage(true);
        $this->time[$key] = microtime(true);
    }

    public function prepareResult()
    {
        $totalTime = round($this->getDiff($this->time), 3);
        $sqlTime = $this->getDiff($this->queryTime);

        $result = [
            "queries" => $this->getDiff($this->queries),
            //"SQL time" => sprintf("%2.3f s", $sqlTime),
            "time" => sprintf("%2.3f s / %2.3f s / %2.3f s", $sqlTime, abs($totalTime - $sqlTime), $totalTime),
//            "totaltime" => sprintf("%2.3f s", $totalTime),
            "memory" => sprintf("%2.3f MB / %2.3f MB", $this->getDiffMemory($this->memory), $this->getDiffMemory($this->memoryPeak)),
        ];
        return $result;
    }

    protected function getDiff($prop)
    {
        return $prop['after'] - $prop['before'];
    }

    protected function getDiffMemory($prop)
    {
        return round(($prop['after'] - $prop['before']) / 1048576, 2);
    }
}

return 'modalConsoleExecProcessor';
