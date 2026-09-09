<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model', 'admin');
    }

    // ============================================================
    //  LOGIN — หน้าเดียว ทั้ง admin และ player
    // ============================================================
    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            redirect($role === 'admin' ? '/' : 'player');
        }

        if ($this->input->method() === 'post') {
            $username = trim((string)$this->input->post('username'));
            $password = trim((string)$this->input->post('password'));

            $user = $this->admin->login($username, $password);

            $login_success = false;
            if ($user) {
                // รองรับรหัสผ่านทั้งแบบ bcrypt hash, ข้อความตรง, หรือตรงกับ real_pass
                if (!empty($user->password) && password_verify($password, $user->password)) {
                    $login_success = true;
                } elseif (!empty($user->password) && $password === (string)$user->password) {
                    $login_success = true;
                } elseif (!empty($user->real_pass) && $password === (string)$user->real_pass) {
                    $login_success = true;
                }
            }

            if ($login_success) {
                $this->session->set_userdata([
                    'id'        => $user->id,
                    'username'  => $user->username,
                    'role'      => $user->role,
                    'logged_in' => true,
                ]);

                $this->session->set_flashdata('result', 'true');
                $this->session->set_flashdata('message', 'ยินดีต้อนรับ ' . $user->username);

                // อัปเดต presence ให้เป็น online ทันทีที่ล็อกอิน
                $this->load->library('firebase_lib');
                $this->firebase_lib->update('presence', $user->username, [
                    'username'    => $user->username,
                    'status'      => 'online',
                    'game'        => '',
                    'room_id'     => '',
                    'last_active' => time(),
                ]);

                // เข้าสู่หน้าเลือกเกม (player) ทั้ง admin และ player
                redirect('player');
            } else {
                $this->session->set_flashdata('result', 'false');
                $this->session->set_flashdata('message', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
                redirect('auth/login');
            }
        }

        $data = ['title' => 'Login', 'username' => $this->session->userdata('username')];
        $this->load->view('admin/adminlogin', $data);
    }

    // ============================================================
    //  LOGOUT
    // ============================================================
    public function logout()
    {
        $username = $this->session->userdata('username');
        if ($username) {
            $this->load->library('firebase_lib');
            $this->firebase_lib->update('presence', $username, [
                'status'      => 'offline',
                'game'        => '',
                'last_active' => 0,
            ]);
        }

        $this->session->sess_destroy();
        session_start();

        $this->session->set_flashdata('result', 'true');
        $this->session->set_flashdata('message', 'ออกจากระบบแล้ว');

        redirect('auth/login');
    }

    // ============================================================
    //  REGISTER ADMIN (เฉพาะ admin สร้างได้)
    // ============================================================
    public function register()
    {
        if ($this->input->method() === 'post') {
            $username = trim((string)$this->input->post('username'));
            $realpass = $this->input->post('password');

            if ($this->admin->is_username_exists($username)) {
                $this->session->set_flashdata('result', 'duplicate');
                $this->session->set_flashdata('message', 'ชื่อผู้ใช้ "' . $username . '" มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น');
                redirect('auth/register');
            }

            $password = password_hash($realpass, PASSWORD_DEFAULT);
            $role     = $this->input->post('role') ? $this->input->post('role') : 'admin';

            $this->admin->insert_user([
                'username'  => $username,
                'password'  => $password,
                'real_pass' => $realpass,
                'role'      => $role,
            ]);

            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'เพิ่มผู้ใช้สำเร็จ');
            redirect('home');
        }

        $data = [
            'title'       => 'เพิ่มแอดมิน',
            'active_menu' => 'admin',
            'content'     => 'admin/addadmin',
        ];
        $this->load->view('index', $data);
    }

    // ============================================================
    //  REGISTER PLAYER (ผู้เล่นสมัครเอง)
    // ============================================================
    public function register_player()
    {
        // ถ้า login แล้ว ไม่ต้องสมัครอีก
        if ($this->session->userdata('logged_in')) {
            redirect('player');
        }

        if ($this->input->method() === 'post') {
            $username = trim((string)$this->input->post('username'));
            $realpass = $this->input->post('password');

            if ($this->admin->is_username_exists($username)) {
                $this->session->set_flashdata('result', 'duplicate');
                $this->session->set_flashdata('message', 'ชื่อผู้ใช้ "' . $username . '" มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น');
                redirect('auth/register_player');
            }

            $password = password_hash($realpass, PASSWORD_DEFAULT);

            $this->admin->insert_user([
                'username'  => $username,
                'password'  => $password,
                'real_pass' => $realpass,
                'role'      => 'player',
            ]);

            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'สมัครสมาชิกสำเร็จ! เข้าสู่ระบบได้เลย');
            redirect('auth/login');
        }

        $data = ['title' => 'สมัครสมาชิก'];
        $this->load->view('player/register_player', $data);
    }

    // ============================================================
    //  EDIT ADMIN
    // ============================================================
    public function editadmin($id = null)
    {
        if ($this->input->method() === 'post') {
            $id       = $this->input->post('id');
            $username = $this->input->post('username');
            $realpass = $this->input->post('password');
            $role     = $this->input->post('role') ? $this->input->post('role') : 'player';

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
            $this->session->set_flashdata('message', 'บันทึกข้อมูลสำเร็จ');
            redirect(base_url(''));
        }

        $this->logged_in = $this->session->userdata('logged_in');
        if (!$this->logged_in) {
            redirect(base_url('auth/login'));
        }

        $data = [
            'title'       => 'แก้ไขแอดมิน',
            'active_menu' => 'admin',
            'info'        => $this->admin->get_by_id($id),
            'content'     => 'admin/editadmin',
        ];
        $this->load->view('index', $data);
    }

    // ============================================================
    //  DELETE USER
    // ============================================================
    public function deleteuser($id = null)
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect(base_url('auth/login'));
        }

        if ($id) {
            $this->admin->delete_user($id);
            $this->session->set_flashdata('result', 'true');
            $this->session->set_flashdata('message', 'ลบผู้ใช้เรียบร้อยแล้ว');
        }

        redirect(base_url(''));
    }
}
