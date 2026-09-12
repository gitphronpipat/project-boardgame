<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Minesweeper Anime Re:Zero Theme View
 * ตำแหน่ง: application/views/games/minesweeper/thameanimerezero.php
 * 
 * ธีมอนิเมะ Re:Zero - Starting Life in Another World:
 * - หน้าตา Rem / Emilia และ Reaction Face 5 ระดับ
 * - ตอนแพ้: ข้อความ "I LOVE YOU 🖤" สั่นเบาๆ + ข้อความหลอนสุ่มทั่วจอวนซ้ำตลอดจนกว่าจะกดเริ่มใหม่หรือกลับหน้าหลัก
 * - เสียงวนตอนแพ้: i_love_you_satella.mp3 เปิดวนซ้ำเรื่อยๆ จนกว่าจะกดเริ่มใหม่หรือกลับหน้าหลัก
 * - Popup แพ้: รูป rezero.png ตรงกลาง + ปุ่ม "⏳ Re:zero" (เริ่มใหม่) และ "💀 ยอมแพ้" (กลับหน้าหลัก)
 * - Popup ชนะ: ข้อความ "VICTORY" ตรงกลาง (รองรับใส่รูปในอนาคต) + ปุ่ม "✨ Re:zero" และ "🏰 กลับคฤหาสน์"
 * - รองรับระบบมือถือ/ไอแพด (แตะขุด / แตะค้างปักธง) และโหมดหลายคนครบถ้วน
 */

$room_id   = isset($room_id) ? $room_id : '';
$username  = isset($username) ? $username : '';
$room_data = isset($room) ? $room : [];
$host      = isset($room_data['host']) ? $room_data['host'] : $username;
$is_host   = ($host === $username);
$is_admin  = !empty($is_admin) || ($this->session->userdata('role') === 'admin');

