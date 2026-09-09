<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dynamic Unified Navbar Component
 * 
 * พารามิเตอร์ที่รองรับ:
 * - $nav_mode   : 'hub' (หน้าเลือกเกม) | 'lobby' (ห้องล็อบบี้) | 'in_game' (ขณะเล่นเกม) (default: 'hub')
 * - $game       : array ข้อมูลเกม (ถ้ามี)
 * - $game_name  : string ชื่อเกม เช่น 'OX (Tic Tac Toe)'
 * - $room_id    : string รหัสห้อง
 * - $leave_url  : string ลิงก์ออกจากห้องเกม (default: base_url('player'))
 */

$nav_mode   = isset($nav_mode) ? $nav_mode : 'hub';
$game_title = isset($game_name) ? $game_name : (isset($game['name']) ? $game['name'] : 'BOARD GAME');
$username   = $this->session->userdata('username');
?>

<style>
    .player-navbar {
        background: rgba(26, 26, 46, 0.88);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.75rem 1.8rem;
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .player-navbar .brand {
        font-size: 1.35rem;
        font-weight: 700;
        background: linear-gradient(135deg, #818cf8, #c084fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s;
    }
    .player-navbar .brand:hover {
        transform: scale(1.02);
    }
    .player-navbar .user-badge {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        padding: 0.38rem 0.85rem;
        border-radius: 20px;
        color: #e0e0e0;
        font-family: inherit;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
        outline: none;
    }
    .player-navbar .user-badge:hover {
        background: rgba(129, 140, 248, 0.22);
        border-color: rgba(129, 140, 248, 0.45);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(129, 140, 248, 0.2);
    }
    .player-navbar .btn-logout {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
        padding: 0.38rem 0.85rem;
        border-radius: 20px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .player-navbar .btn-logout:hover {
        background: rgba(239, 68, 68, 0.3);
        color: #fff;
    }
    .player-navbar .btn-back {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #e0e0e0;
        padding: 0.38rem 0.85rem;
        border-radius: 20px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .player-navbar .btn-back:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
    }
    .player-navbar .btn-leave {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #f87171;
        padding: 0.38rem 0.85rem;
        border-radius: 20px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    .player-navbar .btn-leave:hover {
        background: rgba(239, 68, 68, 0.35);
        color: #fff;
    }
    .player-navbar .widget-btn {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #e0e0e0;
        padding: 0.38rem 0.85rem;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        position: relative;
    }
    .player-navbar .widget-btn:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }
    .player-navbar .online-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 6px #10b981;
    }
    .player-navbar .widget-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #ef4444;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        border-radius: 10px;
        padding: 1px 6px;
        min-width: 16px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .player-navbar .room-badge {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(129, 140, 248, 0.3);
        color: #c7d2fe;
        border-radius: 12px;
        padding: 0.35rem 0.8rem;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .player-navbar .btn-admin-panel {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        padding: 0.38rem 0.95rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.35);
        transition: all 0.2s;
    }
    .player-navbar .btn-admin-panel:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 0 18px rgba(245, 158, 11, 0.5);
    }
	.player-navbar .brand .credit,
	.credit {
        font-size: 10px;
        font-weight: 500;
        background: transparent !important;
        -webkit-background-clip: unset !important;
        -webkit-text-fill-color: #c084fc !important;
        color: #c084fc !important;
        align-self: flex-start;
        margin-top: 1px;
        margin-left: 5px;
        opacity: 0.85;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
</style>

<nav class="player-navbar d-flex justify-content-between align-items-center">

    <!-- ฝั่งซ้าย: โลโก้ & ปุ่มนำทางตามสถานะ -->
    <div class="d-flex align-items-center gap-3">
        <?php if ($nav_mode === 'in_game'): ?>
            <a class="brand" href="<?= base_url('player') ?>">
                <i class="fas fa-gamepad"></i><?= htmlspecialchars($game_title) ?>
                <sup class="credit">by 4GR;waveza4G</sup>
            </a>
            <a href="<?= isset($leave_url) ? $leave_url : base_url('player') ?>" class="btn-leave" onclick="return confirm('คุณแน่ใจว่าต้องการออกจากเกมหรือไม่?')">
                <i class="fas fa-arrow-left me-1"></i>ออกจากห้อง
            </a>
            <?php if (!empty($room_id)): ?>
                <span class="room-badge d-none d-sm-inline-flex">
                    <i class="fas fa-door-open text-primary"></i>ห้อง: <strong><?= htmlspecialchars($room_id) ?></strong>
                </span>
            <?php endif; ?>

        <?php elseif ($nav_mode === 'lobby'): ?>
            <a class="brand" href="<?= !empty($room_id) ? base_url('player/leave_lobby/' . $room_id) : base_url('player') ?>">
                <i class="fas fa-gamepad"></i>BOARD GAME
				<sup class="credit">by 4GR;waveza4G</sup>
            </a>
            <a href="<?= !empty($room_id) ? base_url('player/leave_lobby/' . $room_id) : base_url('player') ?>" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i>กลับเลือกเกม
            </a>
            <span class="room-badge d-none d-md-inline-flex">
                <i class="fas fa-hourglass-half text-warning"></i>ล็อบบี้: <strong><?= htmlspecialchars($game_title) ?></strong>
            </span>

        <?php else: /* 'hub' โหมดหน้าเลือกเกมปกติ */ ?>
            <a class="brand" href="<?= base_url('player') ?>">
                <i class="fas fa-gamepad"></i>BOARD GAME
				<sup class="credit">by 4GR;waveza4G</sup>
            </a>
        <?php endif; ?>
    </div>

    <!-- ฝั่งขวา: วิดเจ็ตออนไลน์, คำเชิญ, ข้อความแชท, ข้อมูลผู้ใช้, ปุ่มแอดมิน -->
    <div class="d-flex align-items-center gap-2">
        <!-- ปุ่มพิเศษสำหรับ Admin: ไปหน้าจัดการข้อมูลระบบ -->
        <?php if ($this->session->userdata('role') === 'admin'): ?>
            <a href="<?= base_url('home') ?>" class="btn-admin-panel" title="ไปหน้าจัดการข้อมูลระบบ">
                <i class="fas fa-screwdriver-wrench me-1"></i>จัดการข้อมูล
            </a>
        <?php endif; ?>

        <!-- ปุ่มเปิด Modal เพื่อนออนไลน์ & แสดงจำนวนแชทที่ยังไม่ได้อ่าน -->
        <button class="widget-btn" data-bs-toggle="modal" data-bs-target="#onlineFriendsModal" title="ดูเพื่อนออนไลน์และข้อความแชท">
            <span class="online-dot"></span>
            <span>ออนไลน์ <strong id="navbar-online-count" class="text-success">0</strong></span>
            <span class="widget-badge" id="navbar-chat-badge" style="display:none; background:#ef4444;" title="ข้อความแชทใหม่">0</span>
        </button>

        <!-- ปุ่มเปิด Modal คำเชิญเล่นเกม -->
        <button class="widget-btn position-relative" data-bs-toggle="modal" data-bs-target="#invitationsModal" title="กล่องคำเชิญเล่นเกม">
            <i class="fas fa-bell text-warning"></i>
            <span class="widget-badge" id="navbar-invite-badge" style="display:none;">0</span>
        </button>

        <!-- ข้อมูลผู้ใช้ปัจจุบัน (คลิกเพื่อแก้ไขชื่อผู้ใช้และรหัสผ่าน) -->
        <div class="d-flex align-items-center gap-2" style="justify-content: flex-end;">
            <button type="button" class="user-badge" id="navbar-user-profile-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal" title="คลิกเพื่อแก้ไขชื่อผู้ใช้และรหัสผ่าน" onclick="openEditProfileModal(event)">
                <?php if ($this->session->userdata('role') === 'admin'): ?>
                    <span class="badge bg-warning text-dark me-1" style="font-size:10px;"><i class="fas fa-crown me-1"></i>ADMIN</span>
                <?php else: ?>
                    <i class="fa-solid fa-circle-user me-1"></i>
                <?php endif; ?>
                <span id="navbar-username-text"><?= htmlspecialchars($username) ?></span>
                <i class="fas fa-pen-to-square ms-2 opacity-50" style="font-size:11px;" title="แก้ไขชื่อผู้ใช้ / รหัสผ่าน"></i>
            </button>
        </div>

        <!-- ปุ่มออกจากระบบ (สำหรับโหมด Hub และ Lobby) -->
        <?php if ($nav_mode !== 'in_game'): ?>
            <a href="<?= base_url('auth/logout') ?>" class="btn-logout" title="ออกจากระบบ">
                <i class="fas fa-sign-out-alt me-1"></i>ออก
            </a>
        <?php endif; ?>
    </div>
</nav>
