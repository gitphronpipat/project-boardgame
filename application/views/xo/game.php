<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Fonts & Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .game-topbar {
            background: rgba(26, 26, 46, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 10px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Game Arena */
        .game-container {
            max-width: 520px;
            width: 100%;
            margin: 1.5rem auto;
            padding: 0 1rem;
            text-align: center;
        }

        /* Scoreboard */
        .scoreboard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 14px 20px;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }
        .player-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s;
            position: relative;
        }
        .player-card.active-turn {
            background: rgba(129, 140, 248, 0.2);
            border: 1px solid rgba(129, 140, 248, 0.5);
            box-shadow: 0 0 16px rgba(129, 140, 248, 0.35);
        }
        .player-card .symbol-badge {
            font-family: 'Fredoka One', cursive;
            font-size: 28px;
            line-height: 1;
        }
        .player-card.player-x .symbol-badge { color: #818cf8; text-shadow: 0 0 12px rgba(129, 140, 248, 0.8); }
        .player-card.player-o .symbol-badge { color: #38bdf8; text-shadow: 0 0 12px rgba(56, 189, 248, 0.8); }
        .player-name {
            font-weight: 600;
            font-size: 14px;
            margin-top: 4px;
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .player-score {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }
        .vs-badge {
            font-size: 16px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 1px;
        }

        /* Status Banner */
        .status-banner {
            font-size: 15px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            margin-bottom: 1.2rem;
            display: inline-block;
        }

        /* 3x3 Board */
        .xo-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            background: rgba(255, 255, 255, 0.04);
            padding: 16px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4);
            aspect-ratio: 1 / 1;
            margin-bottom: 1.5rem;
        }
        .xo-cell {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Fredoka One', cursive;
            font-size: 3.5rem;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .xo-cell:hover:not(.occupied) {
            background: rgba(129, 140, 248, 0.15);
            border-color: rgba(129, 140, 248, 0.4);
            transform: scale(0.97);
        }
        .xo-cell.cell-x {
            color: #818cf8;
            text-shadow: 0 0 20px rgba(129, 140, 248, 0.8);
            cursor: not-allowed;
        }
        .xo-cell.cell-o {
            color: #38bdf8;
            text-shadow: 0 0 20px rgba(56, 189, 248, 0.8);
            cursor: not-allowed;
        }
        .xo-cell.occupied {
            cursor: not-allowed;
        }

        /* Controls */
        .game-controls {
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        .btn-rematch {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
            border: none;
            color: #fff;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-rematch:hover {
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            color: #fff;
        }
        .btn-leave {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #e0e0e0;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-leave:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }
    </style>
</head>
<body>

<!-- Unified Dynamic Navbar -->
<?php $this->load->view('player/components/navbar', [
    'nav_mode'   => 'in_game',
    'game_name'  => 'OX (Tic Tac Toe)',
    'room_id'    => $room_id,
    'leave_url'  => base_url('xo/leave/' . $room_id),
    'user_stats' => isset($user_stats) ? $user_stats : null,
]); ?>

<!-- Game Arena -->
<div class="game-container">

    <!-- Scoreboard -->
    <div class="scoreboard">
        <!-- Player X (Host) -->
        <div class="player-card player-x" id="card-player-x">
            <span class="symbol-badge">X</span>
            <span class="player-name" id="name-player-x"><?= htmlspecialchars($room['player_x'] ?: 'ผู้เล่น X') ?></span>
            <span class="player-score" id="score-player-x"><?= isset($room['score_x']) ? $room['score_x'] : 0 ?></span>
            <div class="player-stats-mini mt-1" id="stats-player-x" style="font-size: 11px; opacity: 0.85;"></div>
        </div>

        <div class="vs-badge">VS</div>

        <!-- Player O (Guest) -->
        <div class="player-card player-o" id="card-player-o">
            <span class="symbol-badge">O</span>
            <span class="player-name" id="name-player-o"><?= htmlspecialchars($room['player_o'] ?: 'รอเพื่อน...') ?></span>
            <span class="player-score" id="score-player-o"><?= isset($room['score_o']) ? $room['score_o'] : 0 ?></span>
            <div class="player-stats-mini mt-1" id="stats-player-o" style="font-size: 11px; opacity: 0.85;"></div>
        </div>
    </div>

    <!-- Status Message -->
    <div class="status-banner" id="game-status-text">
        <i class="fas fa-spinner fa-spin me-1 text-primary"></i> กำลังเชื่อมต่อห้องเกม...
    </div>

    <!-- Waiting Invite Helper (shows when waiting for opponent) -->
    <div id="invite-helper-box" class="mb-3" style="display: none;">
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inviteOnlineFriendsModal" onclick="loadXoOnlineFriends()">
            <i class="fas fa-paper-plane me-1"></i>เชิญเพื่อนที่ออนไลน์เข้าเล่นห้องนี้
        </button>
    </div>

    <!-- 3x3 XO Board -->
    <div class="xo-board" id="xo-board">
        <?php for ($i = 0; $i < 9; $i++): ?>
            <div class="xo-cell" data-index="<?= $i ?>" onclick="cellClicked(<?= $i ?>)"></div>
        <?php endfor; ?>
    </div>

    <!-- Controls -->
    <div class="game-controls">
        <button class="btn-rematch" id="btn-rematch" onclick="requestRematch()" style="display: none;">
            <i class="fas fa-redo me-1"></i> เล่นอีกครั้ง
        </button>
        <a href="<?= base_url('xo/leave/' . $room_id) ?>" class="btn-leave">
            <i class="fas fa-sign-out-alt me-1"></i> ออกจากเกม
        </a>
    </div>

</div>

<!-- Modal: เชิญเพื่อนเข้าห้อง XO นี้ -->
<div class="modal fade modal-dark" id="inviteOnlineFriendsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-paper-plane me-2 text-primary"></i>เชิญเพื่อนเข้าห้องเล่น OX</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="xo-online-friends-list">
                    <div class="text-center py-4 text-muted">กำลังโหลดรายชื่อเพื่อนที่ออนไลน์...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Widgets (Presence, Invites, Chat) -->
<?php $this->load->view('player/global_widget'); ?>

<script>
    const ROOM_ID = '<?= $room_id ?>';
    let mySymbol = '';
    let currentTurn = 'X';
    let gameStatus = 'waiting';
    let pollInterval = null;

    function fetchGameState() {
        $.get('<?= base_url('xo/get_state/') ?>' + ROOM_ID, function(room) {
            if (!room) return;

            // หากห้องถูกลบ (เช่น อีกฝ่ายกดออกจากห้องไปแล้ว)
            if (room.error === 'not_found') {
                if (pollInterval) clearInterval(pollInterval);
                alert('ห้องเกมนี้ถูกปิด หรือผู้เล่นอีกฝ่ายได้ออกจากห้องแล้ว');
                window.location.href = '<?= base_url('player') ?>';
                return;
            }

            // ตรวจสอบสัญลักษณ์ของเรา
            if (room.player_x === CURRENT_USER) mySymbol = 'X';
            if (room.player_o === CURRENT_USER) mySymbol = 'O';

            currentTurn = room.turn || 'X';
            gameStatus = room.status || 'waiting';

            // อัปเดตรายชื่อและคะแนน
            $('#name-player-x').text(room.player_x || 'ผู้เล่น X');
            $('#name-player-o').text(room.player_o || 'รอเพื่อน...');
            $('#score-player-x').text(room.score_x || 0);
            $('#score-player-o').text(room.score_o || 0);

            // อัปเดตสถิติ ชนะ / เล่น ของผู้เล่น X และ O
            if (room.stats_x) {
                $('#stats-player-x').html('<span class="text-success"><i class="fas fa-trophy me-1"></i>ชนะ ' + room.stats_x.wins + '</span> | <span class="text-info"><i class="fas fa-gamepad me-1"></i>เล่น ' + room.stats_x.played + '</span>');
            }
            if (room.stats_o && room.player_o) {
                $('#stats-player-o').html('<span class="text-success"><i class="fas fa-trophy me-1"></i>ชนะ ' + room.stats_o.wins + '</span> | <span class="text-info"><i class="fas fa-gamepad me-1"></i>เล่น ' + room.stats_o.played + '</span>');
            } else {
                $('#stats-player-o').empty();
            }

            // ไฮไลท์การ์ดคนที่มีเทิร์น
            if (gameStatus === 'playing') {
                if (currentTurn === 'X') {
                    $('#card-player-x').addClass('active-turn');
                    $('#card-player-o').removeClass('active-turn');
                } else {
                    $('#card-player-o').addClass('active-turn');
                    $('#card-player-x').removeClass('active-turn');
                }
            } else {
                $('.player-card').removeClass('active-turn');
            }

            // อัปเดตข้อความสถานะ
            if (gameStatus === 'waiting') {
                $('#game-status-text').html('<i class="fas fa-user-clock me-1 text-warning"></i> กำลังรอผู้เล่นอีกคนเข้าร่วม...');
                $('#invite-helper-box').show();
                $('#btn-rematch').hide();
            } else if (gameStatus === 'playing') {
                $('#invite-helper-box').hide();
                $('#btn-rematch').hide();
                if (currentTurn === mySymbol) {
                    $('#game-status-text').html('<i class="fas fa-play-circle me-1 text-success"></i> <strong>ตาของคุณแล้ว! (' + mySymbol + ')</strong> คลิกช่องเพื่อวางหมาก');
                } else {
                    let opponentName = (mySymbol === 'X') ? (room.player_o || 'คู่แข่ง') : room.player_x;
                    $('#game-status-text').html('<i class="fas fa-hourglass-half me-1 text-muted"></i> กำลังรอ <strong>' + escapeHtml(opponentName) + ' (' + currentTurn + ')</strong> เดินหมาก...');
                }
            } else if (gameStatus === 'finished') {
                $('#invite-helper-box').hide();
                $('#btn-rematch').show();
                if (room.winner === 'draw') {
                    $('#game-status-text').html('<strong class="text-warning">🤝 เสมอกัน!</strong> ไม่มีใครชนะ');
                } else if (room.winner === mySymbol) {
                    $('#game-status-text').html('<strong class="text-success">🎉 ยินดีด้วย! คุณเป็นฝ่ายชนะ (' + mySymbol + ')</strong>');
                } else {
                    $('#game-status-text').html('<strong class="text-danger">😢 คุณแพ้! พยายามใหม่อีกครั้งนะ</strong>');
                }
            }

            // อัปเดตช่องกระดาน
            if (room.board && Array.isArray(room.board)) {
                room.board.forEach(function(val, idx) {
                    let $cell = $('.xo-cell[data-index="' + idx + '"]');
                    $cell.text(val);
                    $cell.removeClass('cell-x cell-o occupied');
                    if (val === 'X') $cell.addClass('cell-x occupied');
                    if (val === 'O') $cell.addClass('cell-o occupied');
                });
            }
        }, 'json');
    }

    function cellClicked(index) {
        if (gameStatus !== 'playing') {
            if (gameStatus === 'waiting') alert('กรุณารอเพื่อนเข้าร่วมห้องก่อนเริ่มเล่นครับ');
            return;
        }

        if (currentTurn !== mySymbol) {
            alert('ยังไม่ใช่ตาของคุณครับ กรุณารอคู่แข่งเดินก่อน');
            return;
        }

        let $cell = $('.xo-cell[data-index="' + index + '"]');
        if ($cell.text() !== '') {
            return; // ช่องนี้ถูกวางแล้ว
        }

        // ส่งคำสั่งเดินหมาก
        $.post('<?= base_url('xo/make_move/') ?>' + ROOM_ID, {
            cell_index: index
        }, function(res) {
            if (res && res.status === 'ok') {
                fetchGameState();
            } else if (res && res.error === 'not_your_turn') {
                alert('ยังไม่ใช่ตาของคุณครับ');
            }
        }, 'json');
    }

    function requestRematch() {
        $.post('<?= base_url('xo/rematch/') ?>' + ROOM_ID, function(res) {
            if (res && res.status === 'ok') {
                fetchGameState();
            }
        }, 'json');
    }

    // ฟังก์ชันเชิญเพื่อนเข้าห้อง XO
    function loadXoOnlineFriends() {
        $.get('<?= base_url('player/get_online_users') ?>', function(data) {
            let html = '';
            if (data && data.users) {
                let onlineFriends = data.users.filter(u => !u.is_self && u.status !== 'offline');
                let otherFriends  = data.users.filter(u => !u.is_self && u.status === 'offline');

                if (onlineFriends.length > 0) {
                    onlineFriends.forEach(function(u) {
                        let statusBadge = (u.status === 'playing') ? '<span class="badge bg-warning text-dark ms-2 small">ในเกม</span>' : '<span class="badge bg-success ms-2 small">ออนไลน์</span>';
                        html += `
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="online-dot" style="width:10px;height:10px;background:#10b981;border-radius:50%;display:inline-block;"></span>
                                <strong class="text-white">${escapeHtml(u.username)}</strong>
                                ${statusBadge}
                            </div>
                            <button class="btn btn-sm btn-primary" onclick="sendXoInvite('${escapeHtml(u.username)}')">
                                <i class="fas fa-paper-plane me-1"></i>เชิญเข้าห้องนี้
                            </button>
                        </div>`;
                    });
                } else {
                    html = '<div class="text-center py-3 text-muted">ไม่มีเพื่อนที่ออนไลน์อยู่ในขณะนี้</div>';
                }

                if (otherFriends.length > 0) {
                    html += '<div class="mt-3 mb-2 text-muted small text-start">เพื่อนอื่นๆ:</div>';
                    otherFriends.forEach(function(u) {
                        html += `
                        <div class="d-flex justify-content-between align-items-center p-2 mb-1" style="background: rgba(255,255,255,0.02); border-radius: 8px; opacity: 0.7;">
                            <span class="text-white small">${escapeHtml(u.username)} (ออฟไลน์)</span>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="sendXoInvite('${escapeHtml(u.username)}')">
                                ส่งคำเชิญ
                            </button>
                        </div>`;
                    });
                }
            } else {
                html = '<div class="text-center py-4 text-muted">ไม่พบข้อมูลเพื่อนในระบบ</div>';
            }
            $('#xo-online-friends-list').html(html);
        }, 'json');
    }

    function sendXoInvite(targetUsername) {
        $.post('<?= base_url('player/send_invite') ?>', {
            to_username: targetUsername,
            game_key: 'xo',
            game_name: 'OX (Tic Tac Toe)',
            room_id: ROOM_ID
        }, function(res) {
            if (res && res.status === 'ok') {
                alert('ส่งคำเชิญเข้าห้องนี้ไปยัง ' + targetUsername + ' สำเร็จแล้ว!');
                $('#inviteOnlineFriendsModal').modal('hide');
            }
        }, 'json');
    }

    $('#inviteOnlineFriendsModal').on('show.bs.modal', function() {
        loadXoOnlineFriends();
    });

    $(document).ready(function() {
        // ดึงสถานะครั้งแรก
        fetchGameState();
        // Polling ซิงค์สถานะกระดานทุกๆ 1 วินาทีแบบเรียลไทม์
        pollInterval = setInterval(fetchGameState, 1000);

        // หากผู้เล่นปิดหน้าต่างหรือกดย้อนกลับ ให้ออกจากห้องและล้างข้อมูลห้อง
        window.addEventListener('beforeunload', function() {
            if (navigator.sendBeacon) {
                navigator.sendBeacon('<?= base_url('xo/leave/') ?>' + ROOM_ID);
            }
        });
    });
</script>

</body>
</html>
