<?php

if (!function_exists('modalConsoleEval')) {
    /**
     * Process the code.
     * @param modX $modx
     * @param string $code.
     * @return string
     */
    function modalConsoleEval($modx, $code)
    {
        return eval($code);
    }
}