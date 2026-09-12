<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Minesweeper Controller
 * ควบคุมห้องเกม Minesweeper รองรับ 1-4 คน (Solo, Versus, Co-op, Hunter)
 */
class Minesweeper extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->library('firebase_lib');
        $this->load->model('Minesweeper_model', 'ms_model');
    }

    /**
     * หน้าแรก Minesweeper — สร้างห้องเล่นใหม่ทันที
     */
    public function index()
    {
        $room_id = 'mine_' . time() . '_' . rand(100, 999);
        redirect('minesweeper/room/' . $room_id);
    }

    /**
     * หน้ากระดานเกม Minesweeper
     */
    public function room($room_id = null)
    {
        if (empty($room_id)) {
            redirect('player');
        }

        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);

        if (!$room) {
            // ตรวจสอบว่ามาจากห้อง Lobby หรือไม่
            $lobby = $this->firebase_lib->get_by_key('lobbies', $room_id);
            $players = ($lobby && !empty($lobby['players'])) ? $lobby['players'] : [$username];
            $host    = ($lobby && !empty($lobby['host'])) ? $lobby['host'] : $username;
            $lobby_settings = (isset($lobby['settings']) && is_array($lobby['settings'])) ? $lobby['settings'] : [];
            $mode    = isset($lobby_settings['mode']) ? $lobby_settings['mode'] : ((count($players) > 1) ? 'versus' : 'solo');
            $diff    = isset($lobby_settings['difficulty']) ? $lobby_settings['difficulty'] : 'easy';
            $theme   = isset($lobby_settings['theme']) ? $lobby_settings['theme'] : 'classic';

            $room = $this->ms_model->create_initial_room($room_id, $host, $players, $diff, $mode);
            $room['theme'] = $theme;
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);
        } else {
            if (!isset($room['players']) || !is_array($room['players'])) {
                $room['players'] = [$username];
            }
            if (!isset($room['scores']) || !is_array($room['scores'])) {
                $room['scores'] = [];
            }
            if (!isset($room['knockouts']) || !is_array($room['knockouts'])) {
                $room['knockouts'] = [];
            }
            // ถ้ามีห้องอยู่แล้ว และผู้เล่นยังไม่อยู่ในรายชื่อผู้เล่น ให้เพิ่มเข้าห้องถ้ายังไม่เต็ม (สูงสุด 4 คน)
            if (!in_array($username, $room['players']) && count($room['players']) < 4) {
                $room['players'][] = $username;
                $room['scores'][$username] = 0;
                $room['knockouts'][$username] = false;
                $this->firebase_lib->update('games/minesweeper', $room_id, [
                    'players'   => $room['players'],
                    'scores'    => $room['scores'],
                    'knockouts' => $room['knockouts'],
                ]);
            }
        }

        // อัปเดตสถานะ Presence ว่ากำลังเล่น Minesweeper
        $this->firebase_lib->update('presence', $username, [
            'username'    => $username,
            'status'      => 'playing',
            'game'        => 'Minesweeper',
            'room_id'     => $room_id,
            'last_active' => time(),
        ]);

        // ดึงสถิติของผู้เล่น
        $user_stats = $this->firebase_lib->get_by_key('game/minesweeper', $username);
        $my_wins   = (is_array($user_stats) && isset($user_stats['wins'])) ? (int)$user_stats['wins'] : 0;
        $my_losses = (is_array($user_stats) && isset($user_stats['losses'])) ? (int)$user_stats['losses'] : 0;
        $my_played = (is_array($user_stats) && isset($user_stats['played'])) ? (int)$user_stats['played'] : ($my_wins + $my_losses);

        $data = [
            'title'               => 'Minesweeper — ห้อง ' . $room_id,
            'room_id'             => $room_id,
            'username'            => $username,
            'room'                => $room,
            'difficulty_settings' => $this->ms_model->difficulty_settings,
            'user_stats'          => [
                'wins'   => $my_wins,
                'losses' => $my_losses,
                'played' => $my_played,
            ],
            'is_admin'            => ($this->session->userdata('role') === 'admin'),
        ];

        // ตรวจสอบธีมที่ผู้เล่นเลือก (Classic หรือ Re:Zero Anime)
        $theme_param = $this->input->get('theme');
        if (!empty($theme_param)) {
            $current_theme = $theme_param;
            $this->session->set_userdata('ms_theme', $theme_param);
        } else if (!empty($room['theme'])) {
            $current_theme = $room['theme'];
            $this->session->set_userdata('ms_theme', $room['theme']);
        } else {
            $current_theme = $this->session->userdata('ms_theme') ?: 'classic';
        }

        if ($current_theme === 'rezero') {
            $data['title'] = 'Minesweeper (Re:Zero Anime) — ห้อง ' . $room_id;
            $this->load->view('games/minesweeper/thameanimerezero', $data);
        } else {
            $this->load->view('games/minesweeper/game', $data);
        }
    }

    /**
     * API: ดึงสถานะกระดานล่าสุด
     */
    public function get_state($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);

        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'ไม่พบห้องเกม',
            ]));
        }

        // ป้องกันการโกงดูช่องระเบิดผ่าน Inspect: ถ้าเกมยังไม่จบ ซ่อนตำแหน่งระเบิดที่ยังไม่เปิด
        $client_board = [];
        $is_game_over = in_array($room['status'], ['won', 'lost']);

        if (!empty($room['board']) && is_array($room['board'])) {
            $rows = count($room['board']);
            $cols = count($room['board'][0]);
            for ($r = 0; $r < $rows; $r++) {
                $client_board[$r] = [];
                for ($c = 0; $c < $cols; $c++) {
                    $cell = $room['board'][$r][$c];
                    // ถ้ายังไม่เปิด และเกมยังไม่จบ ให้ปิดบัง m (mine)
                    if ($cell['r'] === 0 && !$is_game_over) {
                        $client_board[$r][$c] = [
                            'r'  => 0,
                            'f'  => $cell['f'],
                            'v'  => 0,
                            'by' => $cell['by'],
                        ];
                    } else {
                        $client_board[$r][$c] = $cell;
                    }
                }
            }
        }

        $room_client = $room;
        $room_client['board'] = $client_board;

        $is_admin = ($this->session->userdata('role') === 'admin');
        $res_data = [
            'status'   => 'ok',
            'room'     => $room_client,
            'me'       => $username,
            'is_admin' => $is_admin,
        ];

        // ถ้าเป็นแอดมิน ให้ส่งเฉลยกระดานจริง (solution_board) ไปด้วย
        if ($is_admin) {
            $res_data['solution_board'] = (!empty($room['board']) && is_array($room['board'])) ? $room['board'] : [];
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode($res_data));
    }

    /**
     * API: คลิกเปิดช่อง (Reveal Cell)
     */
    public function reveal($room_id)
    {
        $username = $this->session->userdata('username');
        $r = (int)$this->input->post('r');
        $c = (int)$this->input->post('c');

        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);
        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่พบห้อง']));
        }

        if ($room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เกมจบแล้ว']));
        }

        // ตรวจสอบเทิร์นในโหมด Versus / Hunter (ถ้ามีผู้เล่นมากกว่า 1 คน)
        if (count($room['players']) > 1 && in_array($room['game_mode'], ['versus', 'hunter'])) {
            if ($room['current_turn'] !== $username) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่ใช่ตาของคุณ']));
            }
        }

        // ประมวลผลการเปิดช่อง
        $result = $this->ms_model->process_reveal($room, $r, $c, $username);
        if ($result['status'] === 'ok') {
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);

            // บันทึกสถิติเมื่อจบเกม
            if ($room['status'] === 'won' || $room['status'] === 'lost') {
                $this->_record_stats($room);
            }
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * API: ปักธง / ปลดธง (Toggle Flag)
     */
    public function flag($room_id)
    {
        $username = $this->session->userdata('username');
        $r = (int)$this->input->post('r');
        $c = (int)$this->input->post('c');

        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);
        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่พบห้อง']));
        }

        if ($room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เกมจบแล้ว']));
        }

        $result = $this->ms_model->process_flag($room, $r, $c, $username);
        if ($result['status'] === 'ok') {
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);
            if ($room['status'] === 'won') {
                $this->_record_stats($room);
            }
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * API: เปลี่ยนโหมดเกมหรือความยาก (Host Only)
     */
    public function change_settings($room_id)
    {
        $username   = $this->session->userdata('username');
        $difficulty = $this->input->post('difficulty');
        $mode       = $this->input->post('mode');

        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);
        if (!$room || $room['host'] !== $username) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'คุณไม่ใช่ Host']));
        }

        $new_room = $this->ms_model->create_initial_room($room_id, $room['host'], $room['players'], $difficulty, $mode);
        $this->firebase_lib->set('games/minesweeper', $room_id, $new_room);

        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'ok']));
    }

    /**
     * API: ขอเปลี่ยนระดับความยาก (Host Propose Difficulty)
     */
    public function propose_difficulty($room_id)
    {
        $username   = $this->session->userdata('username');
        $difficulty = $this->input->post('difficulty');
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            $difficulty = 'easy';
        }

        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);
        if (!$room || $room['host'] !== $username) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'คุณไม่ใช่หัวหน้าห้อง']));
        }

        $players = isset($room['players']) && is_array($room['players']) ? $room['players'] : [$username];
        $diff_names = ['easy' => 'ง่าย (9x9)', 'medium' => 'ปานกลาง (16x16)', 'hard' => 'ยาก (16x30)'];
        $diff_name = isset($diff_names[$difficulty]) ? $diff_names[$difficulty] : $difficulty;

        // ถ้าเล่นคนเดียว: เปลี่ยนระดับความยากและเริ่มใหม่ทันที
        if (count($players) <= 1) {
            $mode = isset($room['game_mode']) ? $room['game_mode'] : 'solo';
            $new_room = $this->ms_model->create_initial_room($room_id, $room['host'], $room['players'], $difficulty, $mode);
            $this->firebase_lib->set('games/minesweeper', $room_id, $new_room);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'ok',
                'action' => 'updated_immediately'
            ]));
        }

        // เล่นหลายคน: ตั้งค่า Diff Proposal รอการโหวตจากทุกคน
        $room['diff_proposal'] = [
            'id'          => uniqid('diff_'),
            'proposed_by' => $username,
            'difficulty'  => $difficulty,
            'diff_name'   => $diff_name,
            'status'      => 'pending',
            'votes'       => [$username => true],
            'total_count' => count($players)
        ];
        $room['log'] = isset($room['log']) ? $room['log'] : [];
        array_unshift($room['log'], '[' . date('H:i:s') . "] ⚙️ {$username} เสนอเปลี่ยนระดับความยากเป็น: {$diff_name} (รอเพื่อนกดยืนยัน)");
        $this->firebase_lib->set('games/minesweeper', $room_id, $room);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'      => 'ok',
            'action'      => 'waiting_others',
            'voted_count' => 1,
            'total_count' => count($players)
        ]));
    }

    /**
     * API: ตอบรับหรือปฏิเสธคำขอเปลี่ยนระดับความยาก
     */
    public function respond_difficulty($room_id)
    {
        $username = $this->session->userdata('username');
        $response = $this->input->post('response'); // 'accept' or 'reject'

        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);
        if (!$room || empty($room['diff_proposal']) || $room['diff_proposal']['status'] !== 'pending') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่มีคำขอเปลี่ยนระดับความยากที่รออยู่']));
        }

        $proposal = &$room['diff_proposal'];
        $players = isset($room['players']) && is_array($room['players']) ? $room['players'] : [$username];

        if ($response === 'reject') {
            $proposal['status'] = 'rejected';
            $proposal['rejected_by'] = $username;
            $room['log'] = isset($room['log']) ? $room['log'] : [];
            array_unshift($room['log'], '[' . date('H:i:s') . "] ❌ {$username} ปฏิเสธการเปลี่ยนระดับความยาก");
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'ok',
                'action' => 'rejected'
            ]));
        }

        // กดยอมรับ
        $proposal['votes'][$username] = true;

        // ตรวจสอบว่าทุกคนยอมรับครบหรือยัง
        $all_accepted = true;
        foreach ($players as $p) {
            if (empty($proposal['votes'][$p])) {
                $all_accepted = false;
                break;
            }
        }

        if ($all_accepted) {
            $new_difficulty = $proposal['difficulty'];
            $mode = isset($room['game_mode']) ? $room['game_mode'] : (count($players) > 1 ? 'coop' : 'solo');
            $new_room = $this->ms_model->create_initial_room($room_id, $room['host'], $room['players'], $new_difficulty, $mode);
            $new_room['log'] = [
                '[' . date('H:i:s') . "] ✅ ทุกคนยอมรับการเปลี่ยนระดับความยากเป็น \"{$proposal['diff_name']}\" เริ่มกระดานใหม่!"
            ];
            $this->firebase_lib->set('games/minesweeper', $room_id, $new_room);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'ok',
                'action' => 'all_accepted'
            ]));
        } else {
            $voted_count = count($proposal['votes']);
            $total_count = count($players);
            $room['log'] = isset($room['log']) ? $room['log'] : [];
            array_unshift($room['log'], '[' . date('H:i:s') . "] 🔄 {$username} กดยอมรับการเปลี่ยนระดับความยาก ({$voted_count}/{$total_count} คน)");
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'      => 'ok',
                'action'      => 'waiting_others',
                'voted_count' => $voted_count,
                'total_count' => $total_count
            ]));
        }
    }

    /**
     * API: เริ่มเกมใหม่ (Rematch) — รองรับการโหวตพร้อมกันทุกคนเมื่อเล่นหลายคน
     */
    public function rematch($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);
        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่พบห้อง']));
        }

        $players = isset($room['players']) && is_array($room['players']) ? $room['players'] : [$username];

        // เล่นคนเดียว (Solo): รีเซ็ตเริ่มใหม่ทันที
        if (count($players) <= 1) {
            $this->ms_model->reset_game($room);
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'ok',
                'action' => 'reset_now',
            ]));
        }

        // เล่นหลายคน: บันทึกโหวต Rematch ของผู้เล่น
        if (!isset($room['rematch_votes']) || !is_array($room['rematch_votes'])) {
            $room['rematch_votes'] = [];
        }
        $room['rematch_votes'][$username] = true;

        // ตรวจสอบว่าผู้เล่นทุกคนในห้องกดยืนยันครบหรือยัง
        $all_voted = true;
        foreach ($players as $p) {
            if (empty($room['rematch_votes'][$p])) {
                $all_voted = false;
                break;
            }
        }

        if ($all_voted) {
            $this->ms_model->reset_game($room);
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => 'ok',
                'action' => 'reset_now',
            ]));
        } else {
            $voted_count = count($room['rematch_votes']);
            $total_count = count($players);
            $room['log'] = isset($room['log']) ? $room['log'] : [];
            array_unshift($room['log'], '[' . date('H:i:s') . "] 🔄 {$username} กดยืนยันเริ่มใหม่ ({$voted_count}/{$total_count} คน)");
            $this->firebase_lib->set('games/minesweeper', $room_id, $room);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'      => 'ok',
                'action'      => 'waiting_others',
                'voted_count' => $voted_count,
                'total_count' => $total_count,
            ]));
        }
    }

    /**
     * ออกจากห้องเกม — บันทึกแจ้งเตือนเพื่อนว่ากลับหน้าหลักแล้ว
     */
    public function leave($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/minesweeper', $room_id);

        if ($room) {
            $room['players'] = array_values(array_diff($room['players'], [$username]));
            unset($room['scores'][$username]);
            unset($room['knockouts'][$username]);
            if (isset($room['rematch_votes'][$username])) {
                unset($room['rematch_votes'][$username]);
            }

            if (empty($room['players'])) {
                $this->firebase_lib->delete('games/minesweeper', $room_id);
            } else {
                if ($room['host'] === $username) {
                    $room['host'] = $room['players'][0];
                }

                // บันทึกรายการผู้เล่นที่กลับหน้าหลักไปแล้ว เพื่อให้แจ้งเตือนเพื่อนในห้อง
                if (!isset($room['left_players']) || !is_array($room['left_players'])) {
                    $room['left_players'] = [];
                }
                if (!in_array($username, $room['left_players'])) {
                    $room['left_players'][] = $username;
                }

                $room['log'] = isset($room['log']) ? $room['log'] : [];
                array_unshift($room['log'], '[' . date('H:i:s') . "] 🚪 ผู้เล่น {$username} กลับหน้าหลักแล้ว");

                $this->firebase_lib->set('games/minesweeper', $room_id, $room);
            }
        }

        $this->firebase_lib->update('presence', $username, [
            'username'    => $username,
            'status'      => 'online',
            'game'        => '',
            'room_id'     => '',
            'last_active' => time(),
        ]);

        redirect('player');
    }

    /**
     * บันทึกสถิติผู้เล่นลง Firebase
     */
    private function _record_stats($room)
    {
        $winner = $room['winner'];
        foreach ($room['players'] as $p) {
            $stats = $this->firebase_lib->get_by_key('game/minesweeper', $p);
            $wins   = (is_array($stats) && isset($stats['wins'])) ? (int)$stats['wins'] : 0;
            $losses = (is_array($stats) && isset($stats['losses'])) ? (int)$stats['losses'] : 0;
            $played = (is_array($stats) && isset($stats['played'])) ? (int)$stats['played'] : 0;

            $played++;
            if ($room['status'] === 'won' && ($winner === $p || $room['game_mode'] === 'coop')) {
                $wins++;
            } else {
                $losses++;
            }

            $this->firebase_lib->update('game/minesweeper', $p, [
                'username' => $p,
                'wins'     => $wins,
                'losses'   => $losses,
                'played'   => $played,
            ]);
        }
    }

    /**
     * สตรีมไฟล์เสียงสำหรับธีม Re:Zero หรือ Minesweeper
     * เพื่อข้ามข้อจำกัด 403 Forbidden ของโฟลเดอร์ application/
     */
    public function sound($file_name = 'i_love_you_satella')
    {
        $clean_name = basename(str_replace(['.mp3', '.wav', '.ogg'], '', $file_name)) . '.mp3';
        $file_path  = APPPATH . 'views/games/minesweeper/image/rezero/' . $clean_name;

        if (file_exists($file_path)) {
            $filesize = filesize($file_path);
            header('Content-Type: audio/mpeg');
            header('Content-Length: ' . $filesize);
            header('Accept-Ranges: bytes');
            header('Cache-Control: public, max-age=86400');
            readfile($file_path);
            exit;
        }

        show_404();
    }

    /**
     * สตรีมไฟล์รูปภาพสำหรับธีม Re:Zero หรือ Minesweeper
     * เพื่อข้ามข้อจำกัด 403 Forbidden ของโฟลเดอร์ application/
     */
    public function image($file_name = '')
    {
        $clean_name = basename($file_name);
        $file_path  = APPPATH . 'views/games/minesweeper/image/rezero/' . $clean_name;
        if (!file_exists($file_path)) {
            $file_path = APPPATH . 'views/games/minesweeper/image/' . $clean_name;
        }

        if (file_exists($file_path)) {
            $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $mimes = [
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'gif'  => 'image/gif',
                'svg'  => 'image/svg+xml',
            ];
            $mime = isset($mimes[$ext]) ? $mimes[$ext] : 'image/png';
            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: public, max-age=86400');
            readfile($file_path);
            exit;
        }

        show_404();
    }
}