// โฟลเดอร์สำหรับใส่รูปภาพ Assets ของเกม (เรียกผ่าน Controller เพื่อให้เบราว์เซอร์เข้าถึงได้ ไม่โดนบล็อก 403 Forbidden จากโฟลเดอร์ application/)
$image_base_url   = base_url('minesweeper/image/');
$rezero_image_url = base_url('minesweeper/image/');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Minesweeper: Re:Zero Theme' ?></title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;700;800&family=Cinzel:wght@600;800;900&family=Prompt:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-dark: #07030c;
            --panel-bg: rgba(22, 10, 35, 0.88);
            --border-glow: rgba(168, 85, 247, 0.35);
            --witch-purple: #a855f7;
            --witch-dark: #3b0764;
            --witch-red: #e11d48;
            --cell-hidden: #1e112a;
            --cell-hidden-hover: #341a4a;
            --cell-revealed: #0c0514;
            --led-red: #f43f5e;
            --led-bg: #1a030a;
            --accent-gold: #f59e0b;
        }

        /* Navbar Layout Fix */
        .player-navbar {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            padding: 0.65rem 1.4rem !important;
            width: 100% !important;
            background: rgba(15, 6, 25, 0.92) !important;
            border-bottom: 1px solid rgba(168, 85, 247, 0.25) !important;
        }
        .player-navbar > div {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
        }
        .player-navbar a {
            text-decoration: none !important;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: radial-gradient(circle at 50% 15%, #2e0854 0%, #08030f 75%, #000000 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            user-select: none;
            position: relative;
        }

        /* Satella's Witch Mist Overlay Background */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(147, 51, 234, 0.14) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(225, 29, 72, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .ms-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            gap: 1.1rem;
            position: relative;
            z-index: 1;
        }

        /* Top HUD Bar */
        .ms-top-bar {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.8rem;
            background: var(--panel-bg);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(168, 85, 247, 0.25);
            border-radius: 16px;
            padding: 0.85rem 1.4rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), 0 0 20px rgba(168, 85, 247, 0.15);
        }

        .ms-room-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .badge-theme-rezero {
            background: linear-gradient(135deg, #7e22ce, #be185d);
            padding: 0.35rem 0.85rem;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 0 12px rgba(190, 24, 93, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-diff {
            background: rgba(30, 15, 45, 0.8);
            border: 1px solid rgba(168, 85, 247, 0.3);
            color: #e9d5ff;
            padding: 0.35rem 0.85rem;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ms-controls {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-ms {
            background: rgba(40, 18, 65, 0.8);
            border: 1px solid rgba(168, 85, 247, 0.4);
            color: #f3e8ff;
            padding: 0.42rem 0.95rem;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-ms:hover {
            background: rgba(126, 34, 206, 0.4);
            border-color: #c084fc;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(168, 85, 247, 0.3);
        }

        /* Players Strip */
        .ms-players-strip {
            width: 100%;
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .player-card {
            flex: 1;
            min-width: 150px;
            background: rgba(25, 12, 40, 0.75);
            border: 1px solid rgba(168, 85, 247, 0.15);
            border-radius: 12px;
            padding: 0.65rem 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.25s ease;
        }

        .player-card.active-turn {
            border-color: #a855f7;
            background: rgba(126, 34, 206, 0.25);
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.4);
            transform: scale(1.02);
        }

        .player-card.knockout {
            opacity: 0.4;
            filter: grayscale(1);
        }

        .player-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .player-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7e22ce, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            box-shadow: 0 0 8px rgba(168, 85, 247, 0.4);
        }

        .player-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .player-score {
            font-family: 'Share Tech Mono', monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: #c084fc;
        }

        /* Console Frame */
        .ms-frame {
            background: rgba(18, 8, 30, 0.95);
            border: 2px solid rgba(168, 85, 247, 0.4);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8), 0 0 35px rgba(126, 34, 206, 0.25);
            border-radius: 20px;
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            max-width: 100%;
        }

        /* Retro LCD Header */
        .ms-header-panel {
            width: 100%;
            background: #090312;
            border: 2px solid #2d1245;
            border-radius: 12px;
            padding: 0.8rem 1.4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.8);
        }

        .led-display {
            font-family: 'Share Tech Mono', monospace;
            font-size: 2.1rem;
            background: var(--led-bg);
            color: var(--led-red);
            padding: 0.2rem 0.65rem;
            border-radius: 6px;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(244, 63, 94, 0.8);
            border: 1px solid rgba(244, 63, 94, 0.3);
            min-width: 80px;
            text-align: center;
        }

        /* Face Button Re:Zero Rem/Emilia */
        .face-btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .face-btn {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3b0764, #1e082b);
            border: 2px solid #a855f7;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6), 0 0 16px rgba(168, 85, 247, 0.5);
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            padding: 4px;
            position: relative;
            overflow: hidden;
        }

        .face-btn:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 20px rgba(168, 85, 247, 0.7);
        }

        .face-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .face-progress-label {
            font-size: 0.72rem;
            color: #c084fc;
            font-weight: 600;
        }

        /* Board Scroll */
        .ms-board-scroll {
            max-width: 100%;
            overflow: auto;
            border-radius: 12px;
            background: #090310;
            border: 2px solid #2d1245;
            padding: 8px;
            box-shadow: inset 0 3px 12px rgba(0, 0, 0, 0.9);
        }

        /* Mine Grid */
        .ms-grid {
            display: grid;
            gap: 3px;
            margin: 0 auto;
        }

        .ms-cell {
            width: 36px;
            height: 36px;
            background: var(--cell-hidden);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        .ms-cell:hover:not(.revealed) {
            background: var(--cell-hidden-hover);
            border-color: rgba(192, 132, 252, 0.6);
            transform: scale(0.96);
        }

        .ms-cell.revealed {
            background: var(--cell-revealed);
            border-color: #1a082b;
            box-shadow: inset 1px 1px 4px rgba(0, 0, 0, 0.8);
            cursor: default;
        }

        .ms-cell.exploded {
            background: #e11d48 !important;
            animation: pulse-explode 0.4s ease;
        }

        @keyframes pulse-explode {
            0% { transform: scale(1); }
            50% { transform: scale(1.25); background: #ff4d6d; }
            100% { transform: scale(1); }
        }

        /* Number Colors */
        .num-1 { color: #60a5fa; text-shadow: 0 0 6px rgba(96, 165, 250, 0.5); }
        .num-2 { color: #34d399; text-shadow: 0 0 6px rgba(52, 211, 153, 0.5); }
        .num-3 { color: #f87171; text-shadow: 0 0 6px rgba(248, 113, 113, 0.5); }
        .num-4 { color: #c084fc; text-shadow: 0 0 6px rgba(192, 132, 252, 0.5); }
        .num-5 { color: #fbbf24; }
        .num-6 { color: #38bdf8; }
        .num-7 { color: #f43f5e; }
        .num-8 { color: #a855f7; }

        .cell-img {
            width: 24px;
            height: 24px;
            object-fit: contain;
            pointer-events: none;
        }

        /* Controls Hint Bar */
        .device-controls-hint {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 1rem;
            background: rgba(22, 10, 35, 0.8);
            border: 1px solid rgba(168, 85, 247, 0.25);
            padding: 0.5rem 1.1rem;
            border-radius: 30px;
            font-size: 0.82rem;
            color: #d8b4fe;
        }

        /* Live Activity Log */
        .ms-log-container {
            width: 100%;
            background: var(--panel-bg);
            border: 1px solid rgba(168, 85, 247, 0.2);
            border-radius: 14px;
            padding: 0.85rem 1.2rem;
            max-height: 130px;
            overflow-y: auto;
            font-size: 0.82rem;
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .log-item {
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1.4;
        }

        /* ========================================================
           🖤 RE:ZERO DEATH / SATELLA SHIVER & CREEPY OVERLAY
           ======================================================== */
        @keyframes rezero-shiver {
            0% { transform: translate(0, 0) rotate(0deg); }
            20% { transform: translate(-1.5px, 1px) rotate(-0.5deg); }
            40% { transform: translate(1px, -1px) rotate(0.5deg); }
            60% { transform: translate(-1px, -1px) rotate(0deg); }
            80% { transform: translate(1.5px, 1px) rotate(0.5deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }

        .rezero-shiver-title {
            font-family: 'Cinzel', serif;
            font-weight: 900;
            font-size: 2.1rem;
            color: #f43f5e;
            text-shadow: 0 0 15px rgba(244, 63, 94, 0.8), 0 0 30px rgba(126, 34, 206, 0.8);
            animation: rezero-shiver 0.12s infinite ease-in-out;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .rezero-victory-title {
            font-family: 'Cinzel', serif;
            font-weight: 900;
            font-size: 2.2rem;
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(245, 158, 11, 0.6);
            letter-spacing: 3px;
            margin-bottom: 8px;
        }

        /* Fullscreen Creepy Overlay Container (Spawns random text continuously) */
        #rezeroCreepyOverlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            z-index: 99999;
            overflow: hidden;
            display: none;
        }

        .creepy-text-item {
            position: absolute;
            font-family: 'Cinzel', 'Prompt', serif;
            font-weight: 800;
            pointer-events: none;
            opacity: 0;
            animation: eerie-float-fade 2.8s ease-in-out forwards, rezero-shiver 0.1s infinite;
            white-space: nowrap;
        }

        @keyframes eerie-float-fade {
            0% { opacity: 0; transform: scale(0.6) translateY(20px); filter: blur(4px); }
            20% { opacity: 0.95; transform: scale(1.1) translateY(0); filter: blur(0); }
            80% { opacity: 0.85; transform: scale(1) translateY(-15px); }
            100% { opacity: 0; transform: scale(1.2) translateY(-30px); filter: blur(6px); }
        }

        /* Center Slot in Popup */
        .rezero-popup-slot {
            margin: 12px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .rezero-popup-img {
            max-width: 140px;
            max-height: 140px;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.5);
            border: 2px solid rgba(168, 85, 247, 0.6);
            object-fit: cover;
        }

        .rezero-popup-text {
            font-size: 0.95rem;
            color: #e9d5ff;
            line-height: 1.4;
            max-width: 380px;
        }

        /* Responsive Icons & Emojis styling */
        .rezero-popup-slot i {
            font-size: 4.5rem;
            color: #c084fc;
            filter: drop-shadow(0 0 16px rgba(168, 85, 247, 0.8));
        }

        .rezero-popup-slot .asset-emoji {
            font-size: 4.5rem;
            line-height: 1;
            filter: drop-shadow(0 0 16px rgba(168, 85, 247, 0.8));
        }

        .face-btn i {
            font-size: 1.85rem;
            color: #c084fc;
        }

        .face-btn .asset-emoji {
            font-size: 1.85rem;
            line-height: 1;
        }

        .ms-cell .cell-img {
            width: 22px;
            height: 22px;
            object-fit: contain;
            pointer-events: none;
        }

        .ms-cell i {
            font-size: 1.15rem;
            line-height: 1;
            pointer-events: none;
        }

        .ms-cell .asset-emoji {
            font-size: 1.15rem;
            line-height: 1;
            pointer-events: none;
        }

        /* Admin Cheat Button & Overlay */
        .btn-admin-cheat {
            background: linear-gradient(135deg, rgba(225, 29, 72, 0.25), rgba(168, 85, 247, 0.25));
            border: 1px solid #e11d48 !important;
            color: #fca5a5 !important;
            box-shadow: 0 0 12px rgba(225, 29, 72, 0.35);
            transition: all 0.25s ease;
        }
        .btn-admin-cheat:hover {
            background: linear-gradient(135deg, rgba(225, 29, 72, 0.45), rgba(168, 85, 247, 0.5));
            border-color: #f43f5e !important;
            color: #fff !important;
            box-shadow: 0 0 18px rgba(244, 63, 94, 0.7);
            transform: translateY(-1px);
        }
        .btn-admin-cheat.active {
            background: linear-gradient(135deg, #e11d48, #9333ea) !important;
            border-color: #fda4af !important;
            color: #fff !important;
            font-weight: 700;
            box-shadow: 0 0 22px rgba(225, 29, 72, 0.95);
            animation: cheat-pulse 1.2s infinite alternate;
        }
        @keyframes cheat-pulse {
            from { box-shadow: 0 0 10px rgba(225, 29, 72, 0.6); }
            to { box-shadow: 0 0 24px rgba(244, 63, 94, 1); }
        }

        /* Admin Hint Overlay on Cells */
        .ms-cell.admin-revealed-mine {
            background: rgba(225, 29, 72, 0.3) !important;
            border: 1.5px dashed #f43f5e !important;
            box-shadow: inset 0 0 10px rgba(244, 63, 94, 0.75) !important;
        }
        .ms-cell.admin-revealed-safe {
            background: rgba(168, 85, 247, 0.12) !important;
            border: 1px dashed rgba(168, 85, 247, 0.45) !important;
        }
        .admin-hint-text {
            opacity: 0.85;
            font-size: 0.88rem;
            font-weight: 800;
            pointer-events: none;
        }
        .admin-hint-mine {
            opacity: 0.95;
            font-size: 1.05rem;
            filter: drop-shadow(0 0 6px #f43f5e);
            pointer-events: none;
            animation: rezero-shiver 0.25s infinite;
        }

        /* Popup Buttons Icon & Emoji slot */
        .swal-btn-asset {
            width: 22px;
            height: 22px;
            vertical-align: middle;
            margin-right: 6px;
            object-fit: contain;
            display: inline-block;
            pointer-events: none;
        }
    </style>
</head>
<body>

<!-- Audio Loop ตอนแพ้ (ไฟล์ i_love_you_satella.mp3 วนซ้ำเรื่อยๆ จนกว่าจะเริ่มใหม่หรือกลับหน้าหลัก) -->
<audio id="rezeroWitchAudio" loop preload="auto">
    <source src="<?= base_url('minesweeper/sound/i_love_you_satella') ?>" type="audio/mpeg">
    <source src="<?= $rezero_image_url ?>i_love_you_satella.mp3" type="audio/mpeg">
</audio>

<!-- Audio ตอนเริ่มเกม (ไฟล์ ha.mp3 ดังขึ้นมาตอนเริ่มเกม) -->
<audio id="rezeroStartAudio" preload="auto">
    <source src="<?= base_url('minesweeper/sound/ha') ?>" type="audio/mpeg">
    <source src="<?= $rezero_image_url ?>ha.mp3" type="audio/mpeg">
</audio>

<!-- Audio ตอนชนะ (ไฟล์ subaru_victory.mp3) -->
<audio id="rezeroVictoryAudio" preload="auto">
    <source src="<?= base_url('minesweeper/sound/subaru_victory') ?>" type="audio/mpeg">
    <source src="<?= $rezero_image_url ?>subaru_victory.mp3" type="audio/mpeg">
</audio>

<!-- Full Screen Creepy Random Text Overlay (รันต่อเนื่องจนกว่าจะกดเริ่มใหม่หรือกลับหน้าหลัก) -->
<div id="rezeroCreepyOverlay"></div>

<!-- Unified Dynamic Navbar -->
<?php $this->load->view('player/components/navbar', [
    'nav_mode'   => 'in_game',
    'game_name'  => 'Minesweeper (Re:Zero ธีม)',
    'room_id'    => $room_id,
    'leave_url'  => base_url('minesweeper/leave/' . $room_id),
    'user_stats' => isset($user_stats) ? $user_stats : null,
]); ?>

<div class="ms-wrapper">

    <!-- Top HUD Bar -->
    <div class="ms-top-bar">
        <div class="ms-room-info">
            <span class="badge-theme-rezero">
                <i class="fas fa-gem"></i> ธีมอนิเมะ: <strong>Re:Zero</strong>
            </span>
            <span class="badge-diff" id="diffBadge">
                <i class="fas fa-layer-group text-warning me-1"></i> ระดับ: <strong id="diffText">ง่าย</strong>
            </span>
        </div>

        <div class="ms-controls">
            <?php if (!empty($is_admin)): ?>
            <button class="btn-ms btn-admin-cheat" id="adminCheatBtn" onclick="toggleAdminCheatMode()" title="โหมดแอดมิน: เปิด/ปิดดูเฉลยกระดาน">
                <i class="fas fa-eye text-danger me-1"></i> <span id="adminCheatText">เฉลย (Admin)</span>
            </button>
            <?php endif; ?>
            <?php if ($is_host): ?>
            <button class="btn-ms" onclick="openHostSettings()">
                <i class="fas fa-sliders-h text-warning me-1"></i> เปลี่ยนระดับความยาก
            </button>
            <?php endif; ?>
            <button class="btn-ms" onclick="triggerRematch()">
                <i class="fas fa-redo text-info"></i> เริ่มเกมใหม่
            </button>
        </div>
    </div>

    <!-- Players Strip -->
    <div class="ms-players-strip" id="playersStrip"></div>

    <!-- Main Console Frame -->
    <div class="ms-frame">

        <!-- Header Panel with Rem Face & LED -->
        <div class="ms-header-panel">
            <div title="จำนวนธงคงเหลือ" class="led-display" id="minesLed">010</div>

            <div class="face-btn-container">
                <button class="face-btn" id="faceBtn" onclick="triggerRematch()" title="คลิกเพื่อเริ่มเกมใหม่ (Re:Zero)">
                    <!-- Default Rem Character Face -->
                    <img id="faceImg" src="https://cdn3.emoji.gg/emojis/7156-remwink.png" alt="Rem">
                </button>
                <span class="face-progress-label" id="progressLabel">เปิดแล้ว 0%</span>
            </div>

            <div title="เวลาเล่น (วินาที)" class="led-display" id="timerLed">000</div>
        </div>

        <!-- Board Grid Container -->
        <div class="ms-board-scroll">
            <div class="ms-grid" id="mineGrid"></div>
        </div>

        <!-- Device Controls Hint Bar -->
        <div class="device-controls-hint">
            <span class="hint-badge text-white">
                <i class="fas fa-mobile-alt text-info me-1"></i> โทรศัพท์ / ไอแพด: <strong>แตะ 1 ครั้งเปิดช่อง</strong> | <strong>แตะค้างไว้เพื่อปักธง 🚩</strong>
            </span>
            <span class="hint-badge d-none d-md-inline ms-2 text-muted">
                <i class="fas fa-mouse text-warning me-1"></i> คอมพิวเตอร์: คลิกซ้ายเปิด | คลิกขวาปักธง 🚩
            </span>
        </div>

    </div>

    <!-- Live Event Log -->
    <div class="ms-log-container" id="logContainer">
        <div class="log-item"><i class="fas fa-info-circle text-info"></i> เชื่อมต่อห้องเกม Minesweeper (ธีม Re:Zero) เรียบร้อย...</div>
    </div>

</div>

<!-- Host Settings Modal Template -->
<div id="hostModal" style="display: none;">
    <div style="text-align: left; font-size: 0.95rem;">
        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #cbd5e1;">
            <i class="fas fa-layer-group text-warning me-1"></i> เลือกระดับความยากที่ต้องการ:
        </label>
        <select id="modalDiffSelect" class="swal2-input" style="margin: 0; width: 100%; font-size: 0.95rem;">
            <?php foreach ($difficulty_settings as $key => $conf): ?>
                <option value="<?= $key ?>">
                    <?= htmlspecialchars($conf['name']) ?> (<?= $conf['rows'] ?>x<?= $conf['cols'] ?> ช่อง, ระเบิด <?= $conf['mines'] ?> ลูก)
                </option>
            <?php endforeach; ?>
        </select>
        <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 10px;">
            <i class="fas fa-info-circle text-info me-1"></i> หากเล่นกับเพื่อน ผู้เล่นทุกคนในห้องจะต้องกดยอมรับก่อนเริ่มกระดานใหม่
        </div>
    </div>
</div>

<script>
/**
 * -------------------------------------------------------------
 * 🎨 RE:ZERO ASSETS & SLOTS
 * -------------------------------------------------------------
 */
const REZERO_ASSETS = {
    // หน้าของ Rem / Subaru / ตัวละคร ตามระดับความคืบหน้า
    faces: {
        stage1: { icon: '', local: '<?= $rezero_image_url ?>rem_stage1.png', url: 'https://cdn3.emoji.gg/emojis/3075-subarucry.png' },
        stage2: { icon: '', local: '<?= $rezero_image_url ?>rem_stage2.png', url: 'https://cdn3.emoji.gg/emojis/5582-subaruheheh.png' },
        stage3: { icon: '', local: '<?= $rezero_image_url ?>image.png', url: '' },
        win:    { icon: '', local: '<?= $rezero_image_url ?>rem_win.png',    url: 'https://cdn3.emoji.gg/emojis/4295-subarucrazy.png' },
        lose:   { icon: '', local: '<?= $rezero_image_url ?>rem_lose.png',   url: 'https://cdn3.emoji.gg/emojis/355506-satella.png' },
    },
    // รูป / ไอคอน ตรงกลางใน Popup แพ้ และ ชนะ
    popup: {
        defeat:  { icon: '', local: '<?= $rezero_image_url ?>satella.jpeg', url: 'https://cdn3.emoji.gg/emojis/355506-satella.png' },
        victory: { icon: '', local: '<?= $rezero_image_url ?>rezero.png',    url: 'https://cdn3.emoji.gg/emojis/3987-subaruthumbsup.png' }
    },
    // ปุ่มกดใน Popup (แพ้ / ชนะ) สามารถใส่ icon/emoji, รูป local ในเครื่อง, หรือ url https ได้เหมือนกัน
    buttons: {
        defeat_retry:  { icon: '', local: '<?= $rezero_image_url ?>recollect.png', url: 'https://cdn3.emoji.gg/emojis/3075-subarucry.png' },
        defeat_giveup: { icon: '', local: '<?= $rezero_image_url ?>remxsubaru.jpg', url: 'https://cdn3.emoji.gg/emojis/7156-remwink.png' },
        victory_retry: { icon: '', local: '', url: 'https://cdn3.emoji.gg/emojis/4295-subarucrazy.png' },
        victory_leave: { icon: '', local: '', url: 'https://cdn3.emoji.gg/emojis/9809-emilianya.png' }
    },
    // ธงปักช่องระเบิด
    flag: { icon: '', local: '<?= $image_base_url ?>flag.png', url: 'https://cdn3.emoji.gg/emojis/9809-emilianya.png' },
    // ระเบิด
    mine: { icon: '', local: '<?= $image_base_url ?>mine.png', url: 'https://cdn3.emoji.gg/emojis/355506-satella.png' }
};

// Aliases เพื่อความเข้ากันได้
REZERO_ASSETS.popup.defeatImage  = REZERO_ASSETS.popup.defeat;
REZERO_ASSETS.popup.victoryImage = REZERO_ASSETS.popup.victory;

const REZERO_FALLBACK_FACES = {
    stage1: 'https://cdn3.emoji.gg/emojis/3075-subarucry.png',
    stage2: 'https://cdn3.emoji.gg/emojis/5582-subaruheheh.png',
    stage3: 'https://cdn3.emoji.gg/emojis/3987-subaruthumbsup.png',
    win:    'https://cdn3.emoji.gg/emojis/4295-subarucrazy.png',
    lose:   'https://cdn3.emoji.gg/emojis/355506-satella.png'
};

/**
 * ฟังก์ชันจัดการ Fallback เมื่อรูปภาพโหลดไม่สำเร็จ (ปลอดภัย 100% ไม่มีปัญหา quote หลุด)
 */
function handleAssetError(img, fallbackUrl, fallbackEmoji) {
    if (!img) return;
    img.onerror = null;

    if (fallbackUrl && fallbackUrl !== img.src) {
        img.src = fallbackUrl;
        img.onerror = function() {
            img.onerror = null;
            applyEmojiFallback(img, fallbackEmoji);
        };
        return;
    }

    applyEmojiFallback(img, fallbackEmoji);
}

function applyEmojiFallback(img, fallbackEmoji) {
    if (!img) return;
    if (!fallbackEmoji) {
        img.style.display = 'none';
        return;
    }
    const val = String(fallbackEmoji).trim();
    if (val.startsWith('http://') || val.startsWith('https://')) {
        img.src = val;
        img.onerror = function() {
            img.onerror = null;
            img.style.display = 'none';
        };
    } else {
        const span = document.createElement('span');
        span.className = 'asset-emoji ' + (img.className || '');
        span.textContent = val;
        if (img.parentNode) {
            img.parentNode.replaceChild(span, img);
        }
    }
}

/**
 * ฟังก์ชันดึง HTML สำหรับแสดงผล Asset ตามลำดับความสำคัญ (Hierarchy):
 *  1. icon  -> ถ้ามีค่า แสดง icon/emoji ก่อนเสมอ (ใหญ่สุด ยึดเป็นหลัก)
 *  2. local -> ถ้าไม่มี icon ให้ใช้ภาพในเครื่อง ถ้าภาพเสียหรือ 403 ให้กระโดดไปหา url
 *  3. url   -> ลิงก์ https จากเว็บ เป็นตัวสำรองลำดับท้ายสุด
 */
function getAssetHtml(slot, className = '', defaultEmoji = '') {
    const renderEmojiOrImg = (val) => {
        if (!val) return '';
        const str = String(val).trim();
        if (str.startsWith('http://') || str.startsWith('https://')) {
            return `<img src="${str}" class="${className}" alt="asset" onerror="handleAssetError(this, '', '😐')">`;
        }
        return `<span class="asset-emoji ${className}">${str}</span>`;
    };

    if (!slot) return renderEmojiOrImg(defaultEmoji);

    let cfg = slot;
    if (typeof slot === 'string') {
        const str = slot.trim();
        if (str.startsWith('fa-') || str.includes('fa-')) {
            cfg = { icon: str, local: '', url: '' };
        } else if (str.startsWith('http://') || str.startsWith('https://')) {
            cfg = { icon: '', local: '', url: str };
        } else if (str.match(/\.(png|jpe?g|webp|gif|svg)$/i)) {
            cfg = { icon: '', local: str, url: '' };
        } else {
            cfg = { icon: str, local: '', url: '' };
        }
    }

    const iconVal  = (cfg.icon || '').trim();
    const localVal = (cfg.local || '').trim();
    const urlVal   = (cfg.url || '').trim();

    // ลำดับที่ 1 (สำคัญสุด ยึดเป็นหลัก): icon
    if (iconVal !== '') {
        if (iconVal.includes('fa-')) {
            return `<i class="${iconVal} ${className}"></i>`;
        }
        return `<span class="asset-emoji ${className}">${iconVal}</span>`;
    }

    // ลำดับที่ 2 (รองลงมา): ภาพในเครื่อง local พร้อม fallback ไป url หรือ defaultEmoji
    if (localVal !== '') {
        const safeUrl = (urlVal || '').replace(/'/g, "\\'");
        const safeEmoji = (defaultEmoji || '').replace(/'/g, "\\'");
        return `<img src="${localVal}" class="${className}" alt="asset" onerror="handleAssetError(this, '${safeUrl}', '${safeEmoji}')">`;
    }

    // ลำดับที่ 3 (สำคัญท้ายสุด): url (https)
    if (urlVal !== '') {
        const safeEmoji = (defaultEmoji || '').replace(/'/g, "\\'");
        return `<img src="${urlVal}" class="${className}" alt="asset" onerror="handleAssetError(this, '', '${safeEmoji}')">`;
    }

    return renderEmojiOrImg(defaultEmoji);
}

// Preload Images อัตโนมัติให้เข้า Memory
(function preloadAllAssets() {
    function tryPreload(src) {
        if (!src || typeof src !== 'string') return;
        const s = src.trim();
        if (s.startsWith('http://') || s.startsWith('https://') || s.match(/\.(png|jpe?g|webp|gif|svg)$/i)) {
            const img = new Image();
            img.src = s;
        }
    }
    if (REZERO_ASSETS.faces) {
        Object.values(REZERO_ASSETS.faces).forEach(s => {
            if (typeof s === 'object') { tryPreload(s.local); tryPreload(s.url); } else tryPreload(s);
        });
    }
    if (REZERO_ASSETS.popup) {
        Object.values(REZERO_ASSETS.popup).forEach(s => {
            if (typeof s === 'object') { tryPreload(s.local); tryPreload(s.url); } else tryPreload(s);
        });
    }
    if (REZERO_ASSETS.buttons) {
        Object.values(REZERO_ASSETS.buttons).forEach(s => {
            if (typeof s === 'object') { tryPreload(s.local); tryPreload(s.url); } else tryPreload(s);
        });
    }
    [REZERO_ASSETS.flag, REZERO_ASSETS.mine].forEach(s => {
        if (typeof s === 'object') { tryPreload(s.local); tryPreload(s.url); } else tryPreload(s);
    });
})();

// SFX Synthesizer
const AudioContext = window.AudioContext || window.webkitAudioContext;
let audioCtx = null;
function playSfx(type) {
    try {
        if (!audioCtx) audioCtx = new AudioContext();
        if (audioCtx.state === 'suspended') audioCtx.resume();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        const now = audioCtx.currentTime;

        if (type === 'click') {
            osc.frequency.setValueAtTime(650, now);
            osc.frequency.exponentialRampToValueAtTime(850, now + 0.05);
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.05);
            osc.start(now);
            osc.stop(now + 0.05);
        } else if (type === 'flag') {
            osc.frequency.setValueAtTime(400, now);
            osc.frequency.exponentialRampToValueAtTime(600, now + 0.08);
            gain.gain.setValueAtTime(0.25, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.08);
            osc.start(now);
            osc.stop(now + 0.08);
        } else if (type === 'explode') {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(120, now);
            osc.frequency.exponentialRampToValueAtTime(30, now + 0.5);
            gain.gain.setValueAtTime(0.5, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            osc.start(now);
            osc.stop(now + 0.5);
        }
    } catch(e) {}
}

// ปลดล็อกสิทธิ์เสียงสำหรับเบราว์เซอร์และมือถือเมื่อผู้เล่นแตะหน้าจอครั้งแรก
let audioUnlocked = false;
let initialStartAudioFired = false;
function unlockGameAudio() {
    if (audioUnlocked) return;
    const witchAudio = document.getElementById('rezeroWitchAudio');
    const startAudio = document.getElementById('rezeroStartAudio');
    [witchAudio, startAudio].forEach(audio => {
        if (audio) {
            audio.volume = 1.0;
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
            }).catch(() => {});
        }
    });
    audioUnlocked = true;
}
window.addEventListener('click', unlockGameAudio, { once: true });
window.addEventListener('touchstart', unlockGameAudio, { once: true });

// เล่นเสียง ha.mp3 ตอนเริ่มเกม
function playStartGameAudio() {
    const audio = document.getElementById('rezeroStartAudio');
    if (audio) {
        audio.currentTime = 0;
        audio.volume = 1.0;
        const playPromise = audio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log('ha.mp3 start game audio played successfully');
            }).catch(e => {
                console.warn('Start audio waiting for user gesture:', e);
                const playOnGesture = () => {
                    audio.currentTime = 0;
                    audio.play().catch(() => {});
                    window.removeEventListener('click', playOnGesture);
                    window.removeEventListener('touchstart', playOnGesture);
                };
                window.addEventListener('click', playOnGesture, { once: true });
                window.addEventListener('touchstart', playOnGesture, { once: true });
            });
        }
    }
}

// เล่นเสียง i_love_you_satella.mp3 วนซ้ำเรื่อยๆ เมื่อแพ้
function startWitchAudioLoop() {
    const audio = document.getElementById('rezeroWitchAudio');
    if (audio) {
        audio.currentTime = 0;
        audio.volume = 1.0;
        audio.loop = true;
        const playPromise = audio.play();
        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log('Satella audio playing continuously in loop');
            }).catch(e => {
                console.warn('Autoplay waiting for touch/click interaction:', e);
                const resumeOnInteract = () => {
                    audio.play().catch(() => {});
                    window.removeEventListener('click', resumeOnInteract);
                    window.removeEventListener('touchstart', resumeOnInteract);
                };
                window.addEventListener('click', resumeOnInteract, { once: true });
                window.addEventListener('touchstart', resumeOnInteract, { once: true });
            });
        }
    }
}

// หยุดเสียงวนซ้ำทันทีเมื่อเริ่มใหม่หรือกลับหน้าหลัก
function stopWitchAudioLoop() {
    const audio = document.getElementById('rezeroWitchAudio');
    if (audio) {
        audio.pause();
        audio.currentTime = 0;
    }
}

// -------------------------------------------------------------
// Random Creepy Text Spawner (รันเรื่อยๆ จนกว่าจะเริ่มใหม่หรือออก)
// -------------------------------------------------------------
let creepyInterval = null;

function triggerRezeroCreepyTexts() {
    const overlay = document.getElementById('rezeroCreepyOverlay');
    overlay.innerHTML = '';
    overlay.style.display = 'block';

    const creepyPhrases = [
    "Subaru...",
    "I LOVE YOU...🖤",
    "ฉันเฝ้ามองเธออยู่...",
    "อย่าทิ้งฉันไป...",
    "ฉันจะอยู่กับเธอตลอดไป...",
    "ต่อให้เธอตายกี่ครั้ง...",
    "ฉันก็จะรอเธอ...",
    "เธอกลับมาหาฉันแล้ว...",
    "ฉันรู้ว่าเธอจะกลับมา...",
    "ช่วยฆ่าฉันที...",
    "Please... don't die...",
    "I love you, Subaru...",
    "ฉันไม่ยอมให้เธอหายไป...",
    "ตายแล้วเริ่มใหม่...",
    "ฉันรักเธอ...🖤",
    "🖤 🖤 🖤"
];

    const colors = ['#f43f5e', '#be185d', '#a855f7', '#e11d48', '#c084fc', '#ffffff'];

    // รันเรื่อยๆ ต่อเนื่อง (ไม่มีการตัด 10 วิ) จนกว่าจะกดยอมรับเริ่มใหม่หรือกลับหน้าหลัก
    if (creepyInterval) clearInterval(creepyInterval);
    creepyInterval = setInterval(() => {
        const item = document.createElement('div');
        item.className = 'creepy-text-item';
        const phrase = creepyPhrases[Math.floor(Math.random() * creepyPhrases.length)];
        item.innerText = phrase;

        const x = Math.floor(Math.random() * 85) + 5;
        const y = Math.floor(Math.random() * 85) + 5;
        const fontSize = (Math.random() * 1.5 + 1.1).toFixed(2);
        const color = colors[Math.floor(Math.random() * colors.length)];

        item.style.left = x + 'vw';
        item.style.top = y + 'vh';
        item.style.fontSize = fontSize + 'rem';
        item.style.color = color;
        item.style.textShadow = `0 0 12px ${color}`;

        overlay.appendChild(item);

        setTimeout(() => {
            if (item.parentNode) item.parentNode.removeChild(item);
        }, 2800);
    }, 240);
}

function stopRezeroCreepyTexts() {
    if (creepyInterval) {
        clearInterval(creepyInterval);
        creepyInterval = null;
    }
    const overlay = document.getElementById('rezeroCreepyOverlay');
    if (overlay) {
        overlay.style.display = 'none';
        overlay.innerHTML = '';
    }
}

// -------------------------------------------------------------
// Global Client Variables
// -------------------------------------------------------------
const roomId   = '<?= $room_id ?>';
const username = '<?= $username ?>';
let roomState = null;
let timerInterval = null;
let elapsedSeconds = 0;
let lastGameStatus = 'playing';
let defeatPopupTimeout = null;
const knownLeftPlayers = new Set();
let currentPromptedProposalId = null;

// Admin Cheat State
let adminCheatActive = false;
let currentSolutionBoard = null;

function toggleAdminCheatMode() {
    adminCheatActive = !adminCheatActive;
    const btn = document.getElementById('adminCheatBtn');
    if (btn) {
        if (adminCheatActive) {
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-eye-slash me-1"></i> <span>ซ่อนเฉลย (Admin)</span>';
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: '👁️ โหมดแอดมิน: เปิดแสดงเฉลยกระดานแล้ว',
                showConfirmButton: false,
                timer: 2000,
                background: '#1a082b',
                color: '#fff'
            });
        } else {
            btn.classList.remove('active');
            btn.innerHTML = '<i class="fas fa-eye text-danger me-1"></i> <span>เฉลย (Admin)</span>';
        }
    }
    if (roomState) {
        renderBoard(roomState);
    }
}

// Fetch State
async function fetchState() {
    try {
        const res = await fetch(`<?= base_url('minesweeper/get_state/') ?>${roomId}`);
        const data = await res.json();
        if (data.status === 'ok' && data.room) {
            if (data.solution_board) {
                currentSolutionBoard = data.solution_board;
            }
            renderRoom(data.room);
        }
    } catch (err) {
        console.error('Error fetching state:', err);
    }
}

// Render Room
function renderRoom(room) {
    roomState = room;

    // Difficulty Info Display
    const diffNames = {
        easy: 'ง่าย (9x9)',
        medium: 'ปานกลาง (16x16)',
        hard: 'ยาก (16x30)'
    };
    const diffEl = document.getElementById('diffText');
    if (diffEl) {
        diffEl.innerText = diffNames[room.difficulty] || (room.difficulty ? room.difficulty.toUpperCase() : 'ง่าย');
    }

    // ตรวจสอบคำขอเปลี่ยนระดับความยากจาก Host
    if (room.diff_proposal) {
        const prop = room.diff_proposal;
        if (prop.status === 'pending') {
            if (prop.proposed_by === username) {
                const votedCount = prop.votes ? Object.keys(prop.votes).length : 1;
                const totalCount = prop.total_count || (room.players ? room.players.length : 2);
                const titleEl = Swal.getTitle();
                if (titleEl && titleEl.innerText.includes('รอยืนยันเปลี่ยนระดับความยาก')) {
                    const htmlEl = Swal.getHtmlContainer();
                    if (htmlEl) {
                        htmlEl.innerHTML = `ส่งคำขอเปลี่ยนระดับความยากแล้ว...<br>กำลังรอเพื่อนในห้องกดยืนยันให้ครบ<br><b style="color: #a855f7; font-size: 1.25rem;">(ยอมรับแล้ว ${votedCount}/${totalCount} คน)</b>`;
                    }
                }
            } else if (!prop.votes || !prop.votes[username]) {
                if (currentPromptedProposalId !== prop.id) {
                    currentPromptedProposalId = prop.id;
                    Swal.fire({
                        title: '⚙️ ขอเปลี่ยนระดับความยาก',
                        html: `หัวหน้าห้อง (<b>${prop.proposed_by}</b>) ขอเปลี่ยนระดับความยากเป็น:<br><b style="color: #c084fc; font-size: 1.25rem; display: block; margin: 12px 0;">${prop.diff_name}</b>คุณต้องการยอมรับและเริ่มกระดานใหม่หรือไม่?`,
                        icon: 'question',
                        background: '#1a082b',
                        color: '#fff',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check me-1"></i> ยอมรับ',
                        cancelButtonText: '<i class="fas fa-times me-1"></i> ปฏิเสธ',
                        confirmButtonColor: '#a855f7',
                        cancelButtonColor: '#ef4444',
                        allowOutsideClick: false
                    }).then(async (res) => {
                        const ans = res.isConfirmed ? 'accept' : 'reject';
                        const fd = new FormData();
                        fd.append('response', ans);
                        try {
                            const r = await fetch(`<?= base_url('minesweeper/respond_difficulty/') ?>${roomId}`, {
                                method: 'POST',
                                body: fd
                            });
                            const d = await r.json();
                            if (d.status === 'ok') {
                                if (ans === 'accept' && d.action === 'waiting_others') {
                                    Swal.fire({
                                        title: '⏳ รอยืนยันเริ่มกระดานใหม่',
                                        html: `คุณกดยอมรับแล้ว กำลังรอผู้เล่นอื่น...<br><b style="color: #a855f7; font-size: 1.2rem;">(${d.voted_count}/${d.total_count} คน)</b>`,
                                        icon: 'info',
                                        background: '#1a082b',
                                        color: '#fff',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                } else if (d.action === 'all_accepted') {
                                    Swal.close();
                                }
                                fetchState();
                            }
                        } catch(e) {}
                    });
                }
            }
        } else if (prop.status === 'rejected') {
            const titleEl = Swal.getTitle();
            if (titleEl && (titleEl.innerText.includes('รอยืนยัน') || titleEl.innerText.includes('ขอเปลี่ยนระดับความยาก'))) {
                Swal.close();
            }
            if (currentPromptedProposalId === prop.id) {
                currentPromptedProposalId = null;
                Swal.fire({
                    title: '❌ ยกเลิกการเปลี่ยนระดับความยาก',
                    text: `ผู้เล่น ${prop.rejected_by} ปฏิเสธการเปลี่ยนระดับความยาก`,
                    icon: 'warning',
                    background: '#1a082b',
                    color: '#fff',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        }
    } else {
        const titleEl = Swal.getTitle();
        if (titleEl && (titleEl.innerText.includes('รอยืนยันเปลี่ยนระดับความยาก') || titleEl.innerText.includes('รอยืนยันเริ่มกระดานใหม่'))) {
            Swal.close();
            elapsedSeconds = 0;
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: 'เปลี่ยนระดับความยากเรียบร้อย! เริ่มกระดานใหม่แล้ว',
                showConfirmButton: false,
                timer: 2500,
                background: '#1a082b',
                color: '#fff'
            });
        }
        currentPromptedProposalId = null;
    }

    // Render Players
    renderPlayers(room);

    // LED Counters
    const flagsLeft = Math.max(0, (room.flags_left !== undefined ? room.flags_left : room.total_mines));
    document.getElementById('minesLed').innerText = String(flagsLeft).padStart(3, '0');

    // Update Face
    updateFaceView();

    // Render Board
    renderBoard(room);

    // Logs
    renderLogs(room.log || []);

    // ตรวจจับผู้เล่นที่กลับหน้าหลัก
    if (room.left_players && Array.isArray(room.left_players)) {
        room.left_players.forEach(p => {
            if (!knownLeftPlayers.has(p)) {
                knownLeftPlayers.add(p);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: `ผู้เล่น ${p} กลับหน้าหลักแล้ว`,
                    showConfirmButton: false,
                    timer: 3000,
                    background: '#1a082b',
                    color: '#fff'
                });
            }
        });
    }

    // ========================================================
    // 🖤 RE:ZERO POPUP ANNOUNCEMENT (ชนะ / แพ้)
    // ========================================================
    if (room.status !== lastGameStatus) {
        if (room.status === 'lost') {
            // แพ้ในธีม Re:Zero: เล่นเสียงระเบิด + เริ่มเสียง Satella วนซ้ำ + แสดงข้อความหลอนลอยสุ่มทั่วจอทันที
            playSfx('explode');
            startWitchAudioLoop();
            triggerRezeroCreepyTexts();

            // หน่วงเวลา 3 วินาที (3000ms) ให้เห็นการระเบิดและข้อความหลอนก่อน ค่อยแสดง Popup
            if (defeatPopupTimeout) clearTimeout(defeatPopupTimeout);
            defeatPopupTimeout = setTimeout(() => {
                if (!roomState || roomState.status !== 'lost') return;

                Swal.fire({
                    title: '<div class="rezero-shiver-title">I LOVE YOU 🖤</div>',
                    html: `
                        <div class="rezero-popup-slot">
                            ${getAssetHtml(REZERO_ASSETS.popup.defeat, 'rezero-popup-img', '🖤')}
                            <div class="rezero-popup-text">
                                พลาดเหยียบทุ่นระเบิดเข้าแล้ว... แต่ความตายเป็นเพียงจุดเริ่มต้นของการย้อนเวลากลับมา
                            </div>
                        </div>
                    `,
                    background: '#130420',
                    color: '#fff',
                    showCancelButton: true,
                    confirmButtonText: `${getAssetHtml(REZERO_ASSETS.buttons ? REZERO_ASSETS.buttons.defeat_retry : null, 'swal-btn-asset', '⏳')} Re:zero สู้ใหม่ไปตายซ้ำๆ`,
                    cancelButtonText: `${getAssetHtml(REZERO_ASSETS.buttons ? REZERO_ASSETS.buttons.defeat_giveup : null, 'swal-btn-asset', '💀')} ยอมแพ้แล้วไปใช้ชีวิตแบบไม่ตายกับเรม`,
                    confirmButtonColor: '#7e22ce',
                    cancelButtonColor: '#475569',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'border border-danger shadow-lg'
                    },
                    didOpen: () => {
                        startWitchAudioLoop();
                    }
                }).then((result) => {
                    stopWitchAudioLoop();
                    stopRezeroCreepyTexts();
                    if (result.isConfirmed) {
                        triggerRematch();
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = `<?= base_url('minesweeper/leave/') ?>${roomId}`;
                    }
                });
            }, 3000);

        } else if (room.status === 'won') {
            if (defeatPopupTimeout) clearTimeout(defeatPopupTimeout);
            playSfx('win');
            stopWitchAudioLoop();
            stopRezeroCreepyTexts();

            // ✅ เพิ่มเสียง Subaru ตอนชนะ 5 วินาทีตรงนี้
            const victoryAudio = document.getElementById('rezeroVictoryAudio');
            if (victoryAudio) {
                victoryAudio.currentTime = 0;
                victoryAudio.play().catch(err => console.error('เสียงชนะエラー:', err));
            }

            Swal.fire({
                title: '<div class="rezero-victory-title">👑 VICTORY</div>',
                html: `
                    <div class="rezero-popup-slot">
                        ${getAssetHtml(REZERO_ASSETS.popup.victory, 'rezero-popup-img', '👑')}
                        <div class="rezero-popup-text">
                            <b>ผู้ชนะ: ${room.winner || 'ทุกคนในทีม'}</b><br>
                            กู้ระเบิดสำเร็จสมบูรณ์ ปกป้องทุกคนไว้ได้สำเร็จ!
                        </div>
                    </div>
                `,
                background: '#130420',
                color: '#fff',
                showCancelButton: true,
                confirmButtonText: `${getAssetHtml(REZERO_ASSETS.buttons ? REZERO_ASSETS.buttons.victory_retry : null, 'swal-btn-asset', '✨')} Arc ต่อไปรออยู่ Re:zero `,
                cancelButtonText: `${getAssetHtml(REZERO_ASSETS.buttons ? REZERO_ASSETS.buttons.victory_leave : null, 'swal-btn-asset', '🏰')} กลับคฤหาสน์ไปกับเอมิเลียตัน`,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#475569',
                allowOutsideClick: false,
                customClass: {
                    popup: 'border border-warning shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    triggerRematch();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = `<?= base_url('minesweeper/leave/') ?>${roomId}`;
                }
            });

        } else if (room.status === 'playing') {
            if (defeatPopupTimeout) clearTimeout(defeatPopupTimeout);
            stopWitchAudioLoop();
            stopRezeroCreepyTexts();
            Swal.close();
            elapsedSeconds = 0;
            if (lastGameStatus === 'lost' || lastGameStatus === 'won') {
                playStartGameAudio();
            }
        }
        lastGameStatus = room.status;
    }

    // เล่นเสียง ha.mp3 ครั้งแรกเมื่อเข้ากระดานเกม
    if (!initialStartAudioFired && room.status === 'playing') {
        initialStartAudioFired = true;
        playStartGameAudio();
    }
}

// Render Players
function renderPlayers(room) {
    const strip = document.getElementById('playersStrip');
    strip.innerHTML = '';

    (room.players || []).forEach(p => {
        const isTurn = (room.current_turn === p && room.status === 'playing' && (room.game_mode === 'versus' || room.game_mode === 'hunter'));
        const isKnockout = (room.knockouts && room.knockouts[p]);
        const score = (room.scores && room.scores[p] !== undefined) ? room.scores[p] : 0;

        const card = document.createElement('div');
        card.className = `player-card ${isTurn ? 'active-turn' : ''} ${isKnockout ? 'knockout' : ''}`;
        card.innerHTML = `
            <div class="player-info">
                <div class="player-avatar">${p.charAt(0).toUpperCase()}</div>
                <div>
                    <div class="player-name">
                        ${p} ${p === room.host ? '<i class="fas fa-crown text-warning" title="Host"></i>' : ''}
                        ${p === username ? '<span style="font-size: 0.7rem; color: #c084fc;">(คุณ)</span>' : ''}
                    </div>
                    <small style="color: #a855f7; font-size: 0.75rem;">
                        ${isKnockout ? '<span class="text-danger">💥 ตกรอบ</span>' : (isTurn ? '<span class="text-warning font-bold">👉 กำลังเดินตา</span>' : 'พร้อม')}
                    </small>
                </div>
            </div>
            <div class="player-score">${score}</div>
        `;
        strip.appendChild(card);
    });
}

// Update Face View
function updateFaceView() {
    if (!roomState) return;
    const faceBtn = document.getElementById('faceBtn');
    const progressLabel = document.getElementById('progressLabel');

    const totalCells = roomState.rows * roomState.cols;
    const safeTotal = totalCells - roomState.total_mines;
    const revealed = roomState.revealed_count || 0;
    const percent = safeTotal > 0 ? Math.min(100, Math.round((revealed / safeTotal) * 100)) : 0;

    progressLabel.innerText = `เปิดแล้ว ${percent}%`;

    let stage = 'stage1';
    if (roomState.status === 'won') {
        stage = 'win';
    } else if (roomState.status === 'lost') {
        stage = 'lose';
    } else {
        if (percent >= 80) {
            stage = 'stage3';
        } else if (percent >= 40) {
            stage = 'stage2';
        } else {
            stage = 'stage1';
        }
    }

    const faceSlot = REZERO_ASSETS.faces ? REZERO_ASSETS.faces[stage] : null;
    const defaultEmoji = REZERO_FALLBACK_FACES[stage] || '😐';
    faceBtn.innerHTML = getAssetHtml(faceSlot, 'face-img', defaultEmoji);
}

// Render Board
function renderBoard(room) {
    const grid = document.getElementById('mineGrid');
    const rows = room.rows;
    const cols = room.cols;

    grid.style.gridTemplateColumns = `repeat(${cols}, 36px)`;

    if (grid.children.length !== rows * cols) {
        grid.innerHTML = '';
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.className = 'ms-cell';
                cell.id = `cell_${r}_${c}`;
                cell.dataset.r = r;
                cell.dataset.c = c;

                // Mobile / Touch: Long Press 380ms
                let touchTimer = null;
                let isLongPress = false;

                cell.addEventListener('touchstart', (e) => {
                    isLongPress = false;
                    touchTimer = setTimeout(() => {
                        isLongPress = true;
                        touchTimer = null;
                        if (navigator.vibrate) navigator.vibrate(50);
                        handleFlag(r, c);
                    }, 380);
                }, { passive: true });

                cell.addEventListener('touchmove', () => {
                    if (touchTimer) {
                        clearTimeout(touchTimer);
                        touchTimer = null;
                    }
                }, { passive: true });

                cell.addEventListener('touchend', (e) => {
                    if (touchTimer) {
                        clearTimeout(touchTimer);
                        touchTimer = null;
                    }
                });

                // Click / Tap
                cell.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (isLongPress) {
                        isLongPress = false;
                        return;
                    }
                    handleReveal(r, c);
                });

                // Context Menu (PC Right Click)
                cell.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    handleFlag(r, c);
                });

                grid.appendChild(cell);
            }
        }
    }

    if (room.board && room.board.length > 0) {
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cellEl = document.getElementById(`cell_${r}_${c}`);
                if (!cellEl) continue;
                const cellData = room.board[r] ? room.board[r][c] : null;
                if (!cellData) continue;

                if (cellData.r === 1) {
                    cellEl.className = 'ms-cell revealed';
                    if (cellData.m === 1) {
                        cellEl.classList.add('exploded');
                        cellEl.innerHTML = getAssetHtml(REZERO_ASSETS.mine, 'cell-img', '💣');
                    } else if (cellData.v > 0) {
                        cellEl.className = `ms-cell revealed num-${cellData.v}`;
                        cellEl.innerText = cellData.v;
                    } else {
                        cellEl.innerText = '';
                    }
                } else {
                    cellEl.className = 'ms-cell';
                    if (cellData.f === 1) {
                        cellEl.innerHTML = getAssetHtml(REZERO_ASSETS.flag, 'cell-img', '🚩');
                    } else {
                        cellEl.innerHTML = '';
                    }

                    // Admin Cheat Mode: แสดงเฉลยบนช่องที่ยังไม่เปิด
                    if (adminCheatActive && currentSolutionBoard && currentSolutionBoard[r] && currentSolutionBoard[r][c]) {
                        const sol = currentSolutionBoard[r][c];
                        if (sol.m === 1) {
                            cellEl.classList.add('admin-revealed-mine');
                            if (cellData.f !== 1) {
                                cellEl.innerHTML = '<span class="admin-hint-mine">💣</span>';
                            }
                        } else {
                            cellEl.classList.add('admin-revealed-safe');
                            if (cellData.f !== 1) {
                                if (sol.v > 0) {
                                    cellEl.innerHTML = `<span class="admin-hint-text num-${sol.v}">${sol.v}</span>`;
                                } else {
                                    cellEl.innerHTML = '<span class="admin-hint-text" style="color: #c084fc;">·</span>';
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        // เมื่อเพิ่งเริ่มกระดานใหม่ (board = []) ให้ล้างหน้ากระดานกลับเป็นช่องเริ่มต้นทันที
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cellEl = document.getElementById(`cell_${r}_${c}`);
                if (cellEl) {
                    cellEl.className = 'ms-cell';
                    cellEl.innerHTML = '';
                    cellEl.innerText = '';
                }
            }
        }
    }
}

// Reveal Cell
async function handleReveal(r, c) {
    if (!roomState || roomState.status !== 'playing') return;

    if (roomState.players.length > 1 && (roomState.game_mode === 'versus' || roomState.game_mode === 'hunter')) {
        if (roomState.current_turn !== username) {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'warning',
                title: 'ยังไม่ใช่ตาของคุณ!',
                showConfirmButton: false,
                timer: 1500,
                background: '#1a082b',
                color: '#fff'
            });
            return;
        }
    }

    playSfx('click');

    const formData = new FormData();
    formData.append('r', r);
    formData.append('c', c);

    try {
        const res = await fetch(`<?= base_url('minesweeper/reveal/') ?>${roomId}`, {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status === 'ok') {
            fetchState();
        } else if (data.message) {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'info',
                title: data.message,
                showConfirmButton: false,
                timer: 1500,
                background: '#1a082b',
                color: '#fff'
            });
        }
    } catch (e) {
        console.error(e);
    }
}

// Flag Cell
async function handleFlag(r, c) {
    if (!roomState || roomState.status !== 'playing') return;

    playSfx('flag');

    const formData = new FormData();
    formData.append('r', r);
    formData.append('c', c);

    try {
        const res = await fetch(`<?= base_url('minesweeper/flag/') ?>${roomId}`, {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status === 'ok') {
            fetchState();
        }
    } catch (e) {
        console.error(e);
    }
}

// Rematch (หยุดเสียงและเอฟเฟกต์ทันที)
async function triggerRematch() {
    if (defeatPopupTimeout) clearTimeout(defeatPopupTimeout);
    stopWitchAudioLoop();
    stopRezeroCreepyTexts();
    try {
        const res = await fetch(`<?= base_url('minesweeper/rematch/') ?>${roomId}`);
        const data = await res.json();
        if (data.status === 'ok') {
            if (data.action === 'waiting_others') {
                Swal.fire({
                    title: '⏳ รอยืนยัน Re:Zero',
                    html: `คุณกดยืนยันแล้ว กำลังรอเพื่อนในห้องกดยืนยันให้ครบ...<br><b style="color: #a855f7; font-size: 1.25rem;">(ยืนยันแล้ว ${data.voted_count}/${data.total_count} คน)</b>`,
                    icon: 'info',
                    background: '#1a082b',
                    color: '#fff',
                    showCancelButton: true,
                    cancelButtonText: '💀 ยอมแพ้กลับหน้าหลัก',
                    showConfirmButton: false,
                    cancelButtonColor: '#475569',
                    allowOutsideClick: true
                }).then((res) => {
                    if (res.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = `<?= base_url('minesweeper/leave/') ?>${roomId}`;
                    }
                });
            } else {
                Swal.close();
                elapsedSeconds = 0;
                lastGameStatus = 'playing';
                playStartGameAudio();
                const grid = document.getElementById('mineGrid');
                if (grid) {
                    grid.querySelectorAll('.ms-cell').forEach(cell => {
                        cell.className = 'ms-cell';
                        cell.innerHTML = '';
                        cell.innerText = '';
                    });
                }
                fetchState();
            }
        }
    } catch (e) {
        console.error(e);
    }
}

// Host Difficulty Settings Modal
function openHostSettings() {
    const modalHtml = document.getElementById('hostModal').innerHTML;
    Swal.fire({
        title: '⚙️ เปลี่ยนระดับความยาก (Re:Zero)',
        html: modalHtml,
        background: '#1a082b',
        color: '#fff',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check me-1"></i> ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#7e22ce',
        cancelButtonColor: '#475569',
        didOpen: () => {
            if (roomState) {
                document.querySelectorAll('#modalDiffSelect option').forEach(o => {
                    if (o.value === roomState.difficulty) o.selected = true;
                });
            }
        },
        preConfirm: () => {
            const popup = Swal.getPopup();
            const diff = popup.querySelector('#modalDiffSelect').value;
            return { diff };
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('difficulty', result.value.diff);

            const res = await fetch(`<?= base_url('minesweeper/propose_difficulty/') ?>${roomId}`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.status === 'ok') {
                if (data.action === 'waiting_others') {
                    Swal.fire({
                        title: '⏳ รอยืนยันเปลี่ยนระดับความยาก',
                        html: `ส่งคำขอเปลี่ยนระดับความยากแล้ว...<br>กำลังรอเพื่อนในห้องกดยืนยันให้ครบ<br><b style="color: #a855f7; font-size: 1.25rem;">(ยอมรับแล้ว ${data.voted_count}/${data.total_count} คน)</b>`,
                        icon: 'info',
                        background: '#1a082b',
                        color: '#fff',
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'ยกเลิกคำขอ',
                        cancelButtonColor: '#475569'
                    }).then(async (waitRes) => {
                        if (waitRes.dismiss === Swal.DismissReason.cancel) {
                            const cancelFd = new FormData();
                            cancelFd.append('response', 'reject');
                            await fetch(`<?= base_url('minesweeper/respond_difficulty/') ?>${roomId}`, {
                                method: 'POST',
                                body: cancelFd
                            });
                            fetchState();
                        }
                    });
                } else {
                    Swal.close();
                    elapsedSeconds = 0;
                    lastGameStatus = 'playing';
                    fetchState();
                }
            } else if (data.message) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.message,
                    background: '#1a082b',
                    color: '#fff'
                });
            }
        }
    });
}

// Activity Log Render
function renderLogs(logs) {
    const logContainer = document.getElementById('logContainer');
    logContainer.innerHTML = '';
    logs.forEach(msg => {
        const item = document.createElement('div');
        item.className = 'log-item';
        item.innerHTML = `<i class="fas fa-chevron-right" style="color: #a855f7; font-size: 0.7rem;"></i> ${msg}`;
        logContainer.appendChild(item);
    });
}

// Timer Loop
timerInterval = setInterval(() => {
    if (roomState && roomState.status === 'playing' && roomState.board_ready) {
        elapsedSeconds++;
        document.getElementById('timerLed').innerText = String(Math.min(999, elapsedSeconds)).padStart(3, '0');
    }
}, 1000);

// Polling Loop
setInterval(fetchState, 1200);
fetchState();
</script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
