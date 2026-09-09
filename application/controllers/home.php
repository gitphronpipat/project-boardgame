<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Home — แดชบอร์ดจัดการระบบสำหรับ Admin
 * จัดการฐานข้อมูลและตารางทั้งหมด: Users, Roles, Presence, Lobbies, Rooms, Chats, Invites
 */
class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        // ตรวจสอบสิทธิ์ Admin เท่านั้น
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('result', 'false');
            $this->session->set_flashdata('message', 'เฉพาะแอดมินเท่านั้นที่สามารถเข้าถึงหน้านี้ได้');
            redirect('player');
        }

        $this->load->model('Admin_model', 'admin');
        $this->load->model('Game_model', 'game');
        $this->load->library('firebase_lib');
    }

    /**
     * หน้าแดชบอร์ดหลักของ Admin
     */
    public function index()
    {
        // 1. ดึงข้อมูลผู้ใช้ทั้งหมด
        $all_users = $this->admin->get_all();

        // รวบรวมรายชื่อ username ที่มีอยู่จริงในตาราง user
        $valid_usernames = [];
        foreach ($all_users as $u) {
            if (!empty($u['username'])) {
                $valid_usernames[strtolower(trim($u['username']))] = trim($u['username']);
            }
        }

        // 2. ดึงข้อมูล Presence ทั้งหมด และซิงค์: หากชื่อผู้ใช้ใดไม่มีอยู่ในตาราง user ให้ลบออกจาก presence ใน Firebase ทันที
        $presence_raw = $this->firebase_lib->get_all('presence');
        $filtered_presence = [];
        if (!empty($presence_raw)) {
            foreach ($presence_raw as $p) {
                $p_user = isset($p['username']) ? trim((string)$p['username']) : (isset($p['firebase_key']) ? trim((string)$p['firebase_key']) : '');
                if ($p_user !== '') {
                    if (!isset($valid_usernames[strtolower($p_user)])) {
                        // ผู้ใช้นี้ไม่มีในตาราง user แล้ว -> ลบออกจาก presence ทันทีเพื่อไม่ให้ค้าง
                        $this->firebase_lib->delete('presence', $p_user);
                        if (isset($p['firebase_key']) && $p['firebase_key'] !== $p_user) {
                            $this->firebase_lib->delete('presence', $p['firebase_key']);
                        }
                    } else {
                        $filtered_presence[] = $p;
                    }
                }
            }
        }
        $presence_raw = $filtered_presence;

        // 3. ดึงข้อมูลห้องล็อบบี้
        $lobbies_raw = $this->firebase_lib->get_all('lobbies');

        // 4. ดึงข้อมูลห้องเกมทั้งหมดจาก games/ (ครอบคลุมทุกเกม ไม่ใช่แค่ XO)
        $all_games_data = $this->firebase_lib->get_all('games');
        $active_game_rooms = [];

        if (!empty($all_games_data)) {
            foreach ($all_games_data as $game_type => $rooms) {
                if (is_array($rooms)) {
                    foreach ($rooms as $r_id => $r_data) {
                        if (is_array($r_data)) {
                            $r_data['game_type'] = $game_type;
                            $r_data['room_id'] = isset($r_data['id']) ? $r_data['id'] : $r_id;
                            $active_game_rooms[] = $r_data;
                        }
                    }
                }
            }
        }

        // รวมห้องจาก legacy xo_rooms ถ้ามี
        $xo_rooms_legacy = $this->firebase_lib->get_all('xo_rooms');
        if (!empty($xo_rooms_legacy)) {
            foreach ($xo_rooms_legacy as $r) {
                $r_id = isset($r['id']) ? $r['id'] : (isset($r['firebase_key']) ? $r['firebase_key'] : '');
                $exists = false;
                foreach ($active_game_rooms as $ag) {
                    if ($ag['room_id'] === $r_id) { $exists = true; break; }
                }
                if (!$exists && $r_id !== '') {
                    $r['game_type'] = 'xo';
                    $r['room_id'] = $r_id;
                    $active_game_rooms[] = $r;
                }
            }
        }

        // 4.1 ดึงข้อมูลสถิติผู้ชนะเกมเบื้องต้น (XO / Tic Tac Toe)
        $xo_scores_raw = $this->firebase_lib->get_all('game/xo');
        if (empty($xo_scores_raw)) {
            $xo_scores_raw = $this->firebase_lib->get_all('game/tictactoe');
        }

        // 5. ดึงข้อมูลคำเชิญเล่นเกม
        $invites_raw = $this->firebase_lib->get_all('invitations');

        // 6. ดึงข้อมูลห้องแชท
        $chats_raw = $this->firebase_lib->get_all('chats');

        // 7. ดึงรายการเกมทั้งหมด
        $games = $this->game->get_all();

        // สรุปสถิติต่างๆ
        $now = time();
        $admin_count = 0;
        $player_count = 0;
        foreach ($all_users as $u) {
            if (isset($u['role']) && $u['role'] === 'admin') {
                $admin_count++;
            } else {
                $player_count++;
            }
        }

        $online_count = 0;
        $playing_users = [];
        if (!empty($presence_raw)) {
            foreach ($presence_raw as $p) {
                $is_active = (isset($p['status']) && $p['status'] !== 'offline' && isset($p['last_active']) && ($now - (int)$p['last_active'] <= 300));
                if ($is_active) {
                    $online_count++;
                    if ($p['status'] === 'playing') {
                        $playing_users[] = $p;
                    }
                }
            }
        }

        $active_lobbies_count = !empty($lobbies_raw) ? count($lobbies_raw) : 0;
        $active_rooms_count = count($active_game_rooms);

        $data = [
            'title'              => 'ระบบจัดการข้อมูล (Admin Console)',
            'active_menu'        => 'admin',
            'content'            => 'admin/home',
            'users'              => $all_users,
            'presence'           => $presence_raw,
            'lobbies'            => $lobbies_raw,
            'game_rooms'         => $active_game_rooms,
            'playing_users'      => $playing_users,
            'xo_scores'          => $xo_scores_raw,
            'invites'            => $invites_raw,
            'chats'              => $chats_raw,
            'games'              => $games,
            'stats'              => [
                'total_users'     => count($all_users),
                'admin_count'     => $admin_count,
                'player_count'    => $player_count,
                'online_count'    => $online_count,
                'active_rooms'    => $active_lobbies_count + $active_rooms_count,
                'playing_count'   => count($playing_users),
                'games_count'     => count($games),
            ],
        ];

        $this->load->view('index', $data);
    }

    // ============================================================
    //  User Management Actions (เพิ่ม / แก้ไข / ลบ ผู้ใช้)
    // ============================================================

    /**
     * เพิ่มผู้ใช้ใหม่ (เลือก role ได้)
     */
    public function add_user()
    {
        if ($this->input->method() === 'post') {
            $username = trim((string)$this->input->post('username'));
            $realpass = trim((string)$this->input->post('password'));
            $role     = $this->input->post('role') ? $this->input->post('role') : 'player';

            if (empty($username) || empty($realpass)) {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน');
                redirect('home');
            }

            if ($this->admin->is_username_exists($username)) {
                $this->session->set_flashdata('result', 'duplicate');
                $this->session->set_flashdata('message', 'ชื่อผู้ใช้ "' . $username . '" มีอยู่ในระบบแล้ว');
                redirect('home');
            }

            $this->admin->insert_user([
                'username'  => $username,
                'password'  => password_hash($realpass, PASSWORD_DEFAULT),
                'real_pass' => $realpass,
                'role'      => $role,
            ]);

            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'เพิ่มผู้ใช้ ' . $username . ' (' . $role . ') สำเร็จ');
        }
        redirect('home');
    }

    /**
     * แก้ไขข้อมูลผู้ใช้ (เปลี่ยน username, password, role)
     */
    public function edit_user()
    {
        if ($this->input->method() === 'post') {
            $id       = $this->input->post('id');
            $username = trim((string)$this->input->post('username'));
            $realpass = trim((string)$this->input->post('password'));
            $role     = $this->input->post('role') ? $this->input->post('role') : 'player';

            if (!$id || empty($username)) {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'ข้อมูลไม่ถูกต้อง');
                redirect('home');
            }

            $update_data = [
                'username' => $username,
                'role'     => $role,
            ];

            if (!empty($realpass)) {
                $update_data['password']  = password_hash($realpass, PASSWORD_DEFAULT);
                $update_data['real_pass'] = $realpass;
            }

            $this->admin->edit_user($id, $update_data);

            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'แก้ไขข้อมูลผู้ใช้ ' . $username . ' สำเร็จ');
        }
        redirect('home');
    }

    /**
     * ลบผู้ใช้
     */
    public function delete_user($id = null)
    {
        if ($id) {
            $user = $this->admin->get_by_id($id);
            $uname = $user ? $user['username'] : '';

            // ป้องกันการลบตัวเองที่กำลังล็อกอินอยู่
            if ($uname === $this->session->userdata('username')) {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'ไม่สามารถลบบัญชีแอดมินที่คุณกำลังใช้งานอยู่ได้');
                redirect('home');
            }

            $this->admin->delete_user($id);

            // ลบ presence ด้วย
            if ($uname) {
                $this->firebase_lib->delete('presence', $uname);
            }

            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'ลบผู้ใช้เรียบร้อยแล้ว');
        }
        redirect('home');
    }

    // ============================================================
    //  Database Cleaners & Resets
    // ============================================================

    /**
     * รีเซ็ตสถานะ Presence ของผู้ใช้ให้เป็น Offline
     */
    public function reset_presence($username = null)
    {
        if ($username) {
            $this->firebase_lib->update('presence', $username, [
                'status'      => 'offline',
                'game'        => '',
                'room_id'     => '',
                'last_active' => 0,
            ]);
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'รีเซ็ตสถานะของ ' . $username . ' เป็นออฟไลน์แล้ว');
        }
        redirect('home');
    }

    /**
     * ลบ Presence ของผู้ใช้โดยตรง (เช่น ผู้ใช้ที่ค้างหรือลบออกจากระบบไปแล้ว)
     */
    public function delete_presence($username = null)
    {
        if ($username) {
            $this->firebase_lib->delete('presence', $username);
            $this->firebase_lib->delete('presence', strtolower($username));
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'ลบ Presence ของ ' . $username . ' ออกจากระบบแล้ว');
        }
        redirect('home');
    }

    /**
     * ลบห้องล็อบบี้
     */
    public function delete_lobby($room_id = null)
    {
        if ($room_id) {
            $this->firebase_lib->delete('lobbies', $room_id);
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'ลบห้องล็อบบี้ ' . $room_id . ' สำเร็จ');
        }
        redirect('home');
    }

    /**
     * ลบห้องเกมใดๆ ในระบบ (ทุกเกม)
     */
    public function delete_room($game_type = null, $room_id = null)
    {
        if ($room_id === null && $game_type !== null) {
            $room_id = $game_type;
            $game_type = null;
        }

        if ($room_id) {
            if ($game_type) {
                $this->firebase_lib->delete('games/' . $game_type, $room_id);
            }
            $this->firebase_lib->delete('games/xo', $room_id);
            $this->firebase_lib->delete('games/tictactoe', $room_id);
            $this->firebase_lib->delete('xo_rooms', $room_id);
            $this->firebase_lib->delete('lobbies', $room_id);
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'ลบห้องเกม ' . $room_id . ' สำเร็จ');
        }
        redirect('home');
    }

    /**
     * ล้างประวัติคำเชิญทั้งหมด
     */
    public function clear_invites()
    {
        $this->firebase_lib->delete('invitations', '');
        $this->session->set_flashdata('result', 'true');
        $this->session->set_flashdata('message', 'ล้างประวัติคำเชิญทั้งหมดเรียบร้อยแล้ว');
        redirect('home');
    }

    /**
     * ล้างข้อความแชททั้งหมด
     */
    public function clear_chats()
    {
        $this->firebase_lib->delete('chats', '');
        $this->firebase_lib->delete('unread_chats', '');
        $this->session->set_flashdata('result', 'true');
        $this->session->set_flashdata('message', 'ล้างข้อความแชทและแจ้งเตือนทั้งหมดเรียบร้อยแล้ว');
        redirect('home');
    }

    /**
     * ล้างห้องล็อบบี้ทั้งหมด (Clear all lobbies)
     */
    public function clear_all_lobbies()
    {
        $this->firebase_lib->delete('lobbies', '');
        $this->session->set_flashdata('result', 'true');
        $this->session->set_flashdata('message', 'ล้างห้องล็อบบี้ทั้งหมดเรียบร้อยแล้ว');
        redirect('home');
    }

    /**
     * ดึงข้อความแชทสำหรับ Admin ดูรายละเอียด
     */
    public function get_chat_messages($channel = null)
    {
        if (!$channel) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

        $messages = $this->firebase_lib->get_all('chats/' . $channel . '/messages');
        $result = [];
        if (!empty($messages)) {
            usort($messages, function ($a, $b) {
                $ta = isset($a['time']) ? (int)$a['time'] : 0;
                $tb = isset($b['time']) ? (int)$b['time'] : 0;
                return $ta - $tb;
            });
            $result = array_values($messages);
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * ดึงสถิติจำนวนครั้งที่ชนะตามเกมที่เลือก (Dropdown)
     */
    public function get_game_scores($game_key = 'xo')
    {
        if (!$game_key) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

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
        if (!empty($scores)) {
            foreach ($scores as $s) {
                $list[] = $s;
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
