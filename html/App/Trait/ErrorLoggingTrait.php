<?php

trait ErrorLoggingTrait {
    public function logError($message) {
        $logFile = __DIR__ . '/../Log/log.txt';

        // Obtém a data e hora atual
        $timestamp = date('[Y-m-d H:i:s]');

        // Formata a mensagem de erro
        $errorMessage = $timestamp . ' ' . $message . PHP_EOL;

        // Registra a mensagem de erro no arquivo de log
        error_log($errorMessage, 3, $logFile);
    }
}