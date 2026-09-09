<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Board Game Registry Loader
|--------------------------------------------------------------------------
| โหลดรายการเกมทั้งหมดจาก application/views/menugame.php
| หากต้องการเพิ่ม/แก้ไขเกม สามารถทำได้ที่ views/menugame.php
|--------------------------------------------------------------------------
*/

$menugame_file = APPPATH . 'views/menugame.php';
if (file_exists($menugame_file)) {
    $config['games'] = include $menugame_file;
} else {
    $config['games'] = [];
}
