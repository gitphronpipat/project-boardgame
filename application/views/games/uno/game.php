<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <!-- Fonts & Icons & Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- iziToast -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Big Popup iziToast Custom Styles as requested */
        .big-popup {
            min-width: 500px !important;
            min-height: 200px !important;
            padding: 28px 36px !important;
            border-radius: 24px !important;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9) !important;
            border: 2px solid rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(20px) !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
        }

        .big-popup .iziToast-body {
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }

        .big-popup .iziToast-icon {
            font-size: 52px !important;
            height: 52px !important;
            margin: 0 0 16px 0 !important;
            display: block !important;
            float: none !important;
        }

        .big-popup .iziToast-texts {
            float: none !important;
            margin: 0 !important;
            text-align: center !important;
        }

        .big-popup .iziToast-title {
            font-size: 28px !important;
            line-height: 38px !important;
            display: block !important;
            margin-bottom: 8px !important;
            font-weight: 700 !important;
        }

        .big-popup .iziToast-message {
            font-size: 20px !important;
            line-height: 30px !important;
            display: block !important;
            color: #e2e8f0 !important;
        }

        .big-popup-win {
            border-color: rgba(52, 211, 153, 0.6) !important;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9), 0 0 45px rgba(52, 211, 153, 0.4) !important;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95)) !important;
        }

        .big-popup-lose {
            border-color: rgba(248, 113, 113, 0.6) !important;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9), 0 0 45px rgba(248, 113, 113, 0.4) !important;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.95), rgba(185, 28, 28, 0.95)) !important;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: radial-gradient(circle at center, #1b2838 0%, #0d131a 100%);
            min-height: 100vh;
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            user-select: none;
        }

        /* Header Navbar */
        .game-header {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .game-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .uno-badge-logo {
            font-family: 'Fredoka One', cursive;
            font-size: 1.8rem;
            color: #ff0037;
            text-shadow: 2px 2px 0 #ffd700, -2px -2px 0 #0051ff, 2px -2px 0 #00bf63;
            letter-spacing: 1px;
            transform: rotate(-5deg);
            display: inline-block;
        }

        /* Direction Pill */
        .direction-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 30px;
            padding: 5px 14px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dir-icon {
            display: inline-block;
            transition: transform 0.5s ease;
        }

        .dir-clockwise {
            animation: rotateClockwise 6s linear infinite;
        }

        .dir-counter {
            animation: rotateCounter 6s linear infinite;
        }

        @keyframes rotateClockwise {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes rotateCounter {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }

        /* Players Bar (Top Grid for up to 12 players) */
        .players-bar-wrapper {
            background: rgba(10, 15, 26, 0.6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 10px 16px;
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
        }

        .players-bar {
            display: inline-flex;
            gap: 12px;
            align-items: center;
            min-width: 100%;
            justify-content: center;
        }

        .player-badge-card {
            background: rgba(30, 41, 59, 0.7);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 6px 12px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .player-badge-card.active-turn {
            border-color: #ffd700;
            background: rgba(234, 179, 8, 0.15);
            box-shadow: 0 0 15px rgba(234, 179, 8, 0.4);
            transform: scale(1.05);
        }

        .player-badge-card.is-me {
            border-color: #38bdf8;
        }

        .p-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #fff;
            font-size: 0.9rem;
        }

        .p-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .p-name {
            font-size: 0.85rem;
            font-weight: 600;
            max-width: 90px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .p-cards-count {
            font-size: 0.75rem;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .uno-shout-tag {
            background: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 6px;
            animation: pulse 1s infinite alternate;
        }

        .btn-catch-uno {
            background: #dc2626;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 8px;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.8);
            animation: blinkCatch 0.8s infinite;
        }

        @keyframes blinkCatch {
            0% { transform: scale(0.95); opacity: 0.85; }
            50% { transform: scale(1.08); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.85; }
        }

        /* Main Game Table */
        .game-table-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }

        /* Action Message Banner */
        .action-banner {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 6px 18px;
            font-size: 0.95rem;
            color: #f1f5f9;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 90%;
        }

        /* Center Pile Area */
        .piles-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            margin-bottom: 30px;
            position: relative;
        }

        /* Draw Pile */
        .draw-pile-box {
            position: relative;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .draw-pile-box:hover {
            transform: translateY(-5px);
        }

        .draw-pile-box:active {
            transform: scale(0.96);
        }

        .pile-count-badge {
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            border: 1px solid #475569;
            color: #f8fafc;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
            white-space: nowrap;
            z-index: 5;
        }

        /* Active Color Glow Ring around Discard Pile */
        .discard-wrapper {
            position: relative;
            padding: 10px;
            border-radius: 20px;
            transition: all 0.4s ease;
        }

        .color-glow-red {
            box-shadow: 0 0 35px rgba(239, 68, 68, 0.75);
            border: 2px solid rgba(239, 68, 68, 0.8);
        }
        .color-glow-blue {
            box-shadow: 0 0 35px rgba(59, 130, 246, 0.75);
            border: 2px solid rgba(59, 130, 246, 0.8);
        }
        .color-glow-green {
            box-shadow: 0 0 35px rgba(34, 197, 94, 0.75);
            border: 2px solid rgba(34, 197, 94, 0.8);
        }
        .color-glow-yellow {
            box-shadow: 0 0 35px rgba(234, 179, 8, 0.75);
            border: 2px solid rgba(234, 179, 8, 0.8);
        }

        /* Active Color Label */
        .active-color-indicator {
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 10px;
            white-space: nowrap;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0,0,0,0.5);
            z-index: 5;
        }

        .active-col-red { background: #ef4444; color: #fff; }
        .active-col-blue { background: #3b82f6; color: #fff; }
        .active-col-green { background: #22c55e; color: #fff; }
        .active-col-yellow { background: #eab308; color: #000; }

        /* UNO Card Graphic Design */
        .uno-card {
            width: 96px;
            height: 142px;
            border-radius: 12px;
            border: 4px solid #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            display: inline-flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 6px;
            position: relative;
            overflow: hidden;
            user-select: none;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            flex-shrink: 0;
        }

        /* Card Colors */
        .uno-card.card-red { background: #e52521; }
        .uno-card.card-blue { background: #0956bf; }
        .uno-card.card-green { background: #2ba940; }
        .uno-card.card-yellow { background: #ffd400; color: #000; }
        .uno-card.card-wild { background: #18181b; }
        .uno-card.card-back {
            background: #111;
            border-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Card Center Oval */
        .card-center-oval {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 76px;
            height: 108px;
            background: #ffffff;
            border-radius: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.15);
            z-index: 1;
        }

        .card-wild .card-center-oval {
            background: conic-gradient(#e52521 0% 25%, #0956bf 25% 50%, #ffd400 50% 75%, #2ba940 75% 100%);
        }

        .card-back .card-center-oval {
            background: #e52521;
            transform: translate(-50%, -50%) rotate(-20deg);
            width: 72px;
            height: 98px;
        }

        .card-center-text {
            font-family: 'Fredoka One', cursive;
            font-size: 2.2rem;
            transform: rotate(25deg);
            z-index: 2;
            text-shadow: 2px 2px 0 rgba(0,0,0,0.25);
        }

        .card-red .card-center-text { color: #e52521; }
        .card-blue .card-center-text { color: #0956bf; }
        .card-green .card-center-text { color: #2ba940; }
        .card-yellow .card-center-text { color: #ffd400; text-shadow: 2px 2px 0 rgba(0,0,0,0.4); }
        .card-wild .card-center-text { color: #ffffff; text-shadow: 2px 2px 4px #000; font-size: 1.8rem; }

        .card-back-text {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: #ffd400;
            transform: rotate(20deg);
            text-shadow: 2px 2px 0 #000;
            letter-spacing: 1px;
        }

        /* Card Corner Marks */
        .card-corner {
            font-family: 'Fredoka One', cursive;
            font-size: 0.95rem;
            color: #ffffff;
            line-height: 1;
            z-index: 2;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.6);
        }

        .card-corner.bottom {
            align-self: flex-end;
            transform: rotate(180deg);
        }

        .card-yellow .card-corner {
            color: #1a1a1a;
            text-shadow: none;
        }

        /* Player Hands Area */
        .player-hand-container {
            background: rgba(15, 23, 42, 0.95);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 14px 20px 20px 20px;
            width: 100%;
            z-index: 50;
            box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.5);
        }

        .hand-controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
        }

        .cards-scroll-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            overflow-x: auto;
            padding: 15px 10px;
            scrollbar-width: thin;
        }

        .cards-flex {
            display: inline-flex;
            gap: -25px; /* Overlap cards slightly */
            padding: 0 15px;
            align-items: flex-end;
        }

        .cards-flex .uno-card {
            margin-left: -18px;
        }

        .cards-flex .uno-card:first-child {
            margin-left: 0;
        }

        .cards-flex .uno-card:hover {
            transform: translateY(-24px) scale(1.12);
            z-index: 20;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.9);
        }

        .uno-card.playable {
            border-color: #ffd700;
            cursor: pointer;
            animation: cardGlow 1.8s infinite alternate;
        }

        @keyframes cardGlow {
            from { box-shadow: 0 0 10px rgba(255, 215, 0, 0.5); }
            to { box-shadow: 0 0 22px rgba(255, 215, 0, 0.9); }
        }

        .uno-card.unplayable {
            opacity: 0.65;
            filter: grayscale(20%);
            cursor: not-allowed;
        }

        /* Buttons & Actions */
        .btn-uno-action {
            font-weight: 700;
            border-radius: 12px;
            padding: 8px 18px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-call-uno {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: #fff;
            border: 2px solid #f87171;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
            font-size: 1rem;
            animation: bounceUno 2s infinite;
        }

        .btn-call-uno:hover {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff;
            transform: scale(1.05);
        }

        @keyframes bounceUno {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-8px); }
            60% { transform: translateY(-4px); }
        }

        /* Color Picker Modal Options */
        .color-choice-btn {
            height: 90px;
            border-radius: 16px;
            border: 3px solid #ffffff;
            font-size: 1.3rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .color-choice-btn:hover {
            transform: scale(1.06);
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }

        .btn-choice-red { background: #e52521; }
        .btn-choice-blue { background: #0956bf; }
        .btn-choice-green { background: #2ba940; }
        .btn-choice-yellow { background: #ffd400; color: #1a1a1a; }

        @media (max-width: 768px) {
            .uno-card {
                width: 76px;
                height: 115px;
            }
            .card-center-oval {
                width: 60px;
                height: 85px;
            }
            .card-center-text {
                font-size: 1.6rem;
            }
            .big-popup {
                min-width: 90vw !important;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="game-header">
        <div class="game-brand">
            <span class="uno-badge-logo">UNO!</span>
            <span class="badge bg-secondary bg-opacity-50 text-light px-2 py-1 rounded-pill small">
                ห้อง: <strong id="header-room-id"><?= htmlspecialchars($room_id) ?></strong>
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- ทิศทางการเล่น -->
            <div class="direction-pill" id="direction-indicator" title="ทิศทางการเล่น">
                <i class="fas fa-sync-alt dir-icon dir-clockwise" id="direction-icon"></i>
                <span id="direction-text">ตามเข็มนาฬิกา</span>
            </div>

            <!-- สถิติผู้เล่น -->
            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1">
                    <i class="fas fa-trophy text-warning me-1"></i>ชนะ: <strong id="stat-wins"><?= $user_stats['wins'] ?></strong>
                </span>
                <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1">
                    <i class="fas fa-gamepad me-1"></i>เล่น: <strong id="stat-played"><?= $user_stats['played'] ?></strong>
                </span>
            </div>

            <!-- กติกา & ออกจากห้อง -->
            <button class="btn btn-sm btn-outline-light rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#unoRulesModal">
                <i class="fas fa-book-open me-1"></i>กติกา
            </button>
            <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="confirmLeaveGame()">
                <i class="fas fa-door-open me-1"></i>ออก
            </button>
        </div>
    </header>

    <!-- Bar แสดงรายชื่อผู้เล่นทั้งหมด (รองรับได้ถึง 12 คน) -->
    <div class="players-bar-wrapper">
        <div class="players-bar" id="players-list-bar">
            <div class="text-muted small"><i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดรายชื่อผู้เล่น...</div>
        </div>
    </div>

    <!-- โต๊ะกลางสำหรับเล่นการ์ด UNO -->
    <main class="game-table-container">

        <!-- ข้อความแจ้งเหตุการณ์ล่าสุดในเกม -->
        <div class="action-banner" id="action-banner">
            <i class="fas fa-info-circle text-info me-2"></i><span id="action-text">กำลังโหลดสถานะห้องเกม...</span>
        </div>

        <!-- กองจั่ว และ กองทิ้ง (Center Piles) -->
        <div class="piles-container">

            <!-- กองจั่ว (Draw Pile) -->
            <div class="draw-pile-box" onclick="onDrawCardClick()" title="คลิกเพื่อจั่วการ์ด 1 ใบ">
                <div class="uno-card card-back">
                    <div class="card-center-oval">
                        <div class="card-back-text">UNO</div>
                    </div>
                </div>
                <div class="pile-count-badge">
                    <i class="fas fa-layer-group me-1 text-warning"></i>กองจั่ว: <span id="draw-pile-count">--</span>
                </div>
            </div>

            <!-- กองทิ้ง (Discard Pile) -->
            <div class="discard-wrapper color-glow-red" id="discard-wrapper">
                <div class="active-color-indicator active-col-red" id="active-color-badge">
                    สีปัจจุบัน: สีแดง
                </div>
                <div id="top-card-container">
                    <!-- Top card injected here -->
                    <div class="uno-card card-red">
                        <div class="card-corner top">7</div>
                        <div class="card-center-oval">
                            <div class="card-center-text">7</div>
                        </div>
                        <div class="card-corner bottom">7</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- แจ้งเตือนสถานะตาเดิน (Turn Status Prompt) -->
        <div id="turn-prompt-container" class="mt-2 text-center">
            <h5 class="fw-bold" id="turn-prompt-text">
                <span class="spinner-border spinner-border-sm text-warning me-2"></span>กำลังเชื่อมต่อ...
            </h5>
        </div>

    </main>

    <!-- พื้นที่แสดงการ์ดบนมือของผู้เล่น (Player Hand Deck) -->
    <footer class="player-hand-container">
        <div class="hand-controls-bar">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold" style="font-size: 0.9rem;">
                    <i class="fas fa-hand-paper me-1"></i>การ์ดในมือคุณ: <span id="my-card-count">0</span> ใบ
                </span>
                <button class="btn btn-sm btn-outline-warning rounded-pill px-3" id="btn-pass-turn" style="display:none;" onclick="passTurn()">
                    <i class="fas fa-forward me-1"></i>ผ่านตา (Pass)
                </button>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- ปุ่มพูด UNO! -->
                <button class="btn btn-call-uno btn-uno-action" id="btn-call-uno" onclick="callUno()">
                    <i class="fas fa-bullhorn me-1"></i>พูดว่า "UNO!"
                </button>
            </div>
        </div>

        <!-- แถบเลื่อนการ์ดในมือ -->
        <div class="cards-scroll-wrapper">
            <div class="cards-flex" id="my-cards-deck">
                <!-- User's cards injected here -->
            </div>
        </div>
    </footer>

    <!-- Modal เลือกสีเมื่อลงการ์ด Wild หรือ Wild Draw 4 -->
    <div class="modal fade" id="colorPickerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-white" style="background: rgba(15, 23, 42, 0.98); border: 2px solid rgba(255,255,255,0.2); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.8);">
                <div class="modal-header border-0 pb-0 justify-content-center">
                    <h4 class="modal-title fw-bold text-center">
                        <i class="fas fa-palette text-warning me-2"></i>เลือกสีที่ต้องการเปลี่ยน
                    </h4>
                </div>
                <div class="modal-body p-4">
                    <p class="text-center text-secondary small mb-4">คุณได้ลงการ์ดพิเศษเปลี่ยนสี กรุณาเลือกสีถัดไปสำหรับผู้เล่นคนต่อไป</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <button class="w-100 color-choice-btn btn-choice-red" onclick="selectWildColor('red')">
                                <i class="fas fa-circle"></i> สีแดง
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="w-100 color-choice-btn btn-choice-blue" onclick="selectWildColor('blue')">
                                <i class="fas fa-circle"></i> สีน้ำเงิน
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="w-100 color-choice-btn btn-choice-green" onclick="selectWildColor('green')">
                                <i class="fas fa-circle"></i> สีเขียว
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="w-100 color-choice-btn btn-choice-yellow" onclick="selectWildColor('yellow')">
                                <i class="fas fa-circle"></i> สีเหลือง
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สรุปผลตอนจบเกม (Game Over Modal) -->
    <div class="modal fade" id="gameOverModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content text-white" style="background: rgba(15, 23, 42, 0.98); border: 2px solid rgba(255,255,255,0.2); border-radius: 28px; box-shadow: 0 25px 70px rgba(0,0,0,0.9);">
                <div class="modal-body text-center p-5">
                    <div id="game-over-icon" class="mb-3" style="font-size: 64px;">🏆</div>
                    <h2 class="fw-bold mb-2" id="game-over-title">เกมสิ้นสุด!</h2>
                    <p class="text-light mb-4" id="game-over-desc">ผู้ชนะคือ...</p>

                    <!-- ตารางแต้มคะแนนของผู้เล่นแต่ละคนในรอบนี้ -->
                    <div class="table-responsive mb-4">
                        <table class="table table-dark table-striped table-hover rounded-4 overflow-hidden border border-secondary border-opacity-25">
                            <thead>
                                <tr class="text-secondary small">
                                    <th>ผู้เล่น</th>
                                    <th>แต้มการ์ดที่เหลือ</th>
                                    <th>ผลการแข่งขัน</th>
                                </tr>
                            </thead>
                            <tbody id="scores-table-body">
                                <!-- Scores injected -->
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold" id="btn-rematch" onclick="requestRematch()">
                            <i class="fas fa-redo me-2"></i>เล่นอีกครั้ง (Rematch)
                        </button>
                        <a href="<?= base_url('player') ?>" class="btn btn-outline-light rounded-pill px-4 py-2">
                            <i class="fas fa-home me-2"></i>กลับหน้าล็อบบี้
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal กติกาการเล่น UNO สากล -->
    <div class="modal fade" id="unoRulesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content text-white" style="background: rgba(15, 23, 42, 0.96); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px;">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title fw-bold"><i class="fas fa-book-open text-warning me-2"></i>กติกาสากล UNO Online (2-12 คน)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <h6><strong>1. เป้าหมายของเกม</strong></h6>
                    <p class="text-secondary">ผู้เล่นที่ทิ้งการ์ดในมือได้หมดเป็นคนแรกจะเป็นผู้ชนะในรอบนั้น และจะได้รับคะแนนสะสมจากแต้มการ์ดที่เหลือในมือของผู้เล่นคนอื่นๆ ทั้งหมด</p>
                    
                    <h6><strong>2. วิธีลงการ์ด</strong></h6>
                    <p class="text-secondary">ต้องลงการ์ดที่มี <strong>สีตรงกัน</strong> หรือ <strong>ตัวเลข/สัญลักษณ์ตรงกัน</strong> กับการ์ดใบบนสุดของกองทิ้ง หรือลงการ์ดพิเศษ <strong>Wild</strong> ได้ทุกเมื่อ</p>
                    
                    <h6><strong>3. หากไม่มีการ์ดที่ลงได้</strong></h6>
                    <p class="text-secondary">ต้องจั่วการ์ด 1 ใบจากกองจั่ว หากการ์ดที่จั่วมาลงได้ สามารถเลือกลงได้ทันที หรือเลือกกด "ผ่านตา"</p>

                    <h6><strong>4. การ์ดแอ็กชันพิเศษ</strong></h6>
                    <ul class="text-secondary">
                        <li><strong>ข้ามตา (Skip):</strong> ข้ามตาของผู้เล่นคนถัดไป</li>
                        <li><strong>ย้อนกลับ (Reverse):</strong> สลับทิศทางการวนรอบ (หากเล่น 2 คน จะทำหน้าที่เหมือน Skip)</li>
                        <li><strong>จั่ว 2 ใบ (+2 Draw Two):</strong> ผู้เล่นคนถัดไปถูกบังคับจั่ว 2 ใบ และข้ามตา</li>
                        <li><strong>เปลี่ยนสี (Wild):</strong> เลือกเปลี่ยนสีของกองทิ้งเป็นสีใดก็ได้</li>
                        <li><strong>จั่ว 4 ใบ (+4 Wild Draw Four):</strong> เลือกเปลี่ยนสี และผู้เล่นคนถัดไปต้องจั่ว 4 ใบพร้อมข้ามตา</li>
                    </ul>

                    <h6><strong>5. กฎ "UNO!"</strong></h6>
                    <p class="text-secondary">เมื่อคุณลงการ์ดจนเหลือการ์ดในมือเพียง <strong>1 ใบ</strong> คุณต้องกดปุ่ม <strong>พูดว่า "UNO!"</strong> หากลืมพูดและมีผู้เล่นคนอื่นกดจับได้ก่อนเริ่มตาถัดไป คุณจะถูกลงโทษจั่ว 2 ใบ!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Web Audio API Synthesized Sound Effects (No external dependencies needed) -->
    <script>
        const AudioFX = {
            ctx: null,
            init() {
                if (!this.ctx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) this.ctx = new AudioContext();
                }
            },
            playTone(freq, type, duration, delay = 0) {
                try {
                    this.init();
                    if (!this.ctx) return;
                    setTimeout(() => {
                        const osc = this.ctx.createOscillator();
                        const gain = this.ctx.createGain();
                        osc.type = type;
                        osc.frequency.setValueAtTime(freq, this.ctx.currentTime);
                        gain.gain.setValueAtTime(0.2, this.ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + duration);
                        osc.connect(gain);
                        gain.connect(this.ctx.destination);
                        osc.start();
                        osc.stop(this.ctx.currentTime + duration);
                    }, delay * 1000);
                } catch(e) {}
            },
            playCard() {
                this.playTone(480, 'sine', 0.1);
            },
            drawCard() {
                this.playTone(320, 'triangle', 0.12);
            },
            unoShout() {
                this.playTone(523.25, 'triangle', 0.15);
                this.playTone(659.25, 'triangle', 0.15, 0.12);
                this.playTone(783.99, 'sine', 0.25, 0.24);
            },
            victory() {
                this.playTone(523.25, 'sine', 0.2, 0);
                this.playTone(659.25, 'sine', 0.2, 0.15);
                this.playTone(783.99, 'sine', 0.2, 0.3);
                this.playTone(1046.50, 'sine', 0.4, 0.45);
            },
            defeat() {
                this.playTone(400, 'sawtooth', 0.3, 0);
                this.playTone(300, 'sawtooth', 0.4, 0.25);
            }
        };
    </script>

    <!-- Game Client Script -->
    <script>
        const ROOM_ID = '<?= addslashes($room_id) ?>';
        const MY_USERNAME = '<?= addslashes($username) ?>';
        const BASE_URL = '<?= rtrim(base_url(), '/') ?>/';

        let gameState = null;
        let pollTimer = null;
        let selectedCardForWild = null;
        let isFetching = false;
        let closedFailCount = 0;
        let isSubmitting = false;
        let hasShownGameOver = false;

        // เริ่มต้น Polling ซิงค์สถานะเกมทุก 1.5 วินาที
        $(document).ready(function() {
            fetchGameState();
            pollTimer = setInterval(fetchGameState, 1500);
        });

        // ดึงสถานะปัจจุบันของเกม
        function fetchGameState() {
            if (isFetching) return;
            isFetching = true;

            $.get(BASE_URL + 'uno/get_state/' + ROOM_ID, function(res) {
                if (!res) return;

                if (res.status === 'syncing') {
                    // กำลังเตรียมห้องเกมหรือเชื่อมต่อ ให้รอรอบถัดไป
                    return;
                }

                if (res.status === 'closed') {
                    closedFailCount++;
                    // ต้องยืนยันว่าห้องปิดจริงติดต่อกัน 4 ครั้ง (ประมาณ 6 วินาที) เพื่อป้องกันปัญหาเน็ตสะดุดชั่วคราว
                    if (closedFailCount >= 4) {
                        if (pollTimer) clearInterval(pollTimer);
                        iziToast.error({
                            title: 'ห้องเกมถูกปิด',
                            message: res.message || 'ห้องเกมนี้ถูกปิดหรือผู้เล่นออกหมดแล้ว',
                            position: 'center',
                            timeout: 3000,
                            onClosed: function() {
                                window.location.href = BASE_URL + 'player';
                            }
                        });
                    }
                    return;
                }

                // ได้รับสถานะห้องปกติ ให้รีเซ็ตตัวนับ
                closedFailCount = 0;

                gameState = res;
                renderGame(res);
            }, 'json').always(function() {
                isFetching = false;
            });
        }

        // Render หน้าจอทั้งหมดจาก gameState
        function renderGame(state) {
            // 1. ทิศทางการเล่น
            const isClockwise = (state.direction === 1);
            $('#direction-text').text(isClockwise ? 'ตามเข็มนาฬิกา' : 'ทวนเข็มนาฬิกา');
            $('#direction-icon')
                .removeClass('dir-clockwise dir-counter')
                .addClass(isClockwise ? 'dir-clockwise' : 'dir-counter');

            // 2. แถบรายชื่อผู้เล่นด้านบน
            renderPlayersBar(state.players, state.current_player);

            // 3. กองจั่ว
            $('#draw-pile-count').text(state.draw_pile_count || 0);

            // 4. กองทิ้ง และสีปัจจุบัน
            renderDiscardPile(state.top_card, state.active_color);

            // 5. แบนเนอร์การเคลื่อนไหวล่าสุด
            $('#action-text').text(state.last_action || 'กำลังเล่นเกม');

            // 6. สถานะตาเดิน
            renderTurnPrompt(state);

            // 7. การ์ดในมือของผู้เล่น
            renderMyHand(state.my_hand, state.is_my_turn, state.active_color, state.top_card);

            // 8. ปุ่มผ่านตา (แสดงเมื่อเป็นตาเราและกดจั่วไปแล้ว)
            if (state.is_my_turn && state.turn_has_drawn) {
                $('#btn-pass-turn').show();
            } else {
                $('#btn-pass-turn').hide();
            }

            // 9. ตรวจสอบการจบเกม
            if (state.status === 'finished' && !hasShownGameOver) {
                hasShownGameOver = true;
                showGameOver(state);
            } else if (state.status === 'playing') {
                hasShownGameOver = false;
                $('#gameOverModal').modal('hide');
            }
        }

        // Render แถบผู้เล่นด้านบน (รองรับสูงสุด 12 คน)
        function renderPlayersBar(players, currentPlayer) {
            if (!players || players.length === 0) return;

            let html = '';
            players.forEach(function(p) {
                const isTurn = (p.username === currentPlayer);
                const isMe = (p.username === MY_USERNAME);
                const showCatchBtn = (!isMe && p.card_count === 1 && !p.called_uno);

                html += `
                <div class="player-badge-card ${isTurn ? 'active-turn' : ''} ${isMe ? 'is-me' : ''}">
                    <div class="p-avatar">${escapeHtml(p.username.charAt(0).toUpperCase())}</div>
                    <div class="p-info">
                        <div class="d-flex align-items-center gap-1">
                            <span class="p-name" title="${escapeHtml(p.username)}">${escapeHtml(p.username)}</span>
                            ${isMe ? '<span class="badge bg-info" style="font-size:0.6rem;">คุณ</span>' : ''}
                            ${p.is_host ? '<i class="fas fa-crown text-warning" style="font-size:0.7rem;" title="Host"></i>' : ''}
                        </div>
                        <div class="p-cards-count">
                            <i class="fas fa-clone text-muted"></i> <strong>${p.card_count}</strong> ใบ
                            ${p.called_uno ? '<span class="uno-shout-tag">UNO!</span>' : ''}
                        </div>
                    </div>
                    ${showCatchBtn ? `<button class="btn-catch-uno" onclick="catchUno('${escapeHtml(p.username)}')"><i class="fas fa-crosshairs me-1"></i>จับ UNO!</button>` : ''}
                </div>`;
            });

            $('#players-list-bar').html(html);
        }

        // Render กองทิ้งและการ์ดบนสุด
        function renderDiscardPile(topCard, activeColor) {
            if (!topCard) return;

            // อัปเดตสีกรอบที่เรืองแสง
            const colorGlowClass = 'color-glow-' + (activeColor || topCard.color);
            $('#discard-wrapper')
                .removeClass('color-glow-red color-glow-blue color-glow-green color-glow-yellow')
                .addClass(colorGlowClass);

            // ป้ายบอกสีปัจจุบัน
            const colorNames = {
                'red': 'สีแดง',
                'blue': 'สีน้ำเงิน',
                'green': 'สีเขียว',
                'yellow': 'สีเหลือง'
            };
            const activeColClass = 'active-col-' + activeColor;
            $('#active-color-badge')
                .removeClass('active-col-red active-col-blue active-col-green active-col-yellow')
                .addClass(activeColClass)
                .html(`<i class="fas fa-palette me-1"></i>สีปัจจุบัน: ${colorNames[activeColor] || activeColor}`);

            // Render Card HTML
            $('#top-card-container').html(buildCardHtml(topCard, false));
        }

        // สร้าง HTML Card
        function buildCardHtml(card, isPlayable, index = 0) {
            const valDisplay = getCardValueSymbol(card.value);
            const cornerDisplay = getCardCornerSymbol(card.value);
            const colorClass = 'card-' + card.color;
            const playableClass = isPlayable ? 'playable' : 'unplayable';

            return `
            <div class="uno-card ${colorClass} ${playableClass}" 
                 data-card-id="${card.id}" 
                 data-card-color="${card.color}" 
                 data-card-value="${card.value}" 
                 ${isPlayable ? `onclick="onPlayCardClick('${card.id}', '${card.color}')"` : ''}>
                <div class="card-corner top">${cornerDisplay}</div>
                <div class="card-center-oval">
                    <div class="card-center-text">${valDisplay}</div>
                </div>
                <div class="card-corner bottom">${cornerDisplay}</div>
            </div>`;
        }

        function getCardValueSymbol(val) {
            if (val === 'skip') return '<i class="fas fa-ban"></i>';
            if (val === 'reverse') return '<i class="fas fa-arrows-spin"></i>';
            if (val === 'draw2') return '+2';
            if (val === 'wild') return 'W';
            if (val === 'wild4') return '+4';
            return val;
        }

        function getCardCornerSymbol(val) {
            if (val === 'skip') return '⊘';
            if (val === 'reverse') return '⇄';
            if (val === 'draw2') return '+2';
            if (val === 'wild') return '★';
            if (val === 'wild4') return '+4';
            return val;
        }

        // Render สถานะตาเดิน
        function renderTurnPrompt(state) {
            if (state.status === 'finished') {
                $('#turn-prompt-text').html('<span class="text-success"><i class="fas fa-trophy me-2"></i>เกมจบแล้ว!</span>');
                return;
            }

            if (state.is_my_turn) {
                if (state.turn_has_drawn) {
                    $('#turn-prompt-text').html('<span class="text-warning"><i class="fas fa-bolt me-2"></i>ตาของคุณ: คุณจั่วไพ่แล้ว สามารถเลือกลงไพ่ หรือกด "ผ่านตา"</span>');
                } else {
                    $('#turn-prompt-text').html('<span class="text-success animate__animated animate__pulse animate__infinite"><i class="fas fa-hand-point-right me-2"></i>ตาของคุณแล้ว! คลิกการ์ดในมือเพื่อลง หรือคลิกกองจั่ว</span>');
                }
            } else {
                $('#turn-prompt-text').html(`<span class="text-light opacity-75"><i class="fas fa-hourglass-half fa-spin me-2 text-primary"></i>กำลังรอตาของ <strong>${escapeHtml(state.current_player)}</strong>...</span>`);
            }
        }

        // Render การ์ดในมือคุณ
        function renderMyHand(hand, isMyTurn, activeColor, topCard) {
            $('#my-card-count').text(hand ? hand.length : 0);

            if (!hand || hand.length === 0) {
                $('#my-cards-deck').html('<div class="text-muted p-3">ไม่มีการ์ดในมือ</div>');
                return;
            }

            let html = '';
            hand.forEach(function(card, idx) {
                // ตรวจสอบว่าการ์ดนี้สามารถลงได้หรือไม่
                let isPlayable = false;
                if (isMyTurn && topCard) {
                    if (card.color === 'wild') {
                        isPlayable = true; // Wild / Wild4 ลงได้เสมอ
                    } else if (card.color === activeColor) {
                        isPlayable = true;
                    } else if (card.value === topCard.value) {
                        isPlayable = true;
                    }
                }

                html += buildCardHtml(card, isPlayable, idx);
            });

            $('#my-cards-deck').html(html);
        }

        // เมื่อผู้เล่นคลิกการ์ดในมือเพื่อลง
        function onPlayCardClick(cardId, cardColor) {
            if (!gameState || !gameState.is_my_turn || isSubmitting) return;

            // หากเป็นการ์ด Wild หรือ Wild Draw 4 ให้เปิด Modal เลือกสี
            if (cardColor === 'wild') {
                selectedCardForWild = cardId;
                $('#colorPickerModal').modal('show');
                return;
            }

            // ลงการ์ดสีปกติ
            executePlayCard(cardId, null);
        }

        // เมื่อเลือกสีจากการ์ด Wild
        function selectWildColor(color) {
            $('#colorPickerModal').modal('hide');
            if (!selectedCardForWild) return;
            executePlayCard(selectedCardForWild, color);
            selectedCardForWild = null;
        }

        // ส่งคำสั่งลงการ์ดไปยัง Server
        function executePlayCard(cardId, chosenColor) {
            isSubmitting = true;
            AudioFX.playCard();

            $.post(BASE_URL + 'uno/play_card/' + ROOM_ID, {
                card_id: cardId,
                chosen_color: chosenColor
            }, function(res) {
                isSubmitting = false;
                if (res && res.status === 'ok') {
                    fetchGameState();
                } else {
                    iziToast.warning({
                        title: 'ไม่สามารถลงได้',
                        message: res.message || 'การ์ดนี้ไม่ตรงกับกติกา',
                        position: 'topCenter',
                        timeout: 2500
                    });
                }
            }, 'json').fail(function() {
                isSubmitting = false;
            });
        }

        // เมื่อคลิกที่กองจั่ว
        function onDrawCardClick() {
            if (!gameState || !gameState.is_my_turn || isSubmitting) return;

            if (gameState.turn_has_drawn) {
                iziToast.info({
                    title: 'จั่วการ์ดไปแล้ว',
                    message: 'คุณจั่วการ์ดในตานี้ไปแล้ว ให้เลือกลงการ์ด หรือกดปุ่ม "ผ่านตา"',
                    position: 'topCenter',
                    timeout: 2500
                });
                return;
            }

            isSubmitting = true;
            AudioFX.drawCard();

            $.post(BASE_URL + 'uno/draw_card/' + ROOM_ID, function(res) {
                isSubmitting = false;
                if (res && res.status === 'ok') {
                    iziToast.success({
                        title: 'จั่วสำเร็จ',
                        message: 'คุณได้รับการ์ดใหม่ 1 ใบ',
                        position: 'topCenter',
                        timeout: 2000
                    });
                    fetchGameState();
                } else {
                    iziToast.error({
                        title: 'เกิดข้อผิดพลาด',
                        message: res.message || 'ไม่สามารถจั่วการ์ดได้',
                        position: 'topCenter'
                    });
                }
            }, 'json').fail(function() {
                isSubmitting = false;
            });
        }

        // ผ่านตา (Pass Turn)
        function passTurn() {
            if (!gameState || !gameState.is_my_turn || isSubmitting) return;

            isSubmitting = true;
            $.post(BASE_URL + 'uno/pass_turn/' + ROOM_ID, function(res) {
                isSubmitting = false;
                if (res && res.status === 'ok') {
                    fetchGameState();
                }
            }, 'json').fail(function() {
                isSubmitting = false;
            });
        }

        // พูด UNO!
        function callUno() {
            AudioFX.unoShout();
            $.post(BASE_URL + 'uno/call_uno/' + ROOM_ID, function(res) {
                if (res && res.status === 'ok') {
                    iziToast.success({
                        title: '🔥 UNO!',
                        message: 'คุณได้พูด UNO เรียบร้อยแล้ว!',
                        position: 'topCenter',
                        timeout: 2500
                    });
                    fetchGameState();
                } else {
                    iziToast.info({
                        title: 'แจ้งเตือน',
                        message: res.message || 'คุณพูด UNO ได้เมื่อมีไพ่เหลือ 1 หรือ 2 ใบเท่านั้น',
                        position: 'topCenter',
                        timeout: 2500
                    });
                }
            }, 'json');
        }

        // จับคนลืมพูด UNO!
        function catchUno(targetUser) {
            $.post(BASE_URL + 'uno/catch_uno/' + ROOM_ID, {
                target_user: targetUser
            }, function(res) {
                if (res && res.status === 'ok') {
                    iziToast.success({
                        title: '🎯 จับสำเร็จ!',
                        message: res.message,
                        position: 'center',
                        timeout: 3000
                    });
                    fetchGameState();
                } else {
                    iziToast.warning({
                        title: 'ไม่สำเร็จ',
                        message: res.message,
                        position: 'topCenter',
                        timeout: 2000
                    });
                }
            }, 'json');
        }

        // ขอเล่นใหม่อีกครั้ง (Rematch)
        function requestRematch() {
            $('#btn-rematch').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังเริ่มใหม่...');
            $.post(BASE_URL + 'uno/rematch/' + ROOM_ID, function(res) {
                $('#btn-rematch').prop('disabled', false).html('<i class="fas fa-redo me-2"></i>เล่นอีกครั้ง (Rematch)');
                if (res && res.status === 'ok') {
                    $('#gameOverModal').modal('hide');
                    hasShownGameOver = false;
                    fetchGameState();
                }
            }, 'json');
        }

        // แสดงผลลัพธ์การจบเกม (ตามที่ผู้ใช้ร้องขอ: popup iziToast ขนาดใหญ่ตรงกลาง .big-popup)
        function showGameOver(state) {
            const isWinner = (state.winner === MY_USERNAME);

            if (isWinner) {
                AudioFX.victory();
                iziToast.show({
                    theme: 'dark',
                    class: 'big-popup big-popup-win',
                    icon: 'fas fa-trophy',
                    title: '🎉 ชนะแล้ว!',
                    message: `ยินดีด้วย คุณชนะเกม UNO รอบนี้! ได้รับแต้มรวม +${state.round_points || 0} คะแนน`,
                    position: 'center',
                    timeout: 4500,
                    progressBar: false,
                    animateInside: true,
                    onClosed: function() {
                        openGameOverModal(state);
                    }
                });
            } else {
                AudioFX.defeat();
                iziToast.show({
                    theme: 'dark',
                    class: 'big-popup big-popup-lose',
                    icon: 'fas fa-crown',
                    title: '🏆 ' + escapeHtml(state.winner) + ' ชนะการแข่งขัน!',
                    message: `ผู้เล่น ${escapeHtml(state.winner)} ทิ้งการ์ดหมดมือและคว้าชัยชนะในรอบนี้`,
                    position: 'center',
                    timeout: 4500,
                    progressBar: false,
                    animateInside: true,
                    onClosed: function() {
                        openGameOverModal(state);
                    }
                });
            }
        }

        // เปิด Modal สรุปผลคะแนน
        function openGameOverModal(state) {
            const isWinner = (state.winner === MY_USERNAME);
            $('#game-over-icon').text(isWinner ? '🏆' : '👑');
            $('#game-over-title').text(isWinner ? 'ยินดีด้วย คุณคือผู้ชนะ!' : `${state.winner} เป็นผู้ชนะ!`);
            $('#game-over-desc').text(`ผู้ชนะได้รับคะแนนรวม ${state.round_points || 0} แต้ม จากไพ่ที่เหลือในมือผู้เล่นทุกคน`);

            let scoresHtml = '';
            if (state.scores) {
                for (let p in state.scores) {
                    const isPWin = (p === state.winner);
                    scoresHtml += `
                    <tr>
                        <td class="text-start ps-4">
                            <strong>${escapeHtml(p)}</strong> ${p === MY_USERNAME ? '<span class="badge bg-primary ms-1">คุณ</span>' : ''}
                        </td>
                        <td>${state.scores[p]} แต้ม</td>
                        <td>
                            ${isPWin ? '<span class="badge bg-success"><i class="fas fa-trophy me-1"></i>ผู้ชนะ</span>' : '<span class="badge bg-secondary">แพ้</span>'}
                        </td>
                    </tr>`;
                }
            }
            $('#scores-table-body').html(scoresHtml);
            $('#gameOverModal').modal('show');
        }

        // ยืนยันออกจากเกม
        function confirmLeaveGame() {
            if (confirm('คุณต้องการออกจากห้องเกม UNO นี้ใช่หรือไม่?')) {
                clearInterval(pollTimer);
                window.location.href = BASE_URL + 'uno/leave/' + ROOM_ID;
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return $('<div>').text(text).html();
        }
    </script>
</body>
</html>
