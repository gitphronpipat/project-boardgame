<?php
defined('BASEPATH') or exit('No direct script access allowed');

function activity_log($step, $data = [], $error = null)
{
    try {
        $log_dir  = dirname(dirname(__DIR__)) . '/logs/';
        $log_file = $log_dir . 'activity_' . date('Y-m-d') . '.json';

        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $existing = [];
        if (file_exists($log_file)) {
            $raw      = file_get_contents($log_file);
            $existing = json_decode($raw, true) ?? [];
        }

        $existing[] = [
            'time'  => date('H:i:s'),
            'step'  => $step,
            'data'  => $data,
            'error' => $error,
        ];

        file_put_contents($log_file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

    } catch (Exception $e) {
        error_log('activity_log failed: ' . $e->getMessage());
    }
}
