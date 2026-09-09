<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Xo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->library('firebase_lib');
    }

    /**
     * หน้าแรก XO — สร้างห้องใหม่และ redirect เข้าห้อง
     */
    public function index()
    {
        $room_id = 'xo_' . time() . '_' . rand(100, 999);
        redirect('xo/room/' . $room_id);
    }

    /**
     * หน้าห้องเล่นเกม XO
     */
    public function room($room_id = null)
    {
        if (empty($room_id)) {
            redirect('player');
        }

        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/xo', $room_id);

        if (!$room) {
            // สร้างห้องใหม่ (คนสร้างเป็น Player X)
            $room = [
                'id'         => $room_id,
                'host'       => $username,
                'player_x'   => $username,
                'player_o'   => null,
                'board'      => ['', '', '', '', '', '', '', '', ''],
                'turn'       => 'X',
                'status'     => 'waiting', // waiting | playing | finished
                'winner'     => null,      // null | X | O | draw
                'score_x'    => 0,
                'score_o'    => 0,
                'created_at' => time(),
            ];
            $this->firebase_lib->update('games/xo', $room_id, $room);
        } else {
            // ถ้ามีห้องอยู่แล้ว และผู้เล่นไม่ใช่ host และยังไม่มี player_o ให้เข้าร่วมเป็น player_o
            if ($room['host'] !== $username && empty($room['player_o'])) {
                $room['player_o'] = $username;
                $room['status']   = 'playing';
                $this->firebase_lib->update('games/xo', $room_id, [
                    'player_o' => $username,
                    'status'   => 'playing',
                ]);
            }
        }

        // อัปเดตสถานะของฉันว่ากำลังเล่นเกม OX
        $this->firebase_lib->update('presence', $username, [
            'username'    => $username,
            'status'      => 'playing',
            'game'        => 'OX (Tic Tac Toe)',
            'room_id'     => $room_id,
            'last_active' => time(),
        ]);

        // ดึงสถิติของฉันในเกมนี้ (ชนะ, แพ้, เล่น)
        $user_stats = $this->firebase_lib->get_by_key('game/xo', $username);
        $my_wins = (is_array($user_stats) && isset($user_stats['wins'])) ? (int)$user_stats['wins'] : 0;
        $my_losses = (is_array($user_stats) && isset($user_stats['losses'])) ? (int)$user_stats['losses'] : 0;
        $my_played = (is_array($user_stats) && isset($user_stats['played'])) ? (int)$user_stats['played'] : ($my_wins + $my_losses);

        $data = [
            'title'      => 'เกม OX — ห้อง ' . $room_id,
            'room_id'    => $room_id,
            'username'   => $username,
            'room'       => $room,
            'user_stats' => [
                'wins'   => $my_wins,
                'losses' => $my_losses,
                'played' => $my_played,
            ],
        ];

        $this->load->view('xo/game', $data);
    }

    /**
     * API: ดึงสถานะปัจจุบันของห้องเกม XO
     */
    public function get_state($room_id)
    {
        $room = $this->firebase_lib->get_by_key('games/xo', $room_id);
        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'not_found']));
        }

        // ป้องกัน board ว่างหรือเพี้ยน
        if (!isset($room['board']) || !is_array($room['board'])) {
            $room['board'] = ['', '', '', '', '', '', '', '', ''];
        }

        // ดึงสถิติของผู้เล่น X และ O จาก game/xo
        $stats_x = !empty($room['player_x']) ? $this->firebase_lib->get_by_key('game/xo', $room['player_x']) : null;
        $stats_o = !empty($room['player_o']) ? $this->firebase_lib->get_by_key('game/xo', $room['player_o']) : null;

        $wx = (is_array($stats_x) && isset($stats_x['wins'])) ? (int)$stats_x['wins'] : 0;
        $lx = (is_array($stats_x) && isset($stats_x['losses'])) ? (int)$stats_x['losses'] : 0;
        $px = (is_array($stats_x) && isset($stats_x['played'])) ? (int)$stats_x['played'] : ($wx + $lx);

        $wo = (is_array($stats_o) && isset($stats_o['wins'])) ? (int)$stats_o['wins'] : 0;
        $lo = (is_array($stats_o) && isset($stats_o['losses'])) ? (int)$stats_o['losses'] : 0;
        $po = (is_array($stats_o) && isset($stats_o['played'])) ? (int)$stats_o['played'] : ($wo + $lo);

        $room['stats_x'] = ['wins' => $wx, 'losses' => $lx, 'played' => $px];
        $room['stats_o'] = ['wins' => $wo, 'losses' => $lo, 'played' => $po];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($room));
    }

    /**
     * API: เดินหมาก (Make Move)
     */
    public function make_move($room_id)
    {
        $username   = $this->session->userdata('username');
        $cell_index = (int)$this->input->post('cell_index');

        $room = $this->firebase_lib->get_by_key('games/xo', $room_id);
        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'not_found']));
        }

        if ($room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'game_not_active', 'room' => $room]));
        }

        // ตรวจสอบสิทธิ์ว่าเป็นตาของใคร
        $my_symbol = null;
        if ($room['player_x'] === $username) $my_symbol = 'X';
        if ($room['player_o'] === $username) $my_symbol = 'O';

        if (!$my_symbol || $room['turn'] !== $my_symbol) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'not_your_turn', 'room' => $room]));
        }

        // ตรวจสอบว่าช่องนั้นว่างหรือไม่
        $board = isset($room['board']) && is_array($room['board']) ? $room['board'] : array_fill(0, 9, '');
        if (!empty($board[$cell_index])) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'cell_occupied', 'room' => $room]));
        }

        // วางหมาก
        $board[$cell_index] = $my_symbol;

        // ตรวจสอบผลแพ้-ชนะ
        $winner = $this->_check_winner($board);
        $new_status = 'playing';
        $score_x = isset($room['score_x']) ? (int)$room['score_x'] : 0;
        $score_o = isset($room['score_o']) ? (int)$room['score_o'] : 0;

        if ($winner) {
            $new_status = 'finished';
            $player_x = isset($room['player_x']) ? $room['player_x'] : null;
            $player_o = isset($room['player_o']) ? $room['player_o'] : null;

            if ($winner === 'X' || $winner === 'O') {
                if ($winner === 'X') {
                    $score_x++;
                    $winner_user = $player_x;
                    $loser_user  = $player_o;
                } else {
                    $score_o++;
                    $winner_user = $player_o;
                    $loser_user  = $player_x;
                }

                // บันทึกสถิติผู้ชนะ: wins + 1, played + 1
                if (!empty($winner_user)) {
                    $w_stats = $this->firebase_lib->get_by_key('game/xo', $winner_user);
                    $w_wins = (is_array($w_stats) && isset($w_stats['wins'])) ? (int)$w_stats['wins'] : 0;
                    $w_losses = (is_array($w_stats) && isset($w_stats['losses'])) ? (int)$w_stats['losses'] : 0;
                    $w_played = (is_array($w_stats) && isset($w_stats['played'])) ? (int)$w_stats['played'] : ($w_wins + $w_losses);
                    $this->firebase_lib->update('game/xo', $winner_user, [
                        'username'       => $winner_user,
                        'wins'           => $w_wins + 1,
                        'losses'         => $w_losses,
                        'played'         => $w_played + 1,
                        'last_win_at'    => time(),
                        'last_played_at' => time(),
                    ]);
                }

                // บันทึกสถิติผู้แพ้: losses + 1, played + 1
                if (!empty($loser_user)) {
                    $l_stats = $this->firebase_lib->get_by_key('game/xo', $loser_user);
                    $l_wins = (is_array($l_stats) && isset($l_stats['wins'])) ? (int)$l_stats['wins'] : 0;
                    $l_losses = (is_array($l_stats) && isset($l_stats['losses'])) ? (int)$l_stats['losses'] : 0;
                    $l_played = (is_array($l_stats) && isset($l_stats['played'])) ? (int)$l_stats['played'] : ($l_wins + $l_losses);
                    $this->firebase_lib->update('game/xo', $loser_user, [
                        'username'       => $loser_user,
                        'wins'           => $l_wins,
                        'losses'         => $l_losses + 1,
                        'played'         => $l_played + 1,
                        'last_played_at' => time(),
                    ]);
                }
            } elseif ($winner === 'draw') {
                // กรณีเสมอ: ทั้งสองฝ่ายได้ played + 1
                foreach ([$player_x, $player_o] as $d_user) {
                    if (!empty($d_user)) {
                        $d_stats = $this->firebase_lib->get_by_key('game/xo', $d_user);
                        $d_wins = (is_array($d_stats) && isset($d_stats['wins'])) ? (int)$d_stats['wins'] : 0;
                        $d_losses = (is_array($d_stats) && isset($d_stats['losses'])) ? (int)$d_stats['losses'] : 0;
                        $d_draws = (is_array($d_stats) && isset($d_stats['draws'])) ? (int)$d_stats['draws'] : 0;
                        $d_played = (is_array($d_stats) && isset($d_stats['played'])) ? (int)$d_stats['played'] : ($d_wins + $d_losses);
                        $this->firebase_lib->update('game/xo', $d_user, [
                            'username'       => $d_user,
                            'wins'           => $d_wins,
                            'losses'         => $d_losses,
                            'draws'          => $d_draws + 1,
                            'played'         => $d_played + 1,
                            'last_played_at' => time(),
                        ]);
                    }
                }
            }
        }

        $next_turn = ($my_symbol === 'X') ? 'O' : 'X';

        $update_data = [
            'board'   => $board,
            'turn'    => $next_turn,
            'status'  => $new_status,
            'winner'  => $winner,
            'score_x' => $score_x,
            'score_o' => $score_o,
        ];

        $this->firebase_lib->update('games/xo', $room_id, $update_data);
        $room = array_merge($room, $update_data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok', 'room' => $room]));
    }

    /**
     * API: เริ่มเกมใหม่ (Rematch)
     */
    public function rematch($room_id)
    {
        $room = $this->firebase_lib->get_by_key('games/xo', $room_id);
        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'not_found']));
        }

        $update_data = [
            'board'  => ['', '', '', '', '', '', '', '', ''],
            'turn'   => 'X',
            'status' => 'playing',
            'winner' => null,
        ];

        $this->firebase_lib->update('games/xo', $room_id, $update_data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok']));
    }

    /**
     * ออกจากห้องเกม — ลบห้องเกมเฉพาะเมื่อเป็น Host หรือเกมจบแล้ว
     */
    public function leave($room_id = null)
    {
        $username = $this->session->userdata('username');
        if ($room_id) {
            $room = $this->firebase_lib->get_by_key('games/xo', $room_id);
            if ($room) {
                $is_host = (isset($room['host']) && $room['host'] === $username);
                $is_finished = (isset($room['status']) && $room['status'] === 'finished');

                if ($is_host || $is_finished) {
                    $this->firebase_lib->delete('games/xo', $room_id);
                    $this->firebase_lib->delete('xo_rooms', $room_id);
                    $this->firebase_lib->delete('lobbies', $room_id);
                } else {
                    // หากเป็น Player O ให้ออกจากห้องและเปลี่ยนสถานะกลับเป็น waiting
                    if (isset($room['player_o']) && $room['player_o'] === $username) {
                        $this->firebase_lib->update('games/xo', $room_id, [
                            'player_o' => null,
                            'status'   => 'waiting',
                        ]);
                    }
                }
            }
        }

        if ($username) {
            $this->firebase_lib->update('presence', $username, [
                'status'      => 'online',
                'game'        => '',
                'room_id'     => '',
                'last_active' => time(),
            ]);
        }

        if ($this->input->is_ajax_request()) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'ok']));
        }
        redirect('player');
    }

    /**
     * ตรวจสอบผู้ชนะ 3 ช่อง
     */
    private function _check_winner($b)
    {
        $lines = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // แนวนอน
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // แนวตั้ง
            [0, 4, 8], [2, 4, 6],             // ทแยง
        ];

        foreach ($lines as $line) {
            list($x, $y, $z) = $line;
            if (!empty($b[$x]) && $b[$x] === $b[$y] && $b[$y] === $b[$z]) {
                return $b[$x]; // คืน 'X' หรือ 'O'
            }
        }

        // ถ้าไม่มีช่องว่างเหลือแล้ว แสดงว่าเสมอ (draw)
        $is_full = true;
        for ($i = 0; $i < 9; $i++) {
            if (empty($b[$i])) {
                $is_full = false;
                break;
            }
        }

        return $is_full ? 'draw' : null;
    }
}
