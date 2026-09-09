<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Firebase Configuration
|--------------------------------------------------------------------------
|
| ตั้งค่าเชื่อมต่อ Firebase Realtime Database
| ใช้ Environment Variables เพื่อความปลอดภัย (เหมาะกับ Vercel)
|
| วิธีตั้งค่าบน Vercel:
|   vercel env add FIREBASE_DATABASE_URL
|   vercel env add FIREBASE_API_KEY
|
| วิธีตั้งค่า Local:
|   สร้างไฟล์ .env ที่ root โปรเจค แล้วใส่:
|   FIREBASE_DATABASE_URL=https://your-project.firebaseio.com
|   FIREBASE_API_KEY=your-api-key
|
*/

// ดึงจาก Environment Variable ก่อน ถ้าไม่มีค่อยใช้ค่า default
$config['firebase_database_url'] = getenv('FIREBASE_DATABASE_URL')
    ? getenv('FIREBASE_DATABASE_URL')
    : 'https://boardgame-4gr-default-rtdb.asia-southeast1.firebasedatabase.app';

$config['firebase_api_key'] = getenv('FIREBASE_API_KEY')
    ? getenv('FIREBASE_API_KEY')
    : 'YOUR-API-KEY';

// Path ของไฟล์ Service Account JSON (ถ้ามี - สำหรับ authentication)
$config['firebase_credentials_path'] = APPPATH . 'config/firebase_credentials.json';
