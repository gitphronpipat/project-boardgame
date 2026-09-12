<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Minesweeper_model — จัดการตรรกะเกม Minesweeper และการตั้งค่าหลังบ้าน
 * รองรับทั้งเล่นคนเดียว (Solo) และเล่นหลายคน 1-4 คน (Versus, Co-op, Flag Hunter)
 */
class Minesweeper_model extends CI_Model
{
    /**
     * การตั้งค่าระดับความยากของแต่ละโหมด (สามารถปรับขนาดตารางและจำนวนระเบิดได้ที่นี่)
     */
    public $difficulty_settings = [
        'easy' => [
            'name'  => 'ง่าย (Easy)',
            'rows'  => 9,
            'cols'  => 9,
            'mines' => 10,
        ],
        'medium' => [
            'name'  => 'ปานกลาง (Medium)',
            'rows'  => 16,
            'cols'  => 16,
            'mines' => 40,
        ],
        'hard' => [
            'name'  => 'ยาก (Hard)',
            'rows'  => 16,
            'cols'  => 30,
            'mines' => 99,
        ],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ดึงการตั้งค่าความยาก
     */
    public function get_difficulty_config($diff = 'easy')
    {
        if (isset($this->difficulty_settings[$diff])) {
            return $this->difficulty_settings[$diff];
        }
        return $this->difficulty_settings['easy'];
    }

    /**
     * สร้างสถานะเริ่มต้นของห้องเกม
     */
    public function create_initial_room($room_id, $host, $players = [], $difficulty = 'easy', $game_mode = 'versus')
    {
        $players = array_values(array_unique(array_filter($players)));
        if (empty($players)) {
            $players = [$host];
        }

        $config = $this->get_difficulty_config($difficulty);
        $rows   = (int)$config['rows'];
        $cols   = (int)$config['cols'];
        $mines  = (int)$config['mines'];

        // ตรวจสอบโหมดเกม: versus | coop | hunter | solo
        if (count($players) === 1) {
            $game_mode = 'solo';
        } else if (!in_array($game_mode, ['versus', 'coop', 'hunter', 'solo'])) {
            $game_mode = 'versus';
        }

        $scores = [];
        $knockouts = [];
        foreach ($players as $p) {
            $scores[$p] = 0;
            $knockouts[$p] = false;
        }

        return [
            'id'             => $room_id,
            'host'           => $host,
            'players'        => $players,
            'difficulty'     => $difficulty,
            'game_mode'      => $game_mode,
            'rows'           => $rows,
            'cols'           => $cols,
            'total_mines'    => $mines,
            'flags_left'     => $mines,
            'board_ready'    => false, // จะสุ่มระเบิดเมื่อคลิกแรก (First-click safe)
            'board'          => [],
            'status'         => 'playing', // playing | won | lost
            'winner'         => null,
            'current_turn'   => $players[0],
            'turn_index'     => 0,
            'turn_deadline'  => ($game_mode === 'versus' || $game_mode === 'hunter') ? (time() + 20) : null,
            'team_hp'        => 3,
            'max_team_hp'    => 3,
            'scores'         => $scores,
            'knockouts'      => $knockouts,
            'revealed_count' => 0,
            'rematch_votes'  => [],
            'left_players'   => [],
            'created_at'     => time(),
            'last_active'    => time(),
            'log'            => ['สร้างห้องเรียบร้อย'],
        ];
    }

    /**
     * รีเซ็ตห้องเกมเพื่อเริ่มเล่นกระดานใหม่ (Rematch)
     */
    public function reset_game(&$room)
    {
        $mines = (int)$room['total_mines'];

        $room['board']          = [];
        $room['board_ready']    = false;
        $room['status']         = 'playing';
        $room['winner']         = null;
        $room['flags_left']     = $mines;
        $room['revealed_count'] = 0;
        $room['team_hp']        = isset($room['max_team_hp']) ? (int)$room['max_team_hp'] : 3;
        $room['rematch_votes']  = [];
        $room['last_active']    = time();

        $players = isset($room['players']) && is_array($room['players']) ? $room['players'] : [$room['host']];
        $room['turn_index']    = 0;
        $room['current_turn']  = $players[0];
        $room['turn_deadline'] = (isset($room['game_mode']) && in_array($room['game_mode'], ['versus', 'hunter'])) ? (time() + 20) : null;

        if (!isset($room['scores']) || !is_array($room['scores'])) {
            $room['scores'] = [];
        }
        if (!isset($room['knockouts']) || !is_array($room['knockouts'])) {
            $room['knockouts'] = [];
        }
        foreach ($players as $p) {
            $room['scores'][$p] = 0;
            $room['knockouts'][$p] = false;
        }

        $this->_add_log($room, '🔄 เริ่มกระดานใหม่ (Rematch) สำเร็จ!');
    }

    /**
     * สุ่มตำแหน่งระเบิดและคำนวณตัวเลขรอบข้าง โดยการันตีว่าคลิกแรกปลอดภัย (Safe First Click)
     */
    public function generate_board($rows, $cols, $mines, $safe_r, $safe_c)
    {
        $board = [];

        // กำหนดเขตปลอดภัยรอบคลิกแรก (รวมคลิกแรกและ 8 ช่องรอบข้าง)
        $safe_zone = [];
        for ($dr = -1; $dr <= 1; $dr++) {
            for ($dc = -1; $dc <= 1; $dc++) {
                $nr = $safe_r + $dr;
                $nc = $safe_c + $dc;
                if ($nr >= 0 && $nr < $rows && $nc >= 0 && $nc < $cols) {
                    $safe_zone["{$nr}_{$nc}"] = true;
                }
            }
        }

        // รายการช่องทั้งหมดที่มีสิทธิ์เป็นระเบิด
        $available_cells = [];
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                $key = "{$r}_{$c}";
                if (!isset($safe_zone[$key])) {
                    $available_cells[] = [$r, $c];
                }
            }
        }

