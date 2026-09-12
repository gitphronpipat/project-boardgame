<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Minesweeper View Component
 * รองรับ:
 * - ระบบเล่นคนเดียว และผู้เล่น 1-4 คน (Versus, Co-op, Hunter, Solo)
 * - ระดับความยาก 3 ระดับ (ง่าย, ปานกลาง, ยาก)
 * - หน้าแสดงอารมณ์ 5 รูปแบบตาม % ความคืบหน้าของเกม
 * - ช่องใส่รูปภาพสำหรับ หน้าตา, ธง, ระเบิด และตัวเลข 1-8
 * - Unified Dynamic Navbar
 */

$room_id   = isset($room_id) ? $room_id : '';
$username  = isset($username) ? $username : '';
$room_data = isset($room) ? $room : [];
$host      = isset($room_data['host']) ? $room_data['host'] : $username;
$is_host   = ($host === $username);
$is_admin  = !empty($is_admin) || ($this->session->userdata('role') === 'admin');

// โฟลเดอร์สำหรับใส่รูปภาพ Assets ของเกม
$image_base_url = base_url('application/views/games/minesweeper/image/');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Minesweeper' ?></title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;700;800&family=Prompt:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS (สำหรับ Navbar และ Components) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-dark: #0f172a;
            --panel-bg: rgba(30, 41, 59, 0.85);
            --border-glow: rgba(99, 102, 241, 0.25);
            --retro-board: #1e293b;
            --cell-hidden: #334155;
            --cell-hidden-hover: #475569;
            --cell-revealed: #0f172a;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-amber: #f59e0b;
            --accent-blue: #3b82f6;
            --led-red: #ff3344;
            --led-bg: #1a0508;
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
            background: radial-gradient(circle at 50% 10%, #1e1b4b 0%, #090d16 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            user-select: none;
        }

        /* Arena Layout */
        .ms-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            gap: 1.2rem;
        }

        /* Top HUD / Info Cards */
        .ms-top-bar {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            background: var(--panel-bg);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 0.9rem 1.4rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .ms-room-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .badge-mode {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            padding: 0.35rem 0.8rem;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-diff {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 0.35rem 0.8rem;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .ms-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-ms {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #fff;
            padding: 0.45rem 1rem;
            border-radius: 10px;
            font-family: 'Prompt', sans-serif;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-ms:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Players Strip */
        .ms-players-strip {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0.9rem;
        }

        .player-card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .player-card.active-turn {
            border-color: #6366f1;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.35);
            background: rgba(99, 102, 241, 0.12);
        }

        .player-card.knockout {
            opacity: 0.45;
            filter: grayscale(0.8);
            border-color: #ef4444;
        }

        .player-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .player-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        }

        .player-name {
            font-weight: 600;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .player-score {
            font-family: 'Share Tech Mono', monospace;
            font-size: 1.3rem;
            font-weight: 700;
            color: #38bdf8;
        }

        /* Minesweeper Console Frame (Retro-Modern) */
        .ms-frame {
            background: #1e293b;
            border: 4px solid #334155;
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), inset 0 2px 8px rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 100%;
        }

        /* Retro LCD Panel */
        .ms-header-panel {
            width: 100%;
            background: #0f172a;
            border: 3px solid #090d16;
            border-radius: 12px;
            padding: 0.75rem 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
            box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.8);
        }

        /* 7-Segment LED Digits */
        .led-display {
            background: var(--led-bg);
            color: var(--led-red);
            font-family: 'Share Tech Mono', monospace;
            font-size: 2.3rem;
            font-weight: 700;
            letter-spacing: 4px;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            border: 1px solid #33080e;
            text-shadow: 0 0 10px rgba(255, 51, 68, 0.7);
            min-width: 95px;
            text-align: center;
        }

        /* Center Dynamic Face Reaction Button */
        .face-btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .face-btn {
            width: 58px;
            height: 58px;
            border-radius: 12px;
            background: linear-gradient(180deg, #fef08a 0%, #eab308 100%);
            border: 3px solid #ca8a04;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.1rem;
            cursor: pointer;
            box-shadow: 0 4px 0 #a16207, 0 8px 15px rgba(0, 0, 0, 0.4);
            transition: all 0.1s ease;
            position: relative;
            overflow: hidden;
        }

        .face-btn:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 #a16207;
        }

        .face-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 9px;
        }

        .face-progress-label {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Board Container */
        .ms-board-scroll {
            max-width: 100%;
            overflow: auto;
            padding: 6px;
            border-radius: 8px;
            background: #090d16;
            box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.8);
        }

        .ms-grid {
            display: grid;
            gap: 3px;
            background: #090d16;
            padding: 4px;
            border-radius: 6px;
        }

        /* Cell Styling */
        .ms-cell {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: var(--cell-hidden);
            border: 2px solid #475569;
            box-shadow: inset 1px 1px 0 rgba(255, 255, 255, 0.2), inset -1px -1px 0 rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            cursor: pointer;
            transition: background 0.15s, transform 0.05s;
            position: relative;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
            touch-action: manipulation;
        }

        .ms-cell:hover:not(.revealed) {
            background: var(--cell-hidden-hover);
            transform: scale(0.97);
        }

        .ms-cell.revealed {
            background: var(--cell-revealed);
            border-color: #1e293b;
            box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.6);
            cursor: default;
        }

        .ms-cell.exploded {
            background: #ef4444 !important;
            animation: pulse-explode 0.4s ease;
        }

        @keyframes pulse-explode {
            0% { transform: scale(1); }
            50% { transform: scale(1.25); background: #ff7777; }
            100% { transform: scale(1); }
        }

        /* Number Colors (Classic Minesweeper) */
        .num-1 { color: #3b82f6; text-shadow: 0 0 6px rgba(59, 130, 246, 0.4); }
        .num-2 { color: #10b981; text-shadow: 0 0 6px rgba(16, 185, 129, 0.4); }
        .num-3 { color: #ef4444; text-shadow: 0 0 6px rgba(239, 68, 68, 0.4); }
        .num-4 { color: #6366f1; text-shadow: 0 0 6px rgba(99, 102, 241, 0.4); }
        .num-5 { color: #b91c1c; }
        .num-6 { color: #0891b2; }
        .num-7 { color: #000000; }
        .num-8 { color: #64748b; }

        .cell-img {
            width: 24px;
            height: 24px;
            object-fit: contain;
            pointer-events: none;
        }

        /* Device Controls Hint Bar */
        .device-controls-hint {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 1rem;
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.5rem 1.1rem;
            border-radius: 30px;
            font-size: 0.82rem;
            color: #94a3b8;
        }

        .hint-badge {
            display: inline-flex;
            align-items: center;
            line-height: 1.4;
        }

        /* Live Activity Log */
        .ms-log-container {
            width: 100%;
            background: var(--panel-bg);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 0.9rem 1.2rem;
            max-height: 140px;
            overflow-y: auto;
            font-size: 0.83rem;
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

        /* Team HP Hearts */
        .hp-bar {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #ef4444;
            font-size: 1.2rem;
        }

        /* Admin Cheat Button & Overlay (Classic Dark Theme) */
        .btn-admin-cheat {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444 !important;
            color: #fca5a5 !important;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
            transition: all 0.25s ease;
        }
        .btn-admin-cheat:hover {
            background: rgba(239, 68, 68, 0.35);
            border-color: #f87171 !important;
            color: #fff !important;
            box-shadow: 0 0 16px rgba(239, 68, 68, 0.55);
            transform: translateY(-1px);
        }
        .btn-admin-cheat.active {
            background: linear-gradient(135deg, #ef4444, #b91c1c) !important;
            border-color: #fca5a5 !important;
            color: #fff !important;
            font-weight: 700;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.85);
            animation: cheat-pulse-classic 1.2s infinite alternate;
        }
        @keyframes cheat-pulse-classic {
            from { box-shadow: 0 0 8px rgba(239, 68, 68, 0.5); }
            to { box-shadow: 0 0 22px rgba(239, 68, 68, 0.95); }
        }

        /* Admin Hint Overlay on Cells */
        .ms-cell.admin-revealed-mine {
            background: rgba(239, 68, 68, 0.28) !important;
            border: 1.5px dashed #ef4444 !important;
            box-shadow: inset 0 0 8px rgba(239, 68, 68, 0.7) !important;
        }
        .ms-cell.admin-revealed-safe {
            background: rgba(99, 102, 241, 0.12) !important;
            border: 1px dashed rgba(99, 102, 241, 0.45) !important;
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
            filter: drop-shadow(0 0 6px #ef4444);
            pointer-events: none;
        }
    </style>
</head>
<body>

<!-- Unified Dynamic Navbar -->
<?php $this->load->view('player/components/navbar', [
    'nav_mode'   => 'in_game',
    'game_name'  => 'Minesweeper (เกมกู้ระเบิด)',
    'room_id'    => $room_id,
    'leave_url'  => base_url('minesweeper/leave/' . $room_id),
    'user_stats' => isset($user_stats) ? $user_stats : null,
]); ?>

<div class="ms-wrapper">

    <!-- Top HUD Bar -->
    <div class="ms-top-bar">
        <div class="ms-room-info">
            <span class="badge-diff" id="diffBadge">
                <i class="fas fa-layer-group text-success me-1"></i> ระดับ: <strong id="diffText">ง่าย</strong>
            </span>
            <span id="coopHpContainer" style="display: none;" class="badge-diff" style="border-color: #ef4444; color: #f87171;">
                พลังชีวิตทีม: <span id="teamHpDisplay" class="hp-bar">❤️❤️❤️</span>
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

    <!-- Players Strip (1-4 Players) -->
    <div class="ms-players-strip" id="playersStrip">
        <!-- Dynamic Player Cards generated by JS -->
    </div>

    <!-- Main Retro-Modern Console Frame -->
    <div class="ms-frame">

        <!-- Retro LCD Display Header -->
        <div class="ms-header-panel">
            <!-- Left: Flags / Mines Remaining LED -->
            <div title="จำนวนธงคงเหลือ" class="led-display" id="minesLed">010</div>

            <!-- Center: Dynamic Reaction Face Button -->
            <div class="face-btn-container">
                <button class="face-btn" id="faceBtn" onclick="triggerRematch()" title="คลิกเพื่อเริ่มเกมใหม่">
                    <span id="faceIcon">🙂</span>
                </button>
                <span class="face-progress-label" id="progressLabel">เปิดแล้ว 0%</span>
            </div>

            <!-- Right: Timer / Turn LED -->
            <div title="เวลาเล่น (วินาที)" class="led-display" id="timerLed">000</div>
        </div>

        <!-- Board Grid Container (Scrollable for large grids) -->
        <div class="ms-board-scroll">
            <div class="ms-grid" id="mineGrid">
                <!-- Cells rendered dynamically -->
            </div>
        </div>

        <!-- Device Controls Hint Bar (แทนที่ปุ่มเปลี่ยนโหมดเดิม) -->
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
        <div class="log-item"><i class="fas fa-info-circle text-info"></i> เชื่อมต่อห้องเกม Minesweeper เรียบร้อย...</div>
    </div>

</div>

<!-- Host Settings Modal Template -->
<div id="hostModal" style="display: none;">
    <div style="text-align: left; font-size: 0.95rem;">
        <label style="font-weight: 600; display: block; margin-bottom: 8px; color: #cbd5e1;">
            <i class="fas fa-layer-group text-success me-1"></i> เลือกระดับความยากที่ต้องการ:
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
 * 🎨 ASSET CONFIGURATION & SLOTS (สำหรับใส่รูปภาพได้เอง)
 * -------------------------------------------------------------
 * สามารถนำไฟล์ภาพนามสกุล .png หรือ .webp ไปวางใน:
 * application/views/games/minesweeper/image/
 * หากยังไม่มีไฟล์ภาพ ระบบจะมี Fallback แสดง Emoji / SVG และตัวเลขสีอัตโนมัติ
 */
const ASSET_SLOTS = {
    // หน้าแสดงอารมณ์ 5 รูปแบบ (% ช่องปลอดภัยที่เปิดแล้ว)
    faces: {
        stage1: '<?= $image_base_url ?>face_stage1.png', // 0% - 40%
        stage2: '<?= $image_base_url ?>face_stage2.png', // 40% - 80%
        stage3: '<?= $image_base_url ?>face_stage3.png', // 80% - 100%
        win:    '<?= $image_base_url ?>face_win.png',    // ชนะ
        lose:   '<?= $image_base_url ?>face_lose.png',   // แพ้
    },
    // รูปธงและระเบิด
    flag: '<?= $image_base_url ?>flag.png',
    mine: '<?= $image_base_url ?>mine.png',
    
    // ช่องใส่รูปภาพตัวเลข 1 - 8 (เผื่อใส่ภาพตัวเลขแบบ Custom)
    numbers: {
        1: '<?= $image_base_url ?>num_1.png',
        2: '<?= $image_base_url ?>num_2.png',
        3: '<?= $image_base_url ?>num_3.png',
        4: '<?= $image_base_url ?>num_4.png',
        5: '<?= $image_base_url ?>num_5.png',
        6: '<?= $image_base_url ?>num_6.png',
        7: '<?= $image_base_url ?>num_7.png',
        8: '<?= $image_base_url ?>num_8.png',
    }
};

// ตรวจสอบภาพที่โหลดได้จริงในแคช
const loadedAssets = {};
function preloadAsset(key, src) {
    if (!src) return;
    const img = new Image();
    img.onload = () => { loadedAssets[key] = src; updateFaceView(); };
    img.onerror = () => { loadedAssets[key] = false; };
    img.src = src;
}

// โหลดรูปภาพตรวจสอบเบื้องต้น
preloadAsset('face_stage1', ASSET_SLOTS.faces.stage1);
preloadAsset('face_stage2', ASSET_SLOTS.faces.stage2);
preloadAsset('face_stage3', ASSET_SLOTS.faces.stage3);
preloadAsset('face_win', ASSET_SLOTS.faces.win);
preloadAsset('face_lose', ASSET_SLOTS.faces.lose);
preloadAsset('flag', ASSET_SLOTS.flag);
preloadAsset('mine', ASSET_SLOTS.mine);
for (let n = 1; n <= 8; n++) {
    preloadAsset('num_' + n, ASSET_SLOTS.numbers[n]);
}

// Audio Synthesis for Game SFX (เสียงสังเคราะห์ ไม่ต้องใช้ไฟล์ mp3)
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
            osc.frequency.setValueAtTime(600, now);
            osc.frequency.exponentialRampToValueAtTime(800, now + 0.05);
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.05);
            osc.start(now);
            osc.stop(now + 0.05);
        } else if (type === 'flag') {
            osc.frequency.setValueAtTime(350, now);
            osc.frequency.exponentialRampToValueAtTime(500, now + 0.08);
            gain.gain.setValueAtTime(0.25, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.08);
            osc.start(now);
            osc.stop(now + 0.08);
        } else if (type === 'explode') {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(150, now);
            osc.frequency.exponentialRampToValueAtTime(40, now + 0.35);
            gain.gain.setValueAtTime(0.4, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
            osc.start(now);
            osc.stop(now + 0.35);
        } else if (type === 'win') {
            osc.frequency.setValueAtTime(523.25, now);
            osc.frequency.setValueAtTime(659.25, now + 0.1);
            osc.frequency.setValueAtTime(783.99, now + 0.2);
            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
            osc.start(now);
            osc.stop(now + 0.35);
        }
    } catch(e) {}
}

// Global Client Variables
const roomId   = '<?= $room_id ?>';
const username = '<?= $username ?>';
let roomState = null;
let timerInterval = null;
let elapsedSeconds = 0;
let lastGameStatus = 'playing';
const knownLeftPlayers = new Set();
let currentPromptedProposalId = null;

// Fallback Face Emojis
const FACE_EMOJIS = {
    stage1: '🙂', // 0 - 40%
    stage2: '😰', // 40 - 80%
    stage3: '😱', // 80 - 100%
    win:    '😎', // ชนะ
    lose:   '😵', // แพ้
};

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
                background: '#1e293b',
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

// -------------------------------------------------------------
// Fetch Real-time Room State (Polling 1.2s)
// -------------------------------------------------------------
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

// Render Entire Room & Board
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

    // Co-op HP (ถ้ามี)
    const hpContainer = document.getElementById('coopHpContainer');
    if (hpContainer) {
        if (room.game_mode === 'coop') {
            hpContainer.style.display = 'inline-flex';
            let hearts = '';
            for (let i = 0; i < room.max_team_hp; i++) {
                hearts += (i < room.team_hp) ? '❤️' : '🖤';
            }
            document.getElementById('teamHpDisplay').innerHTML = hearts;
        } else {
            hpContainer.style.display = 'none';
        }
    }

    // ตรวจสอบคำขอเปลี่ยนระดับความยากจาก Host (ระบบยอมรับร่วมกัน)
    if (room.diff_proposal) {
        const prop = room.diff_proposal;
        if (prop.status === 'pending') {
            // กรณีเป็น Host: อัปเดตตัวเลขจำนวนคนที่ยอมรับแบบ Real-time บน Modal
            if (prop.proposed_by === username) {
                const votedCount = prop.votes ? Object.keys(prop.votes).length : 1;
                const totalCount = prop.total_count || (room.players ? room.players.length : 2);
                const titleEl = Swal.getTitle();
                if (titleEl && titleEl.innerText.includes('รอยืนยันเปลี่ยนระดับความยาก')) {
                    const htmlEl = Swal.getHtmlContainer();
                    if (htmlEl) {
                        htmlEl.innerHTML = `ส่งคำขอเปลี่ยนระดับความยากแล้ว...<br>กำลังรอเพื่อนในห้องกดยืนยันให้ครบ<br><b style="color: #6366f1; font-size: 1.25rem;">(ยอมรับแล้ว ${votedCount}/${totalCount} คน)</b>`;
                    }
                }
            } else if (!prop.votes || !prop.votes[username]) {
                // ถ้าเป็นผู้เล่นคนอื่นที่ยังไม่ได้ตอบรับ
                if (currentPromptedProposalId !== prop.id) {
                    currentPromptedProposalId = prop.id;
                    Swal.fire({
                        title: '⚙️ ขอเปลี่ยนระดับความยาก',
                        html: `หัวหน้าห้อง (<b>${prop.proposed_by}</b>) ขอเปลี่ยนระดับความยากเป็น:<br><b style="color: #38bdf8; font-size: 1.25rem; display: block; margin: 12px 0;">${prop.diff_name}</b>คุณต้องการยอมรับและเริ่มกระดานใหม่หรือไม่?`,
                        icon: 'question',
                        background: '#1e293b',
                        color: '#fff',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check me-1"></i> ยอมรับ',
                        cancelButtonText: '<i class="fas fa-times me-1"></i> ปฏิเสธ',
                        confirmButtonColor: '#10b981',
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
                                        html: `คุณกดยอมรับแล้ว กำลังรอผู้เล่นอื่น...<br><b style="color: #6366f1; font-size: 1.2rem;">(${d.voted_count}/${d.total_count} คน)</b>`,
                                        icon: 'info',
                                        background: '#1e293b',
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
                    background: '#1e293b',
                    color: '#fff',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        }
    } else {
        // เมื่อ room.diff_proposal ไม่มีแล้ว (เพราะทุกคนกดยอมรับครบ และเริ่มกระดานใหม่แล้ว)
        // สั่งปิดหน้าต่างรอที่ค้างอยู่บนหน้าจอของ Host และเพื่อนทุกคนทันที!
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
                background: '#1e293b',
                color: '#fff'
            });
        }
        currentPromptedProposalId = null;
    }

    // Render Players
    renderPlayers(room);

    // Render LED Counters
    const flagsLeft = Math.max(0, (room.flags_left !== undefined ? room.flags_left : room.total_mines));
    document.getElementById('minesLed').innerText = String(flagsLeft).padStart(3, '0');

    // Update Reaction Face
    updateFaceView();

    // Render Board Grid
    renderBoard(room);

    // Render Logs
    renderLogs(room.log || []);

    // ตรวจจับผู้เล่นที่กลับหน้าหลักแล้ว
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
                    background: '#1e293b',
                    color: '#fff'
                });
            }
        });
    }

    // Win/Lose Announcement
    if (room.status !== lastGameStatus) {
        if (room.status === 'won' || room.status === 'lost') {
            const isWin = (room.status === 'won');
            if (isWin) playSfx('win');
            else playSfx('explode');

            Swal.fire({
                icon: isWin ? 'success' : 'error',
                title: isWin ? '🎉 ชัยชนะเป็นของคุณ!' : '💥 ระเบิดทำงาน!',
                html: isWin 
                    ? `<b>ผู้ชนะ: ${room.winner || 'ทีมทุกคน'}</b><br>เปิดกระดานสำเร็จครบสมบูรณ์` 
                    : `พลาดเหยียบทุ่นระเบิดเข้าแล้ว!`,
                background: '#1e293b',
                color: '#fff',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-redo me-1"></i> เริ่มเกมใหม่',
                cancelButtonText: '<i class="fas fa-home me-1"></i> กลับหน้าหลัก',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#475569',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    triggerRematch();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = `<?= base_url('minesweeper/leave/') ?>${roomId}`;
                }
            });
        } else if (room.status === 'playing') {
            // ปิด Modal ทั้งหมดอัตโนมัติเมื่อห้องเริ่มรอบใหม่
            Swal.close();
            elapsedSeconds = 0;
        }
        lastGameStatus = room.status;
    }
}

// Update Players Strip
function renderPlayers(room) {
    const strip = document.getElementById('playersStrip');
    strip.innerHTML = '';

    (room.players || []).forEach((p, idx) => {
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
                        ${p === username ? '<span style="font-size: 0.7rem; color: #818cf8;">(คุณ)</span>' : ''}
                    </div>
                    <small style="color: #94a3b8; font-size: 0.75rem;">
                        ${isKnockout ? '<span class="text-danger">💥 ตกรอบ</span>' : (isTurn ? '<span class="text-primary font-bold">👉 กำลังเดินตา</span>' : 'พร้อม')}
                    </small>
                </div>
            </div>
            <div class="player-score">${score}</div>
        `;
        strip.appendChild(card);
    });
}

// Update 5-Stage Face Reaction
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
            stage = 'stage3'; // 80 - 100%
        } else if (percent >= 40) {
            stage = 'stage2'; // 40 - 80%
        } else {
            stage = 'stage1'; // 0 - 40%
        }
    }

    // เช็คว่ามีไฟล์ภาพใน Slot หรือไม่
    const assetKey = 'face_' + stage;
    if (loadedAssets[assetKey]) {
        faceBtn.innerHTML = `<img src="${loadedAssets[assetKey]}" alt="${stage}">`;
    } else {
        faceBtn.innerHTML = `<span>${FACE_EMOJIS[stage]}</span>`;
    }
}

// Render Board Matrix
function renderBoard(room) {
    const grid = document.getElementById('mineGrid');
    const rows = room.rows;
    const cols = room.cols;

    // ตั้งค่า CSS Grid Columns
    grid.style.gridTemplateColumns = `repeat(${cols}, 36px)`;

    // ถ้าจำนวนช่องเปลี่ยนไป ให้สร้างโครงใหม่
    if (grid.children.length !== rows * cols) {
        grid.innerHTML = '';
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.className = 'ms-cell';
                cell.id = `cell_${r}_${c}`;
                cell.dataset.r = r;
                cell.dataset.c = c;

                // Mobile / Touch: แตะค้าง (Long Press 380ms) เพื่อปักธง
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

                // Left click / Tap
                cell.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (isLongPress) {
                        isLongPress = false;
                        return; // แตะค้างเพื่อปักธงไปแล้ว ไม่ต้องขุดซ้ำ
                    }
                    handleReveal(r, c);
                });

                // Right click (PC / Mac)
                cell.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                    handleFlag(r, c);
                });

                grid.appendChild(cell);
            }
        }
    }

    // อัปเดตข้อมูลแต่ละช่อง
    if (room.board && room.board.length > 0) {
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cellEl = document.getElementById(`cell_${r}_${c}`);
                if (!cellEl) continue;
                const cellData = room.board[r] ? room.board[r][c] : null;
                if (!cellData) continue;

                if (cellData.r === 1) {
                    // เปิดแล้ว
                    cellEl.className = 'ms-cell revealed';
                    if (cellData.m === 1) {
                        cellEl.classList.add('exploded');
                        if (loadedAssets['mine']) {
                            cellEl.innerHTML = `<img src="${loadedAssets['mine']}" class="cell-img" alt="mine">`;
                        } else {
                            cellEl.innerHTML = '💣';
                        }
                    } else if (cellData.v > 0) {
                        const numAsset = loadedAssets['num_' + cellData.v];
                        if (numAsset) {
                            cellEl.innerHTML = `<img src="${numAsset}" class="cell-img" alt="${cellData.v}">`;
                        } else {
                            cellEl.className = `ms-cell revealed num-${cellData.v}`;
                            cellEl.innerText = cellData.v;
                        }
                    } else {
                        cellEl.innerText = '';
                    }
                } else {
                    // ยังไม่เปิด
                    cellEl.className = 'ms-cell';
                    if (cellData.f === 1) {
                        if (loadedAssets['flag']) {
                            cellEl.innerHTML = `<img src="${loadedAssets['flag']}" class="cell-img" alt="flag">`;
                        } else {
                            cellEl.innerHTML = '🚩';
                        }
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
                                    cellEl.innerHTML = '<span class="admin-hint-text" style="color: #64748b;">·</span>';
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

// Action: Reveal Cell
async function handleReveal(r, c) {
    if (!roomState || roomState.status !== 'playing') return;

    // Check Turn
    if (roomState.players.length > 1 && (roomState.game_mode === 'versus' || roomState.game_mode === 'hunter')) {
        if (roomState.current_turn !== username) {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'warning',
                title: 'ยังไม่ใช่ตาของคุณ!',
                showConfirmButton: false,
                timer: 1500,
                background: '#1e293b',
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
                background: '#1e293b',
                color: '#fff'
            });
        }
    } catch (e) {
        console.error(e);
    }
}

// Action: Toggle Flag
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

// Action: Rematch
async function triggerRematch() {
    try {
        const res = await fetch(`<?= base_url('minesweeper/rematch/') ?>${roomId}`);
        const data = await res.json();
        if (data.status === 'ok') {
            if (data.action === 'waiting_others') {
                Swal.fire({
                    title: '⏳ รอยืนยันเริ่มเกมใหม่',
                    html: `คุณกดยืนยันแล้ว กำลังรอเพื่อนในห้องกดยืนยันให้ครบ...<br><b style="color: #6366f1; font-size: 1.25rem;">(ยืนยันแล้ว ${data.voted_count}/${data.total_count} คน)</b>`,
                    icon: 'info',
                    background: '#1e293b',
                    color: '#fff',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fas fa-home me-1"></i> กลับหน้าหลัก',
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

// Host Settings Modal: ขอเปลี่ยนระดับความยาก
function openHostSettings() {
    const modalHtml = document.getElementById('hostModal').innerHTML;
    Swal.fire({
        title: '⚙️ เปลี่ยนระดับความยาก',
        html: modalHtml,
        background: '#1e293b',
        color: '#fff',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check me-1"></i> ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#6366f1',
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
                        html: `ส่งคำขอเปลี่ยนระดับความยากแล้ว...<br>กำลังรอเพื่อนในห้องกดยืนยันให้ครบ<br><b style="color: #6366f1; font-size: 1.25rem;">(ยอมรับแล้ว ${data.voted_count}/${data.total_count} คน)</b>`,
                        icon: 'info',
                        background: '#1e293b',
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
                    background: '#1e293b',
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
        item.innerHTML = `<i class="fas fa-chevron-right" style="color: #6366f1; font-size: 0.7rem;"></i> ${msg}`;
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
