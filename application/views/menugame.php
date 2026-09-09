<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * หน้ารวบรวมรายการเกมทั้งหมดของระบบ (Menu Game Registry)
 * สามารถเพิ่ม / ลบ / แก้ไข เกมในระบบได้ที่นี่
 */
return [
    'tictactoe' => [
        'name'     => 'OX (Tic Tac Toe)',
        'icon'     => '❌⭕',
        'desc'     => 'เกม XO คลาสสิก 3x3 เรียง 3 ตัวก่อนชนะ',
        'players'  => '2 คน',
        'color'    => '#6366f1',
        'category' => 'strategy',
        'status'   => 'ready',
        'route'    => 'xo',
    ],
    'chess' => [
        'name'     => 'Chess',
        'icon'     => '♟️',
        'desc'     => 'หมากรุกสากล วางแผนยุทธศาสตร์',
        'players'  => '2 คน',
        'color'    => '#059669',
        'category' => 'strategy',
        'status'   => 'coming_soon',
        'route'    => 'chess',	
    ],
    'uno' => [
        'name'     => 'UNO',
        'icon'     => '🃏',
        'desc'     => 'การ์ดเกม UNO สุดมันส์ แกล้งเพื่อนให้สุด',
        'players'  => '2-6 คน',
        'color'    => '#dc2626',
        'category' => 'card',
        'status'   => 'coming_soon',
        'route'    => 'uno',
    ],
    'snake-ladder' => [
        'name'     => 'Snake & Ladder',
        'icon'     => '🐍🪜',
        'desc'     => 'บันไดงู ทอยลูกเต๋าแข่งกันถึงเส้นชัย',
        'players'  => '2-4 คน',
        'color'    => '#d97706',
        'category' => 'casual',
        'status'   => 'coming_soon',
        'route'    => 'snake_ladder',
    ],
    'memory' => [
        'name'     => 'Memory Match',
        'icon'     => '🧠',
        'desc'     => 'จับคู่การ์ด ฝึกความจำ ประลองไหวพริบ',
        'players'  => '1-4 คน',
        'color'    => '#7c3aed',
        'category' => 'puzzle',
        'status'   => 'coming_soon',
        'route'    => 'memory',
    ],
    'quiz' => [
        'name'     => 'Quiz Battle',
        'icon'     => '❓',
        'desc'     => 'ตอบคำถาม แข่งความรู้รอบตัวกับเพื่อน',
        'players'  => '2-8 คน',
        'color'    => '#0891b2',
        'category' => 'casual',
        'status'   => 'coming_soon',
        'route'    => 'quiz',
    ],
    'checkers' => [
        'name'     => 'Checkers',
        'icon'     => '⚫🔴',
        'desc'     => 'หมากฮอส กินสอง กินสาม ชิงความได้เปรียบ',
        'players'  => '2 คน',
        'color'    => '#be185d',
        'category' => 'strategy',
        'status'   => 'coming_soon',
        'route'    => 'checkers',
    ],
    'connect4' => [
        'name'     => 'Connect Four',
        'icon'     => '🔵🟡',
        'desc'     => 'หยอดเหรียญสลับกัน เรียง 4 จุดก่อนชนะ!',
        'players'  => '2 คน',
        'color'    => '#2563eb',
        'category' => 'strategy',
        'status'   => 'coming_soon',
        'route'    => 'connect4',
    ],
];
