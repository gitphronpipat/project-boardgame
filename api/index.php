<?php

// =====================================================
// Vercel Serverless Entry Point สำหรับ CodeIgniter 3
// =====================================================

// โหลด .env (ถ้ามี - สำหรับ local dev)
$root = dirname(__DIR__);
if (file_exists($root . '/.env')) {
    $lines = file($root . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
        putenv(trim($key) . '=' . trim($value));
    }
}

// ---- Fix: Vercel ส่ง request มาที่ /api/index.php ----
// ต้อง rewrite URI ให้ CodeIgniter เข้าใจ
$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';

// ---- ตั้ง path ให้ CI ชี้ไป root ของโปรเจค ----
chdir($root);

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 */
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 */
switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;
    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

/*
 *---------------------------------------------------------------
 * SYSTEM / APPLICATION PATHS
 *---------------------------------------------------------------
 */
$system_path     = 'system';
$application_folder = 'application';
$view_folder = '';

// ---- Set the path ----
if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

if (($_temp = realpath($system_path)) !== FALSE) {
    $system_path = $_temp . DIRECTORY_SEPARATOR;
} else {
    $system_path = rtrim($system_path, '/\\') . DIRECTORY_SEPARATOR;
}

if (!is_dir($system_path)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly.';
    exit(3);
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', str_replace('\\', '/', $system_path));
define('FCPATH', str_replace(SELF, '', __FILE__));
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

if (is_dir($application_folder)) {
    if (($_temp = realpath($application_folder)) !== FALSE) {
        $application_folder = $_temp;
    } else {
        $application_folder = strtr(rtrim($application_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR);
    }
} elseif (is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
    $application_folder = BASEPATH . strtr(trim($application_folder, '/\\'), '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR);
} else {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your application folder path does not appear to be set correctly.';
    exit(3);
}

define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

if (!is_dir($view_folder)) {
    if (!empty($view_folder) && is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
        $view_folder = APPPATH . $view_folder;
    } elseif (!is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'Your view folder path does not appear to be set correctly.';
        exit(3);
    } else {
        $view_folder = APPPATH . 'views';
    }
}
define('VIEWPATH', rtrim($view_folder, '/\\') . DIRECTORY_SEPARATOR);

/*
 * ---------------------------------------------------------------
 *  Bootstrap CodeIgniter
 * ---------------------------------------------------------------
 */
require_once BASEPATH . 'core/CodeIgniter.php';
