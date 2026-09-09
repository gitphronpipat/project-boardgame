<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Player extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // ต้อง login ก่อน
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
            redirect('auth/login');
        }
        $this->load->model('Game_model', 'game');
    }

    /**
     * หน้าเลือกเกม — Game Grid พร้อมตาราง Ranking
     */
    public function index()
    {
        $this->load->library('firebase_lib');
        $games = $this->game->get_all();

        // ดึงสถิติตั้งต้นสำหรับตาราง Ranking (OX / Tic Tac Toe)
        $scores = $this->firebase_lib->get_all('game/xo');
        if (empty($scores)) {
            $scores = $this->firebase_lib->get_all('game/tictactoe');
        }

        $ranking_list = [];
        if (!empty($scores) && is_array($scores)) {
            foreach ($scores as $s) {
                if (is_array($s)) {
                    $ranking_list[] = $s;
                }
            }
            usort($ranking_list, function($a, $b) {
                $wa = isset($a['wins']) ? (int)$a['wins'] : 0;
                $wb = isset($b['wins']) ? (int)$b['wins'] : 0;
                return $wb - $wa;
            });
        }

        $data = [
            'title'          => 'เลือกเกม',
            'games'          => $games,
            'default_game'   => 'tictactoe',
            'ranking_scores' => $ranking_list,
        ];
        $this->load->view('player/player_layout', $data);
    }

    /**
     * หน้า Lobby — รอเพื่อนเข้าร่วมก่อนเริ่มเกม
     */
    public function lobby($game_key = null, $room_id = null)
    {
        $game = $this->game->get_by_key($game_key);

        if (!$game) {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'ไม่พบเกมนี้');
            redirect('player');
        }

        $this->load->library('firebase_lib');
        $username = $this->session->userdata('username');

        if (empty($room_id)) {
            $room_id = 'room_' . $game_key . '_' . time() . '_' . rand(100, 999);
            // สร้างห้องล็อบบี้ใน Firebase ทันที
            $this->firebase_lib->update('lobbies', $room_id, [
                'room_id'  => $room_id,
                'game_key' => $game_key,
                'host'     => $username,
                'players'  => [$username],
                'status'   => 'waiting',
                'created'  => time(),
            ]);
            redirect('player/lobby/' . $game_key . '/' . $room_id);
            return;
        } else {
            // ตรวจสอบสถานะห้อง
            $existing_lobby = $this->firebase_lib->get_by_key('lobbies', $room_id);
            if (!$existing_lobby) {
                // 1. ตรวจสอบว่ามีห้องใน games/xo อยู่หรือไม่ (กรณีเพื่อนกดรับคำเชิญจากในเกม OX โดยตรง)
                $xo_room = $this->firebase_lib->get_by_key('games/xo', $room_id);
                if ($xo_room) {
                    redirect('xo/room/' . $room_id);
                    return;
                }

                // 2. หากไม่พบห้องใน lobbies ให้สร้างห้องใหม่ด้วย room_id นี้ เพื่อรองรับผู้เล่นเข้าเล่น ไม่เตะออก
                $this->firebase_lib->update('lobbies', $room_id, [
                    'room_id'  => $room_id,
                    'game_key' => $game_key,
                    'host'     => $username,
                    'players'  => [$username],
                    'status'   => 'waiting',
                    'created'  => time(),
                ]);
            } else {
                // ถ้าห้องนี้ Host กดเริ่มเกมไปแล้ว ให้พาเข้าเกมเลยทันที
                if (isset($existing_lobby['status']) && $existing_lobby['status'] === 'started' && !empty($existing_lobby['redirect_url'])) {
                    redirect($existing_lobby['redirect_url']);
                    return;
                }
            }
        }

        // อัปเดตสถานะผู้ใช้ว่าอยู่ที่ล็อบบี้เกมนี้
        $this->firebase_lib->update('presence', $username, [
            'username'    => $username,
            'status'      => 'playing',
            'game'        => 'Lobby ' . $game['name'],
            'room_id'     => $room_id,
            'last_active' => time(),
        ]);

        // ดึงสถิติของฉันในเกมนี้ (ชนะ, เล่น)
        $score_key = ($game_key === 'tictactoe') ? 'xo' : $game_key;
        $user_stats = $this->firebase_lib->get_by_key('game/' . $score_key, $username);
        if (empty($user_stats) && $game_key === 'tictactoe') {
            $user_stats = $this->firebase_lib->get_by_key('game/tictactoe', $username);
        }
        $my_wins = (is_array($user_stats) && isset($user_stats['wins'])) ? (int)$user_stats['wins'] : 0;
        $my_losses = (is_array($user_stats) && isset($user_stats['losses'])) ? (int)$user_stats['losses'] : 0;
        $my_played = (is_array($user_stats) && isset($user_stats['played'])) ? (int)$user_stats['played'] : ($my_wins + $my_losses);

        $data = [
            'title'      => $game['name'] . ' — Lobby',
            'game'       => $game,
            'game_key'   => $game_key,
            'room_id'    => $room_id,
            'user_stats' => [
                'wins'   => $my_wins,
                'losses' => $my_losses,
                'played' => $my_played,
            ],
        ];
        $this->load->view('player/lobby', $data);
    }

    // ============================================================
    //  API: Presence & Online Users
    // ============================================================

    /**
     * Heartbeat อัปเดตสถานะออนไลน์ / กำลังเล่นเกม
     */
    public function heartbeat()
    {
        $username = $this->session->userdata('username');
        if (!$username) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error']));
        }

        $this->load->library('firebase_lib');
        $status  = $this->input->post('status') ? $this->input->post('status') : 'online';
        $game    = $this->input->post('game') ? $this->input->post('game') : '';
        $room_id = $this->input->post('room_id') ? $this->input->post('room_id') : '';

        $data = [
            'username'    => $username,
            'status'      => $status, // 'online' | 'playing' | 'offline'
            'game'        => $game,
            'room_id'     => $room_id,
            'last_active' => time(),
        ];

        $this->firebase_lib->update('presence', $username, $data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok']));
    }

    /**
     * ดึงรายชื่อเพื่อนและสถานะออนไลน์ทั้งหมด
     */
    public function get_online_users()
    {
        $current_user = $this->session->userdata('username');
        $this->load->model('Admin_model', 'admin');
        $this->load->library('firebase_lib');

        $all_users = $this->admin->get_all();
        $presence_data = $this->firebase_lib->get_all('presence');

        // จัด group presence ตาม username (case-insensitive)
        $presence_map = [];
        if (!empty($presence_data)) {
            foreach ($presence_data as $p) {
                $p_user = isset($p['username']) ? trim((string)$p['username']) : (isset($p['firebase_key']) ? trim((string)$p['firebase_key']) : '');
                if ($p_user !== '') {
                    $presence_map[$p_user] = $p;
                    $presence_map[strtolower($p_user)] = $p;
                }
            }
        }

        $now = time();
        $online_count = 0;
        $users_list = [];
        $processed_users = [];

        // ดึงข้อมูลข้อความแชทที่ยังไม่ได้อ่านของผู้ใช้ปัจจุบัน
        $unread_chats = [];
        $total_unread_chats = 0;
        if ($current_user) {
            $unread_data = $this->firebase_lib->get_all('unread_chats/' . $current_user);
            if (!empty($unread_data)) {
                foreach ($unread_data as $u_chat) {
                    $s = isset($u_chat['sender']) ? trim((string)$u_chat['sender']) : (isset($u_chat['firebase_key']) ? trim((string)$u_chat['firebase_key']) : '');
                    if ($s !== '') {
                        $c = isset($u_chat['count']) ? (int)$u_chat['count'] : 0;
                        if ($c > 0) {
                            $unread_chats[$s] = $u_chat;
                            $unread_chats[strtolower($s)] = $u_chat;
                            $total_unread_chats += $c;
                        }
                    }
                }
            }
        }

        foreach ($all_users as $u) {
            $uname = trim((string)$u['username']);
            if (empty($uname)) continue;
            $uname_lower = strtolower($uname);
            $processed_users[$uname_lower] = true;

            $p = isset($presence_map[$uname]) ? $presence_map[$uname] : (isset($presence_map[$uname_lower]) ? $presence_map[$uname_lower] : null);
            $is_self = ($current_user && strcasecmp($uname, $current_user) === 0);

            // เช็คว่า active: ตัวเอง online เสมอ, เพื่อน active ภายใน 300 วินาที (5 นาที) และไม่ใช่ offline
            if ($is_self) {
                $is_active = true;
                $status = ($p && isset($p['status']) && $p['status'] === 'playing') ? 'playing' : 'online';
                $online_count++;
            } else {
                $is_active = $p && isset($p['last_active']) && ($now - (int)$p['last_active'] <= 300) && (isset($p['status']) && $p['status'] !== 'offline');
                if ($is_active) {
                    $status = (isset($p['status']) && $p['status'] === 'playing') ? 'playing' : 'online';
                    $online_count++;
                } else {
                    $status = 'offline';
                }
            }

            // ตรวจสอบจำนวนข้อความที่ยังไม่ได้อ่านจากเพื่อนคนนี้
            $unread_count = 0;
            $last_unread_msg = '';
            if (isset($unread_chats[$uname])) {
                $unread_count = (int)$unread_chats[$uname]['count'];
                $last_unread_msg = isset($unread_chats[$uname]['last_message']) ? $unread_chats[$uname]['last_message'] : '';
            } elseif (isset($unread_chats[$uname_lower])) {
                $unread_count = (int)$unread_chats[$uname_lower]['count'];
                $last_unread_msg = isset($unread_chats[$uname_lower]['last_message']) ? $unread_chats[$uname_lower]['last_message'] : '';
            }

            $users_list[] = [
                'username'            => $uname,
                'role'                => isset($u['role']) ? $u['role'] : 'player',
                'status'              => $status,
                'game'                => ($p && isset($p['game'])) ? $p['game'] : '',
                'room_id'             => ($p && isset($p['room_id'])) ? $p['room_id'] : '',
                'is_self'             => $is_self,
                'unread_count'        => $unread_count,
                'last_unread_message' => $last_unread_msg,
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'online_count'       => $online_count,
                'total_unread_chats' => $total_unread_chats,
                'users'              => $users_list,
            ]));
    }

    // ============================================================
    //  API: Invitations (คำเชิญเล่นเกม)
    // ============================================================

    /**
     * ส่งคำเชิญเล่นเกมให้เพื่อน
     */
    public function send_invite()
    {
        $from_user = $this->session->userdata('username');
        $to_user   = $this->input->post('to_username');
        $game_key  = $this->input->post('game_key');
        $game_name = $this->input->post('game_name');
        $room_id   = $this->input->post('room_id');

        if (!$from_user || !$to_user) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']));
        }

        $this->load->library('firebase_lib');

        $invite_id = 'inv_' . time() . '_' . rand(100, 999);
        $invite_data = [
            'id'        => $invite_id,
            'from'      => $from_user,
            'to'        => $to_user,
            'game_key'  => $game_key,
            'game_name' => $game_name,
            'room_id'   => $room_id,
            'time'      => time(),
            'status'    => 'pending', // pending | accepted | declined
        ];

        // บันทึกลง invitations/{to_user}/{invite_id}
        $this->firebase_lib->update('invitations/' . $to_user, $invite_id, $invite_data);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok', 'message' => 'ส่งคำเชิญเรียบร้อยแล้ว']));
    }

    /**
     * ดึงคำเชิญที่ส่งมาหาฉัน
     */
    public function get_invitations()
    {
        $current_user = $this->session->userdata('username');
        if (!$current_user) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

        $this->load->library('firebase_lib');
        $invites = $this->firebase_lib->get_all('invitations/' . $current_user);

        $now = time();
        $pending = [];
        if (!empty($invites)) {
            foreach ($invites as $inv) {
                // แสดงเฉพาะคำเชิญที่ยัง pending และอายุไม่เกิน 3 นาที (180 วิ)
                if (isset($inv['status']) && $inv['status'] === 'pending' && isset($inv['time']) && ($now - (int)$inv['time'] <= 180)) {
                    $pending[] = $inv;
                }
            }
        }

        // เรียงลำดับคำเชิญล่าสุดให้อยู่บนสุด (Newest First)
        usort($pending, function($a, $b) {
            $ta = isset($a['time']) ? (int)$a['time'] : 0;
            $tb = isset($b['time']) ? (int)$b['time'] : 0;
            return $tb - $ta;
        });

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array_values($pending)));
    }

    /**
     * ตอบรับ / ปฏิเสธคำเชิญ
     */
    public function respond_invite()
    {
        $current_user = $this->session->userdata('username');
        $invite_id    = $this->input->post('invite_id');
        $action       = $this->input->post('action'); // 'accept' | 'decline'

        if (!$current_user || !$invite_id) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error']));
        }

        $this->load->library('firebase_lib');

        if ($action === 'accept') {
            $invite = $this->firebase_lib->get_by_key('invitations/' . $current_user, $invite_id);
            $this->firebase_lib->update('invitations/' . $current_user, $invite_id, ['status' => 'accepted']);

            $redirect_url = base_url('player');
            if ($invite) {
                $game_key = ($invite['game_key'] === 'xo') ? 'tictactoe' : $invite['game_key'];
                $target_room_id = $invite['room_id'];

                // ตรวจสอบว่าห้องนี้เปิดเล่นอยู่ใน games/xo แล้วหรือไม่
                $xo_room = $this->firebase_lib->get_by_key('games/xo', $target_room_id);
                if ($xo_room) {
                    $redirect_url = base_url('xo/room/' . $target_room_id);
                } else {
                    $lobby = $this->firebase_lib->get_by_key('lobbies', $target_room_id);
                    if ($lobby && isset($lobby['status']) && $lobby['status'] === 'started' && !empty($lobby['redirect_url'])) {
                        $redirect_url = $lobby['redirect_url'];
                    } else {
                        $redirect_url = base_url('player/lobby/' . $game_key . '/' . $target_room_id);
                    }
                }
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'       => 'ok',
                    'action'       => 'accepted',
                    'redirect_url' => $redirect_url,
                ]));
        } else {
            $this->firebase_lib->update('invitations/' . $current_user, $invite_id, ['status' => 'declined']);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'ok', 'action' => 'declined']));
        }
    }

    // ============================================================
    //  API: Lobby Synchronization (ซิงค์สถานะห้องล็อบบี้)
    // ============================================================

    /**
     * ดึงสถานะห้องล็อบบี้ และเพิ่มผู้เล่นเข้าห้อง
     */
    public function get_lobby_state($room_id)
    {
        $current_user = $this->session->userdata('username');
        if (!$current_user || !$room_id) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error']));
        }

        $this->load->library('firebase_lib');
        $lobby = $this->firebase_lib->get_by_key('lobbies', $room_id);

        if (!$lobby) {
            // ตรวจสอบว่าโฮสต์กดเริ่มเกมและห้องเปลี่ยนเป็นเกม OX แล้วหรือยัง
            $xo_room = $this->firebase_lib->get_by_key('games/xo', $room_id);
            if ($xo_room) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'       => 'started',
                    'redirect_url' => base_url('xo/room/' . $room_id),
                ]));
            }

            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'closed',
                'message' => 'ห้องล็อบบี้นี้ถูกปิดหรือออกจากห้องแล้ว',
            ]));
        }

        // หากมีห้องอยู่แล้ว และผู้เล่นยังไม่อยู่ในรายชื่อผู้เล่น ให้เพิ่มเข้าไป
        $players = isset($lobby['players']) && is_array($lobby['players']) ? $lobby['players'] : [];
        if (!in_array($current_user, $players)) {
            $players[] = $current_user;
            $lobby['players'] = $players;
            $this->firebase_lib->update('lobbies', $room_id, ['players' => $players]);
        }

        // ดึงสถิติของแต่ละผู้เล่นในเกมนี้เพื่อส่งไปแสดงในช่องผู้เล่นของล็อบบี้
        $game_key = isset($lobby['game_key']) ? $lobby['game_key'] : ($this->input->get('game_key') ? $this->input->get('game_key') : 'xo');
        $stats_map = [];
        foreach ($players as $p) {
            $stat = $this->firebase_lib->get_by_key('game/' . $game_key, $p);
            if (!$stat && ($game_key === 'tictactoe' || $game_key === 'xo')) {
                $alt_key = ($game_key === 'tictactoe') ? 'xo' : 'tictactoe';
                $stat = $this->firebase_lib->get_by_key('game/' . $alt_key, $p);
            }
            $stats_map[$p] = [
                'wins'   => (is_array($stat) && isset($stat['wins'])) ? (int)$stat['wins'] : 0,
                'losses' => (is_array($stat) && isset($stat['losses'])) ? (int)$stat['losses'] : 0,
                'played' => (is_array($stat) && isset($stat['played'])) ? (int)$stat['played'] : 0,
            ];
        }
        $lobby['player_stats'] = $stats_map;

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($lobby));
    }

    /**
     * ออกจากห้องล็อบบี้ — ลบห้องทิ้งเมื่อโฮสต์ออก หรือไม่มีผู้เล่นเหลือ (ยกเว้นตอนที่เกมเริ่มแล้ว)
     */
    public function leave_lobby($room_id = null)
    {
        $current_user = $this->session->userdata('username');
        if ($room_id) {
            $this->load->library('firebase_lib');
            $lobby = $this->firebase_lib->get_by_key('lobbies', $room_id);
            if ($lobby) {
                // ถ้าเกมเริ่มแล้ว (status === 'started') ห้ามลบห้องเด็ดขาด! เพราะผู้เล่นกำลังย้ายเข้าหน้าเกม
                if (isset($lobby['status']) && $lobby['status'] === 'started') {
                    if ($this->input->is_ajax_request()) {
                        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'ok']));
                    }
                    redirect('player');
                    return;
                }

                $is_host = (isset($lobby['host']) && $lobby['host'] === $current_user);
                $players = isset($lobby['players']) && is_array($lobby['players']) ? $lobby['players'] : [];

                // ถ้าเป็น Host หรือไม่มีผู้เล่นอื่นเหลือ ให้ลบห้องทิ้ง
                if ($is_host || count($players) <= 1) {
                    $this->firebase_lib->delete('lobbies', $room_id);
                } else {
                    // หากเป็นผู้เล่นคนอื่น ให้ออกจากห้อง
                    $players = array_values(array_filter($players, function($p) use ($current_user) {
                        return $p !== $current_user;
                    }));
                    $this->firebase_lib->update('lobbies', $room_id, ['players' => $players]);
                }
            }

            // อัปเดตสถานะ Presence เป็น Online ว่าง
            if ($current_user) {
                $this->firebase_lib->update('presence', $current_user, [
                    'status'      => 'online',
                    'game'        => '',
                    'room_id'     => '',
                    'last_active' => time(),
                ]);
            }
        }

        if ($this->input->is_ajax_request()) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'ok']));
        }
        redirect('player');
    }

    /**
     * Host กดเริ่มเกมจากห้องล็อบบี้
     */
    public function start_lobby_game($room_id)
    {
        $current_user = $this->session->userdata('username');
        $this->load->library('firebase_lib');

        $lobby = $this->firebase_lib->get_by_key('lobbies', $room_id);
        if (!$lobby) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่พบห้องนี้']));
        }

        // กำหนด URL ของเกมที่จะเล่น
        $game_key = isset($lobby['game_key']) ? $lobby['game_key'] : 'tictactoe';
        $redirect_url = base_url('xo/room/' . $room_id);
        if ($game_key !== 'tictactoe' && $game_key !== 'xo') {
            $redirect_url = base_url('player/lobby/' . $game_key . '/' . $room_id);
        }

        $players = isset($lobby['players']) && is_array($lobby['players']) ? $lobby['players'] : [$lobby['host']];
        $host = isset($lobby['host']) ? $lobby['host'] : $current_user;
        $guest = (count($players) > 1) ? $players[1] : null;

        // ถ้าเป็นเกม OX ให้สร้างห้องเกม games/xo/{room_id} ใน Firebase รอไว้ล่วงหน้าทันที
        if ($game_key === 'tictactoe' || $game_key === 'xo') {
            $this->firebase_lib->update('games/xo', $room_id, [
                'id'         => $room_id,
                'host'       => $host,
                'player_x'   => $host,
                'player_o'   => $guest,
                'board'      => ['', '', '', '', '', '', '', '', ''],
                'turn'       => 'X',
                'status'     => !empty($guest) ? 'playing' : 'waiting',
                'winner'     => null,
                'score_x'    => 0,
                'score_o'    => 0,
                'created_at' => time(),
            ]);
        }

        $this->firebase_lib->update('lobbies', $room_id, [
            'status'       => 'started',
            'redirect_url' => $redirect_url,
        ]);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'       => 'ok',
                'redirect_url' => $redirect_url,
            ]));
    }

    // ============================================================
    //  API: Chat (กล่องข้อความส่งหาเพื่อน)
    // ============================================================

    /**
     * ส่งข้อความแชท
     */
    public function send_chat()
    {
        $from_user = $this->session->userdata('username');
        $to_user   = $this->input->post('to_username');
        $message   = trim((string)$this->input->post('message'));

        if (!$from_user || !$to_user || empty($message)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error']));
        }

        $this->load->library('firebase_lib');

        // สร้าง channel key ร่วมกัน เช่น "chat_alice_bob"
        $users = [$from_user, $to_user];
        sort($users);
        $channel = 'chat_' . $users[0] . '_' . $users[1];

        $msg_id = 'msg_' . time() . '_' . rand(100, 999);
        $msg_data = [
            'id'        => $msg_id,
            'sender'    => $from_user,
            'recipient' => $to_user,
            'message'   => htmlspecialchars($message),
            'time'      => time(),
        ];

        $this->firebase_lib->update('chats/' . $channel . '/messages', $msg_id, $msg_data);

        // บันทึกและเพิ่มจำนวนข้อความที่ยังไม่ได้อ่านให้ผู้รับ ($to_user)
        $current_unread = $this->firebase_lib->get_by_key('unread_chats/' . $to_user, $from_user);
        $prev_count = (is_array($current_unread) && isset($current_unread['count'])) ? (int)$current_unread['count'] : 0;
        $this->firebase_lib->update('unread_chats/' . $to_user, $from_user, [
            'sender'       => $from_user,
            'count'        => $prev_count + 1,
            'last_message' => mb_substr($message, 0, 50),
            'time'         => time(),
        ]);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok', 'message' => $msg_data]));
    }

    /**
     * ดึงข้อความแชท และเคลียร์สถานะยังไม่ได้อ่าน
     */
    public function get_chat()
    {
        $from_user = $this->session->userdata('username');
        $to_user   = $this->input->get('to_username');

        if (!$from_user || !$to_user) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

        $this->load->library('firebase_lib');

        // เคลียร์ unread counter เมื่อเปิดอ่านแชทกับเพื่อนคนนี้
        $this->firebase_lib->delete('unread_chats/' . $from_user, $to_user);

        $users = [$from_user, $to_user];
        sort($users);
        $channel = 'chat_' . $users[0] . '_' . $users[1];

        $messages = $this->firebase_lib->get_all('chats/' . $channel . '/messages');

        // sort ตามเวลา
        usort($messages, function ($a, $b) {
            $ta = isset($a['time']) ? (int)$a['time'] : 0;
            $tb = isset($b['time']) ? (int)$b['time'] : 0;
            return $ta - $tb;
        });

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array_values($messages)));
    }

    /**
     * เคลียร์สถานะข้อความที่ยังไม่ได้อ่าน
     */
    public function mark_chat_read()
    {
        $current_user = $this->session->userdata('username');
        $with_user    = $this->input->post('with_username');

        if (!$current_user || !$with_user) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error']));
        }

        $this->load->library('firebase_lib');
        $this->firebase_lib->delete('unread_chats/' . $current_user, $with_user);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'ok']));
    }

    // ============================================================
    //  PROFILE MANAGEMENT (แก้ไขชื่อผู้ใช้และรหัสผ่าน)
    // ============================================================

    /**
     * ดึงข้อมูลโปรไฟล์ของผู้ใช้ปัจจุบัน
     */
    public function get_profile()
    {
        $current_user = $this->session->userdata('username');
        if (!$current_user) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'ยังไม่ได้เข้าสู่ระบบ',
            ]));
        }

        $this->load->model('Admin_model', 'admin');
        $user_id = $this->session->userdata('id');
        $user = null;

        if ($user_id) {
            $user = $this->admin->get_by_id($user_id);
        }

        if (!$user) {
            $user_obj = $this->admin->login($current_user, '');
            if ($user_obj) {
                $user = [
                    'id'        => $user_obj->id,
                    'username'  => $user_obj->username,
                    'real_pass' => $user_obj->real_pass,
                    'role'      => $user_obj->role,
                ];
            }
        }

        if (!$user) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'ไม่พบข้อมูลผู้ใช้ในระบบ',
            ]));
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'   => 'ok',
            'username' => $user['username'],
            'password' => isset($user['real_pass']) ? $user['real_pass'] : '',
            'role'     => isset($user['role']) ? $user['role'] : 'player',
        ]));
    }

    /**
     * อัปเดตข้อมูลโปรไฟล์ (เปลี่ยนชื่อผู้ใช้ / รหัสผ่าน)
     */
    public function update_profile()
    {
        $current_user = $this->session->userdata('username');
        if (!$current_user) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'กรุณาเข้าสู่ระบบก่อนทำรายการ',
            ]));
        }

        $new_username = trim((string)$this->input->post('username'));
        $new_password = trim((string)$this->input->post('password'));

        if (empty($new_username)) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'กรุณากรอกชื่อผู้ใช้',
            ]));
        }

        $this->load->model('Admin_model', 'admin');
        $this->load->library('firebase_lib');

        // ค้นหา ID ผู้ใช้
        $user_id = $this->session->userdata('id');
        $user_data = null;
        if ($user_id) {
            $user_data = $this->admin->get_by_id($user_id);
        }
        if (!$user_data) {
            $user_obj = $this->admin->login($current_user, '');
            if ($user_obj) {
                $user_id   = $user_obj->id;
                $user_data = [
                    'id'        => $user_obj->id,
                    'username'  => $user_obj->username,
                    'real_pass' => $user_obj->real_pass,
                    'role'      => $user_obj->role,
                ];
            }
        }

        if (!$user_id) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'error',
                'message' => 'ไม่พบบัญชีผู้ใช้ในระบบ',
            ]));
        }

        // หากมีการเปลี่ยนชื่อผู้ใช้ ให้ตรวจสอบว่าชื่อใหม่ซ้ำกับผู้อื่นหรือไม่
        if (strtolower($new_username) !== strtolower($current_user)) {
            if ($this->admin->is_username_exists($new_username)) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'ชื่อผู้ใช้ "' . $new_username . '" มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น',
                ]));
            }
        }

        // เตรียมข้อมูลอัปเดต
        $update_payload = [
            'username' => $new_username,
        ];
        if (!empty($new_password)) {
            $update_payload['password']  = password_hash($new_password, PASSWORD_DEFAULT);
            $update_payload['real_pass'] = $new_password;
        }

        $this->admin->edit_user($user_id, $update_payload);

        // หากเปลี่ยนชื่อผู้ใช้ ให้ย้ายข้อมูลที่เกี่ยวข้อง
        if ($new_username !== $current_user) {
            // 1. อัปเดต Session
            $this->session->set_userdata('username', $new_username);

            // 2. เคลียร์ Presence เก่า และสร้างใหม่
            $this->firebase_lib->delete('presence', $current_user);
            $this->firebase_lib->delete('presence', strtolower($current_user));
            $this->firebase_lib->update('presence', $new_username, [
                'username'    => $new_username,
                'status'      => 'online',
                'game'        => '',
                'room_id'     => '',
                'last_active' => time(),
            ]);

            // 3. ย้ายสถิติเกมทั้งหมด (xo, chess, othello, ฯลฯ)
            $game_keys = ['xo', 'tictactoe', 'chess', 'othello', 'connect4'];
            foreach ($game_keys as $gkey) {
                $stat = $this->firebase_lib->get_by_key('game/' . $gkey, $current_user);
                if ($stat && is_array($stat)) {
                    $stat['username'] = $new_username;
                    $this->firebase_lib->update('game/' . $gkey, $new_username, $stat);
                    $this->firebase_lib->delete('game/' . $gkey, $current_user);
                }
            }

            // 4. ย้าย unread_chats
            $unread = $this->firebase_lib->get_by_key('unread_chats', $current_user);
            if ($unread) {
                $this->firebase_lib->update('unread_chats', $new_username, $unread);
                $this->firebase_lib->delete('unread_chats', $current_user);
            }
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'       => 'ok',
            'message'      => 'บันทึกข้อมูลส่วนตัวเรียบร้อยแล้ว',
            'new_username' => $new_username,
        ]));
    }

    /**
     * ดึงสถิติจำนวนครั้งที่ชนะตามเกมที่เลือก สำหรับตาราง Ranking (AJAX)
     */
    public function get_game_scores($game_key = 'xo')
    {
        if (!$game_key) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

        $this->load->library('firebase_lib');
        $scores = $this->firebase_lib->get_all('game/' . $game_key);
        // สลับ fallback ระหว่าง xo และ tictactoe
        if (empty($scores)) {
            if ($game_key === 'tictactoe') {
                $scores = $this->firebase_lib->get_all('game/xo');
            } elseif ($game_key === 'xo') {
                $scores = $this->firebase_lib->get_all('game/tictactoe');
            }
        }

        $list = [];
        if (!empty($scores) && is_array($scores)) {
            foreach ($scores as $s) {
                if (is_array($s)) {
                    $list[] = $s;
                }
            }
            // เรียงลำดับจากชนะมากไปน้อย
            usort($list, function($a, $b) {
                $wa = isset($a['wins']) ? (int)$a['wins'] : 0;
                $wb = isset($b['wins']) ? (int)$b['wins'] : 0;
                return $wb - $wa;
            });
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array_values($list)));
    }
}