        // สุ่มเลือกระเบิด
        shuffle($available_cells);
        $mine_cells = [];
        $mines_to_place = min($mines, count($available_cells));
        for ($i = 0; $i < $mines_to_place; $i++) {
            $mine_cells[$available_cells[$i][0] . '_' . $available_cells[$i][1]] = true;
        }

        // สร้างตารางข้อมูล
        for ($r = 0; $r < $rows; $r++) {
            $board[$r] = [];
            for ($c = 0; $c < $cols; $c++) {
                $is_mine = isset($mine_cells["{$r}_{$c}"]) ? 1 : 0;
                $board[$r][$c] = [
                    'm'  => $is_mine, // 1 = mine, 0 = safe
                    'v'  => 0,        // adjacent mines
                    'r'  => 0,        // revealed (0 = hidden, 1 = opened)
                    'f'  => 0,        // flagged (0 = no, 1 = flagged)
                    'by' => '',       // username who clicked
                ];
            }
        }

        // คำนวณตัวเลขจำนวนระเบิดรอบข้าง (v)
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if ($board[$r][$c]['m'] === 1) {
                    continue;
                }
                $count = 0;
                for ($dr = -1; $dr <= 1; $dr++) {
                    for ($dc = -1; $dc <= 1; $dc++) {
                        $nr = $r + $dr;
                        $nc = $c + $dc;
                        if ($nr >= 0 && $nr < $rows && $nc >= 0 && $nc < $cols) {
                            if ($board[$nr][$nc]['m'] === 1) {
                                $count++;
                            }
                        }
                    }
                }
                $board[$r][$c]['v'] = $count;
            }
        }

        return $board;
    }

    /**
     * ดำเนินการเปิดช่อง (Reveal Cell)
     */
    public function process_reveal(&$room, $r, $c, $username)
    {
        $rows = (int)$room['rows'];
        $cols = (int)$room['cols'];
        $mines = (int)$room['total_mines'];

        // ถ้ากระดานยังไม่สุ่ม (คลิกแรกของเกม) ให้สุ่มระเบิดก่อน
        if (empty($room['board_ready']) || empty($room['board'])) {
            $room['board'] = $this->generate_board($rows, $cols, $mines, $r, $c);
            $room['board_ready'] = true;
        }

        if ($r < 0 || $r >= $rows || $c < 0 || $c >= $cols) {
            return ['status' => 'error', 'message' => 'พิกัดนอกกระดาน'];
        }

        $cell = &$room['board'][$r][$c];
        if ($cell['r'] === 1 || $cell['f'] === 1) {
            return ['status' => 'error', 'message' => 'ช่องนี้เปิดแล้วหรือปักธงอยู่'];
        }

        $mode = $room['game_mode'];

        // ตรวจสอบว่าโดนระเบิดหรือไม่
        if ($cell['m'] === 1) {
            $cell['r'] = 1;
            $cell['by'] = $username;

            if ($mode === 'coop') {
                $room['team_hp'] = max(0, $room['team_hp'] - 1);
                $this->_add_log($room, "💥 {$username} เหยียบระเบิด! เสีย 1 HP (เหลือ {$room['team_hp']}/{$room['max_team_hp']})");
                
                if ($room['team_hp'] <= 0) {
                    $room['status'] = 'lost';
                    $this->_reveal_all_mines($room);
                    $this->_add_log($room, "☠️ HP ทีมหมดแล้ว — จบเกม (แพ้ทั้งทีม)");
                }
            } else if ($mode === 'hunter') {
                // ในโหมดล่าระเบิด ถ้าเปิดเจอระเบิด ได้คะแนน +20 ทันที!
                $room['scores'][$username] = (isset($room['scores'][$username]) ? $room['scores'][$username] : 0) + 20;
                $this->_add_log($room, "🎯 {$username} ขุดเจอระเบิดพอดี! ได้รับ +20 แต้ม");
                $this->_check_hunter_win($room);
            } else if ($mode === 'versus') {
                // ในโหมดชิงแต้ม เหยียบระเบิดจะเสียคะแนนและถูก Knockout หรือตัดสิทธิ์
                $room['scores'][$username] = max(0, (isset($room['scores'][$username]) ? $room['scores'][$username] : 0) - 50);
                $room['knockouts'][$username] = true;
                $this->_add_log($room, "💥 {$username} พลาดเหยียบระเบิด! ถูก Knockout และหัก 50 แต้ม");

                // ตรวจสอบว่าผู้เล่นทุกคนโดน knockout หมดหรือยัง
                $alive = 0;
                $last_alive = null;
                foreach ($room['players'] as $p) {
                    if (empty($room['knockouts'][$p])) {
                        $alive++;
                        $last_alive = $p;
                    }
                }
                if ($alive === 0 || $alive === 1) {
                    $room['status'] = 'won';
                    $room['winner'] = $this->_get_highest_score_player($room);
                    $this->_reveal_all_mines($room);
                    $this->_add_log($room, "🏆 จบการแข่งขัน! ผู้ชนะคือ {$room['winner']}");
                }
            } else {
                // Solo mode
                $room['status'] = 'lost';
                $this->_reveal_all_mines($room);
                $this->_add_log($room, "💥 เหยียบระเบิด! จบเกม");
            }
        } else {
            // ปลอดภัย! ทำ Flood Fill ขยายช่อง
            $revealed_cells = $this->_flood_fill($room, $r, $c, $username);
            $points = count($revealed_cells) * 5;
            if (isset($room['scores'][$username])) {
                $room['scores'][$username] += $points;
            }
            $this->_add_log($room, "✨ {$username} เปิดช่องปลอดภัย ({$r}, {$c}) ขยาย " . count($revealed_cells) . " ช่อง (+{$points} แต้ม)");

            // ตรวจสอบการชนะ
            $total_cells = $rows * $cols;
            $safe_total = $total_cells - $mines;
            if ($room['revealed_count'] >= $safe_total) {
                $room['status'] = 'won';
                $room['winner'] = $this->_get_highest_score_player($room);
                $this->_add_log($room, "🎉 เคลียร์ระเบิดสำเร็จทั้งกระดาน! ผู้ชนะคือ {$room['winner']}");
            }
        }

        // สลับเทิร์นหากยังไม่จบเกม (สำหรับ Versus & Hunter)
        if ($room['status'] === 'playing' && ($mode === 'versus' || $mode === 'hunter')) {
            $this->_next_turn($room);
        }

        $room['last_active'] = time();
        return ['status' => 'ok'];
    }

    /**
     * ดำเนินการปักธง / ปลดธง (Toggle Flag)
     */
    public function process_flag(&$room, $r, $c, $username)
    {
        if (empty($room['board_ready']) || empty($room['board'])) {
            return ['status' => 'error', 'message' => 'กรุณาเปิดช่องแรกก่อนปักธง'];
        }

        $rows = (int)$room['rows'];
        $cols = (int)$room['cols'];
        if ($r < 0 || $r >= $rows || $c < 0 || $c >= $cols) {
            return ['status' => 'error', 'message' => 'พิกัดนอกกระดาน'];
        }

        $cell = &$room['board'][$r][$c];
        if ($cell['r'] === 1) {
            return ['status' => 'error', 'message' => 'ช่องนี้เปิดแล้ว ไม่สามารถปักธงได้'];
        }

        if ($cell['f'] === 1) {
            // ปลดธง
            $cell['f'] = 0;
            $room['flags_left']++;
            $this->_add_log($room, "🚩 {$username} ปลดธงที่ช่อง ({$r}, {$c})");
        } else {
            // ปักธง (ไม่บอกว่าปักถูกหรือผิดเพื่อความยุติธรรมและไม่ให้รู้ตำแหน่งระเบิด)
            $cell['f'] = 1;
            $cell['by'] = $username;
            $room['flags_left']--;
            
            // ในโหมด Hunter: ถ้าปักถูกจะนับคะแนนเงียบๆ ตรวจสอบเงื่อนไขชนะ
            if ($cell['m'] === 1 && $room['game_mode'] === 'hunter') {
                if (isset($room['scores'][$username])) {
                    $room['scores'][$username] += 20;
                }
                $this->_check_hunter_win($room);
            }
            $this->_add_log($room, "🚩 {$username} ปักธงที่ช่อง ({$r}, {$c})");
        }

        $room['last_active'] = time();
        return ['status' => 'ok'];
    }

    // ========================================================
    //  Helper Functions
    // ========================================================

    private function _flood_fill(&$room, $start_r, $start_c, $username)
    {
        $rows = (int)$room['rows'];
        $cols = (int)$room['cols'];
        $revealed = [];

        $queue = [[$start_r, $start_c]];
        $visited = [];
        $visited["{$start_r}_{$start_c}"] = true;

        while (!empty($queue)) {
            $curr = array_shift($queue);
            $r = $curr[0];
            $c = $curr[1];

            $cell = &$room['board'][$r][$c];
            if ($cell['r'] === 0 && $cell['f'] === 0) {
                $cell['r'] = 1;
                $cell['by'] = $username;
                $room['revealed_count']++;
                $revealed[] = [$r, $c];
            }

            // ถ้าช่องนี้เป็น 0 ให้ขยายช่องรอบข้างต่อ
            if ($cell['v'] === 0 && $cell['m'] === 0) {
                for ($dr = -1; $dr <= 1; $dr++) {
                    for ($dc = -1; $dc <= 1; $dc++) {
                        $nr = $r + $dr;
                        $nc = $c + $dc;
                        $key = "{$nr}_{$nc}";
                        if ($nr >= 0 && $nr < $rows && $nc >= 0 && $nc < $cols && !isset($visited[$key])) {
                            $visited[$key] = true;
                            if ($room['board'][$nr][$nc]['r'] === 0 && $room['board'][$nr][$nc]['f'] === 0 && $room['board'][$nr][$nc]['m'] === 0) {
                                $queue[] = [$nr, $nc];
                            }
                        }
                    }
                }
            }
        }

        return $revealed;
    }

    private function _reveal_all_mines(&$room)
    {
        $rows = (int)$room['rows'];
        $cols = (int)$room['cols'];
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if ($room['board'][$r][$c]['m'] === 1) {
                    $room['board'][$r][$c]['r'] = 1;
                }
            }
        }
    }

    private function _next_turn(&$room)
    {
        $players = $room['players'];
        $count = count($players);
        if ($count <= 1) return;

        $attempts = 0;
        do {
            $room['turn_index'] = ($room['turn_index'] + 1) % $count;
            $next_player = $players[$room['turn_index']];
            $attempts++;
        } while (!empty($room['knockouts'][$next_player]) && $attempts < $count);

        $room['current_turn'] = $next_player;
        $room['turn_deadline'] = time() + 20;
    }

    private function _get_highest_score_player($room)
    {
        $max_score = -99999;
        $top_player = $room['players'][0];
        foreach ($room['scores'] as $player => $score) {
            if ($score > $max_score) {
                $max_score = $score;
                $top_player = $player;
            }
        }
        return $top_player;
    }

    private function _check_hunter_win(&$room)
    {
        $mines = (int)$room['total_mines'];
        $target = ceil($mines / 2);
        foreach ($room['scores'] as $p => $score) {
            // ใน hunter mode 1 ลูก = 20 แต้ม
            $found = floor($score / 20);
            if ($found >= $target) {
                $room['status'] = 'won';
                $room['winner'] = $p;
                $this->_reveal_all_mines($room);
                $this->_add_log($room, "👑 {$p} หาและเคลียร์ระเบิดได้เกินครึ่งกระดาน ({$found}/{$mines}) ชนะทันที!");
                break;
            }
        }
    }

    private function _add_log(&$room, $msg)
    {
        if (!isset($room['log']) || !is_array($room['log'])) {
            $room['log'] = [];
        }
        array_unshift($room['log'], '[' . date('H:i:s') . '] ' . $msg);
        if (count($room['log']) > 15) {
            array_pop($room['log']);
        }
    }
}
