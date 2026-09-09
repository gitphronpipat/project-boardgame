<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Game_model — จัดการข้อมูลเกมทั้งหมด
 * ดึงข้อมูลจาก application/config/games.php
 */
class Game_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->config->load('games', TRUE);
    }

    /**
     * ดึงรายการเกมทั้งหมดจาก application/views/menugame.php
     * @return array
     */
    public function get_all()
    {
        $file = APPPATH . 'views/menugame.php';
        if (file_exists($file)) {
            $games = include $file;
            if (is_array($games)) {
                return $games;
            }
        }
        $games = $this->config->item('games', 'games');
        return !empty($games) ? $games : [];
    }

    /**
     * ดึงข้อมูลเกมตาม key
     * @param string $key
     * @return array|null
     */
    public function get_by_key($key)
    {
        $games = $this->get_all();
        return isset($games[$key]) ? $games[$key] : null;
    }

    /**
     * ดึงรายการเกมตามสถานะ (เช่น 'ready' หรือ 'coming_soon')
     * @param string $status
     * @return array
     */
    public function get_by_status($status)
    {
        $games = $this->get_all();
        return array_filter($games, function ($game) use ($status) {
            return isset($game['status']) && $game['status'] === $status;
        });
    }

    /**
     * ดึงรายการเกมตามหมวดหมู่
     * @param string $category
     * @return array
     */
    public function get_by_category($category)
    {
        $games = $this->get_all();
        return array_filter($games, function ($game) use ($category) {
            return isset($game['category']) && $game['category'] === $category;
        });
    }
}
