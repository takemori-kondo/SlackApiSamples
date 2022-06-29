<?php
// PHP Version 8.1

function logInner($level, $msg) {
    $datetime = date('c');
    $bt = debug_backtrace();
    $nestCount = 1;
    error_log($level.' '.$datetime.' '.pathinfo($bt[$nestCount]['file'], PATHINFO_BASENAME).' '.$bt[$nestCount]['line'].' - '.$msg);
}
function logInfo($msg)  { logInner('INFO ', $msg); }
function logError($msg) { logInner('ERROR', $msg); }
function logErrorAndExit($msg) {
    logError($msg);
    exit($msg);
}