<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Firebase Library สำหรับ CodeIgniter 3
 * ใช้ REST API เชื่อมต่อ Firebase Realtime Database
 * ไม่ต้องลง composer / package เพิ่ม ใช้ cURL ที่มีใน PHP อยู่แล้ว
 */
class Firebase_lib {

    private $CI;
    private $database_url;
    private $api_key;
    private $auth_token = null;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('firebase');

        $this->database_url = rtrim($this->CI->config->item('firebase_database_url'), '/');
        $this->api_key      = $this->CI->config->item('firebase_api_key');

        // พยายามโหลด token จาก service account (ถ้ามี)
        $cred_path = $this->CI->config->item('firebase_credentials_path');
        if ($cred_path && file_exists($cred_path)) {
            $this->auth_token = $this->_get_access_token($cred_path);
        }
    }

    // ========================================================
    //  CRUD Methods
    // ========================================================

    /**
     * ดึงข้อมูลทั้งหมดจาก path
     * @param string $path  เช่น 'user'
     * @return array
     */
    public function get_all($path)
    {
        $response = $this->_request('GET', $path . '.json');
        if (empty($response) || !is_array($response) || isset($response['error'])) return [];

        $result = [];
        foreach ($response as $key => $value) {
            if (is_array($value)) {
                $value['firebase_key'] = $key;
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * ดึงข้อมูลตาม key
     * @param string $path  เช่น 'user'
     * @param string $key   Firebase key
     * @return array|null
     */
    public function get_by_key($path, $key)
    {
        $response = $this->_request('GET', $path . '/' . $key . '.json');
        if (empty($response) || !is_array($response) || isset($response['error'])) return null;

        $response['firebase_key'] = $key;
        return $response;
    }

    /**
     * ค้นหาข้อมูลตาม field
     * ดึงและค้นหาใน PHP โดยตรงเพื่อให้ทำงานได้ 100% โดยไม่ต้องตั้ง .indexOn ใน Firebase Rules
     * @param string $path   เช่น 'user'
     * @param string $field  เช่น 'username'
     * @param string $value  ค่าที่ต้องการค้นหา
     * @return array|null    คืน record แรกที่เจอ หรือ null
     */
    public function find_by($path, $field, $value)
    {
        $response = $this->_request('GET', $path . '.json');

        if (empty($response) || !is_array($response) || isset($response['error'])) {
            return null;
        }

        foreach ($response as $key => $data) {
            if (is_array($data) && isset($data[$field]) && (string)$data[$field] === (string)$value) {
                $data['firebase_key'] = $key;
                return $data;
            }
        }
        return null;
    }

    /**
     * เพิ่มข้อมูลใหม่ (auto-generate key)
     * @param string $path  เช่น 'user'
     * @param array  $data  ข้อมูลที่จะเพิ่ม
     * @return string|false  คืน key ที่สร้างขึ้น หรือ false ถ้าล้มเหลว
     */
    public function insert($path, $data)
    {
        $response = $this->_request('POST', $path . '.json', $data);
        return isset($response['name']) ? $response['name'] : false;
    }

    /**
     * อัปเดตข้อมูลตาม key
     * @param string $path  เช่น 'user'
     * @param string $key   Firebase key
     * @param array  $data  ข้อมูลที่จะอัปเดต
     * @return array
     */
    public function update($path, $key, $data)
    {
        return $this->_request('PATCH', $path . '/' . $key . '.json', $data);
    }

    /**
     * ลบข้อมูลตาม key (ถ้าไม่ระบุ key จะลบทั้ง collection / path)
     * @param string $path  เช่น 'user' หรือ 'lobbies'
     * @param string $key   Firebase key
     * @return bool
     */
    public function delete($path, $key = '')
    {
        $uri = ($key !== null && $key !== '') ? ($path . '/' . $key) : $path;
        $this->_request('DELETE', $uri . '.json');
        return true;
    }

    // ========================================================
    //  Internal: HTTP Request
    // ========================================================

    /**
     * ส่ง HTTP request ไปยัง Firebase REST API
     */
    private function _request($method, $uri, $data = null)
    {
        // ถ้า uri มี ? อยู่แล้ว (query params) ให้ต่อ auth ด้วย &
        if (strpos($uri, '?') !== false) {
            $url = $this->database_url . '/' . $uri;
            if ($this->auth_token) {
                $url .= '&auth=' . $this->auth_token;
            }
        } else {
            $url = $this->database_url . '/' . $uri;
            if ($this->auth_token) {
                $url .= '?auth=' . $this->auth_token;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // ปิด SSL verify ชั่วคราวเพื่อป้องกันปัญหา local cURL CA บน Windows
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $headers = ['Content-Type: application/json'];

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            log_message('error', 'Firebase cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    // ========================================================
    //  Internal: Service Account Token (ถ้าต้องการ)
    // ========================================================

    /**
     * สร้าง access token จาก Service Account JSON
     * ใช้ JWT สร้าง token เองโดยไม่ต้องพึ่ง library ภายนอก
     */
    private function _get_access_token($credentials_path)
    {
        $cred = json_decode(file_get_contents($credentials_path), true);
        if (!$cred) return null;

        $now = time();
        $header = $this->_base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));

        $payload = $this->_base64url_encode(json_encode([
            'iss'   => $cred['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signature_input = $header . '.' . $payload;
        openssl_sign($signature_input, $signature, $cred['private_key'], 'sha256');
        $jwt = $signature_input . '.' . $this->_base64url_encode($signature);

        // แลก JWT เป็น access token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]));
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return isset($response['access_token']) ? $response['access_token'] : null;
    }

    private function _base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
