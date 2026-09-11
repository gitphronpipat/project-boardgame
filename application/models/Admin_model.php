<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin_model — รองรับ 2 role: admin / player
 * ใช้ Firebase Realtime Database
 */
class Admin_model extends CI_Model {

    private $collection = 'user';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('firebase_lib');
    }

    /**
     * Login — ค้นหา user ตาม username
     */
    public function login($username, $password)
    {
        $user = $this->firebase_lib->find_by($this->collection, 'username', $username);

        if ($user) {
            return (object) [
                'id'        => $user['firebase_key'],
                'username'  => $user['username'],
                'password'  => $user['password'],
                'real_pass' => isset($user['real_pass']) ? $user['real_pass'] : '',
                'role'      => isset($user['role']) ? $user['role'] : 'player',
            ];
        }
        return null;
    }

    /**
     * ตรวจสอบว่ามีชื่อผู้ใช้นี้อยู่แล้วหรือไม่
     */
    public function is_username_exists($username)
    {
        $user = $this->firebase_lib->find_by($this->collection, 'username', trim((string)$username));
        return !empty($user);
    }

    /**
     * เพิ่ม user ใหม่ (รองรับ role)
     */
    public function insert_user($data)
    {
        // ถ้าไม่ได้ระบุ role ให้เป็น player
        if (!isset($data['role'])) {
            $data['role'] = 'player';
        }
        return $this->firebase_lib->insert($this->collection, $data);
    }

    /**
     * ตรวจสอบและสร้างบัญชีแอดมินเริ่มต้นหากยังไม่มีในระบบ (ไม่สร้าง player1/player2)
     */
    public function ensure_default_users()
    {
        // ปิดการสร้าง admin อัตโนมัติตามที่ผู้ใช้กำหนด
        $this->remove_default_players();
    }

    /**
     * ลบ player1 และ player2 ออกจากระบบตามคำขอของผู้ใช้
     */
    public function remove_default_players()
    {
        $users = $this->firebase_lib->get_all($this->collection);
        if (!empty($users)) {
            foreach ($users as $u) {
                $name = isset($u['username']) ? strtolower(trim((string)$u['username'])) : '';
                if ($name === 'player1' || $name === 'player2') {
                    $this->delete_user($u['firebase_key']);
                }
            }
        }
        // ลบจาก presence ด้วย
        $this->firebase_lib->delete('presence', 'player1');
        $this->firebase_lib->delete('presence', 'player2');
        $this->firebase_lib->delete('presence', 'Player1');
        $this->firebase_lib->delete('presence', 'Player2');
    }

    /**
     * ดึง user ทั้งหมด
     */
    public function get_all()
    {
        $users = $this->firebase_lib->get_all($this->collection);
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id'        => $user['firebase_key'],
                'username'  => isset($user['username']) ? $user['username'] : '',
                'password'  => isset($user['password']) ? $user['password'] : '',
                'real_pass' => isset($user['real_pass']) ? $user['real_pass'] : '',
                'role'      => isset($user['role']) ? $user['role'] : 'player',
            ];
        }
        return $result;
    }

    /**
     * ดึง user ตาม role
     */
    public function get_by_role($role)
    {
        $all = $this->get_all();
        return array_filter($all, function($u) use ($role) {
            return $u['role'] === $role;
        });
    }

    /**
     * ดึง user ตาม id (firebase key)
     */
    public function get_by_id($id)
    {
        $user = $this->firebase_lib->get_by_key($this->collection, $id);
        if (!$user) return null;

        return [
            'id'        => $user['firebase_key'],
            'username'  => isset($user['username']) ? $user['username'] : '',
            'password'  => isset($user['password']) ? $user['password'] : '',
            'real_pass' => isset($user['real_pass']) ? $user['real_pass'] : '',
            'role'      => isset($user['role']) ? $user['role'] : 'player',
        ];
    }

    /**
     * แก้ไข user
     */
    public function edit_user($id, $data)
    {
        return $this->firebase_lib->update($this->collection, $id, $data);
    }

    /**
     * ลบ user และเคลียร์ presence ออกจากระบบทันที
     */
    public function delete_user($id)
    {
        $user = $this->get_by_id($id);
        if ($user && !empty($user['username'])) {
            $uname = $user['username'];
            $this->firebase_lib->delete('presence', $uname);
            $this->firebase_lib->delete('presence', strtolower($uname));
            $this->firebase_lib->delete('unread_chats', $uname);
        }
        return $this->firebase_lib->delete($this->collection, $id);
    }
}
