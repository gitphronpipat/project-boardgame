<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js"></script>
    <!-- iziToast -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            color: #e0e0e0;
        }

        .text-muted {
            --bs-text-opacity: 1 !important;
            color: #ffffff !important;
        }

        .lobby-main-container {
            max-width: 1240px;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
        }

        .lobby-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            position: relative;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
        }
        .lobby-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: var(--game-color, #818cf8);
        }

        .lobby-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .lobby-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.3rem;
        }
        .lobby-desc {
            color: rgba(255,255,255,0.5);
            margin-bottom: 2rem;
        }

        .lobby-status {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
        }
        .lobby-status h5 {
            color: #a5b4fc;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }
        .player-slot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0.6rem 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .player-slot:last-child { border-bottom: none; }
        .player-slot .avatar {
            width: 36px; height: 36px;
            background: rgba(129,140,248,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; color: #a5b4fc;
        }
        .player-slot .name { font-size: 0.95rem; }
        .player-slot .host-badge {
            background: rgba(250,204,21,0.15);
            color: #fbbf24;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 8px;
        }
        .player-slot.empty {
            color: rgba(255,255,255,0.25);
            font-style: italic;
        }
        .player-slot.empty .avatar {
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.15);
        }
        .player-slot .player-stats-badges {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .waiting-text {
            color: rgba(255,255,255,0.4);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .waiting-text .dots::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }
        @keyframes dots {
            0% { content: ''; }
            25% { content: '.'; }
            50% { content: '..'; }
            75% { content: '...'; }
        }

        .btn-invite {
            background: rgba(129,140,248,0.2);
            border: 1px solid rgba(129,140,248,0.3);
            color: #a5b4fc;
            border-radius: 12px;
            padding: 10px 28px;
            font-size: 0.95rem;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            margin: 0 0.3rem;
        }
        .btn-invite:hover {
            background: rgba(129,140,248,0.3);
            color: #c7d2fe;
        }
        .btn-start {
            background: var(--game-color, #818cf8);
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: 10px 28px;
            font-size: 0.95rem;
            transition: all 0.2s;
            display: inline-block;
            margin: 0 0.3rem;
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <title><?= $title ?></title>
</head>
<body>

<!-- Unified Dynamic Navbar -->
<?php $this->load->view('player/components/navbar', [
    'nav_mode'   => 'lobby',
    'game'       => $game,
    'game_key'   => $game_key,
    'room_id'    => $room_id,
    'user_stats' => isset($user_stats) ? $user_stats : null,
]); ?>

<!-- Notify -->
<?php
$result  = $this->session->flashdata('result');
$message = $this->session->flashdata('message');
$titles_map  = ['true' => 'สำเร็จ', 'false' => 'ไม่สำเร็จ', 'duplicate' => 'ไม่สำเร็จ'];
$icons   = ['true' => 'success', 'false' => 'error', 'duplicate' => 'warning'];
?>
<?php if ($result && isset($icons[$result])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        iziToast.<?= $icons[$result] ?>({
            title: '<?= $titles_map[$result] ?>',
            message: '<?= addslashes($message) ?>',
            position: 'topRight',
            timeout: 3000,
        });
    });
</script>
<?php endif; ?>

<!-- Lobby & Rules Section -->
<div class="lobby-main-container">
    <div class="row g-4 align-items-start justify-content-center">
        <!-- ฝั่งซ้าย: ห้องล็อบบี้และรายชื่อผู้เล่น -->
        <div class="col-12 col-lg-7 col-xl-7">
            <div class="lobby-card" style="--game-color: <?= $game['color'] ?>">
                <div class="lobby-icon"><?= $game['icon'] ?></div>
                <div class="lobby-title"><?= $game['name'] ?></div>
                <div class="lobby-desc"><?= $game['desc'] ?></div>

                <div class="lobby-status">
                    <h5 id="lobby-count-text"><i class="fas fa-users me-1"></i>ผู้เล่นในห้อง (<span id="current-players-count">1</span>/<?= explode('-', str_replace(' คน', '', $game['players']))[0] ?>)</h5>

                    <!-- ช่องแสดงผู้เล่นในห้อง (ซิงค์เรียลไทม์) -->
                    <div id="lobby-player-slots">
                        <div class="player-slot">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar"><i class="fas fa-user"></i></div>
                                <span class="name"><?= htmlspecialchars($this->session->userdata('username')) ?></span>
                                <span class="host-badge"><i class="fas fa-crown me-1"></i>Host</span>
                            </div>
                            <div class="player-stats-badges">
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 0.8rem; border-radius: 8px;" title="จำนวนครั้งที่ชนะในเกมนี้">
                                    <i class="fas fa-trophy text-warning me-1"></i>ชนะ: <strong><?= isset($user_stats['wins']) ? (int)$user_stats['wins'] : 0 ?></strong>
                                </span>
                                <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1" style="font-size: 0.8rem; border-radius: 8px;" title="จำนวนครั้งที่เล่นทั้งหมดในเกมนี้">
                                    <i class="fas fa-gamepad text-primary me-1"></i>เล่น: <strong><?= isset($user_stats['played']) ? (int)$user_stats['played'] : 0 ?></strong>
                                </span>
                            </div>
                        </div>
                        <div class="player-slot empty">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar"><i class="fas fa-user-plus"></i></div>
                                <span class="name">รอผู้เล่น...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="waiting-text" id="lobby-waiting-msg">
                    <i class="fas fa-spinner fa-spin me-1"></i>กำลังรอเพื่อนเข้าร่วมห้องล็อบบี้<span class="dots"></span>
                </p>

                <!-- ปุ่มควบคุมในล็อบบี้ (แยกมุมมอง Host / Guest) -->
                <div id="lobby-actions-container">
                    <button type="button" class="btn-invite" data-bs-toggle="modal" data-bs-target="#inviteOnlineFriendsModal" onclick="loadLobbyOnlineFriends()">
                        <i class="fas fa-paper-plane me-1"></i>เชิญเพื่อนที่ออนไลน์
                    </button>
                    <button class="btn-start" id="btn-start-game" disabled onclick="hostStartGame()">
                        <i class="fas fa-play me-1"></i>เริ่มเกม
                    </button>
                </div>
            </div>
        </div>

        <!-- ฝั่งขวา: หน้าต่างกฎและกติกาการเล่น -->
        <div class="col-12 col-lg-5 col-xl-5">
            <?php $this->load->view('game_rules', [
                'game_key' => $game_key,
                'game'     => $game,
            ]); ?>
        </div>
    </div>
</div>

<!-- Modal: เชิญเพื่อนที่ออนไลน์ -->
<div class="modal fade modal-dark" id="inviteOnlineFriendsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(26, 26, 46, 0.96); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 20px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h5 class="modal-title"><i class="fas fa-paper-plane me-2 text-primary"></i>เลือกเพื่อนที่ออนไลน์เพื่อเชิญ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="lobby-online-friends-list">
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดรายชื่อเพื่อนที่ออนไลน์...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Helper function escapeHtml
    window.escapeHtml = window.escapeHtml || function(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };
    var escapeHtml = window.escapeHtml;

    window.CURRENT_GAME_KEY = '<?= $game_key ?>';
    window.CURRENT_GAME_NAME = '<?= $game['name'] ?>';
    window.CURRENT_ROOM_ID = '<?= $room_id ?>';
    window.CURRENT_USER = '<?= $this->session->userdata('username') ?>';
    var CURRENT_GAME_KEY = window.CURRENT_GAME_KEY;
    var CURRENT_GAME_NAME = window.CURRENT_GAME_NAME;
    var CURRENT_ROOM_ID = window.CURRENT_ROOM_ID;
    var CURRENT_USER = window.CURRENT_USER;
    let isHost = false;
    let lobbyPollInterval = null;

    // ซิงค์สถานะห้องล็อบบี้แบบเรียลไทม์
    function fetchLobbyState() {
        $.get('<?= base_url('player/get_lobby_state/') ?>' + CURRENT_ROOM_ID + '?game_key=' + CURRENT_GAME_KEY, function(lobby) {
            if (!lobby || lobby.status === 'error') return;

            // หากห้องถูกปิดหรือโฮสต์ออกจากห้องแล้ว
            if (lobby.status === 'closed') {
                if (lobbyPollInterval) clearInterval(lobbyPollInterval);
                alert(lobby.message || 'ห้องล็อบบี้นี้ถูกปิดหรือโฮสต์ออกจากห้องแล้ว');
                window.location.href = '<?= base_url('player') ?>';
                return;
            }

            isHost = (lobby.host === CURRENT_USER);
            let players = lobby.players || [lobby.host];

            // อัปเดตจำนวนผู้เล่น
            $('#current-players-count').text(players.length);

            // อัปเดตรายชื่อผู้เล่นใน Slot
            let slotsHtml = '';
            players.forEach(function(p, idx) {
                let isSlotHost = (p === lobby.host);
                let pStats = (lobby.player_stats && lobby.player_stats[p]) ? lobby.player_stats[p] : { wins: 0, played: 0 };
                slotsHtml += `
                <div class="player-slot">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar"><i class="fas fa-user"></i></div>
                        <span class="name">${escapeHtml(p)}</span>
                        ${isSlotHost ? '<span class="host-badge"><i class="fas fa-crown me-1"></i>Host</span>' : '<span class="badge bg-primary ms-1" style="font-size:0.7rem;">ผู้เล่น</span>'}
                    </div>
                    <div class="player-stats-badges">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 0.8rem; border-radius: 8px;" title="จำนวนครั้งที่ชนะในเกมนี้">
                            <i class="fas fa-trophy text-warning me-1"></i>ชนะ: <strong>${pStats.wins || 0}</strong>
                        </span>
                        <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 px-2 py-1" style="font-size: 0.8rem; border-radius: 8px;" title="จำนวนครั้งที่เล่นทั้งหมดในเกมนี้">
                            <i class="fas fa-gamepad text-primary me-1"></i>เล่น: <strong>${pStats.played || 0}</strong>
                        </span>
                    </div>
                </div>`;
            });

            // ถ้ามีผู้เล่นไม่ถึง 2 คน ให้ขึ้นช่องว่าง
            if (players.length < 2) {
                slotsHtml += `
                <div class="player-slot empty">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar"><i class="fas fa-user-plus"></i></div>
                        <span class="name">รอผู้เล่น...</span>
                    </div>
                </div>`;
            }

            $('#lobby-player-slots').html(slotsHtml);

            // ควบคุมปุ่มเริ่มเกมตามบทบาท Host / Guest
            if (isHost) {
                if (players.length >= 2) {
                    $('#lobby-waiting-msg').html('<span class="text-success"><i class="fas fa-check-circle me-1"></i> เพื่อนเข้าร่วมล็อบบี้แล้ว! กดเริ่มเกมได้เลย</span>');
                    $('#btn-start-game').prop('disabled', false).css({ opacity: 1, cursor: 'pointer' });
                } else {
                    $('#lobby-waiting-msg').html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังรอเพื่อนตอบรับคำเชิญ<span class="dots"></span>');
                    $('#btn-start-game').prop('disabled', true).css({ opacity: 0.5, cursor: 'not-allowed' });
                }
            } else {
                // สำหรับ Guest ที่ตอบรับคำเชิญเข้ามา
                $('#lobby-actions-container').html(`
                    <div class="alert alert-info py-2 px-3 text-white d-inline-block" style="background: rgba(129,140,248,0.2); border: 1px solid rgba(129,140,248,0.4); border-radius: 12px;">
                        <i class="fas fa-hourglass-half fa-spin me-2 text-primary"></i>คุณอยู่ในล็อบบี้แล้ว รอ Host กดเริ่มเกม...
                    </div>
                `);
            }

            // ถ้า Host กดเริ่มเกมแล้ว -> พาผู้เล่นทุกคนเข้าห้องเกมทันที
            if (lobby.status === 'started' && lobby.redirect_url) {
                clearInterval(lobbyPollInterval);
                window.location.href = lobby.redirect_url;
            }
        }, 'json');
    }

    // Host กดเริ่มเกม
    function hostStartGame() {
        $('#btn-start-game').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังเข้าเกม...');
        $.post('<?= base_url('player/start_lobby_game/') ?>' + CURRENT_ROOM_ID, function(res) {
            if (res && res.status === 'ok' && res.redirect_url) {
                window.location.href = res.redirect_url;
            }
        }, 'json');
    }

    function loadLobbyOnlineFriends() {
        $('#lobby-online-friends-list').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดรายชื่อเพื่อนที่ออนไลน์...</div>');
        $.get('<?= base_url('player/get_online_users') ?>', function(data) {
            let html = '';
            if (data && data.users) {
                // เพื่อนที่ออนไลน์ หรือกำลังเล่นเกม (status !== 'offline')
                let onlineFriends = data.users.filter(u => !u.is_self && u.status !== 'offline');
                let otherFriends  = data.users.filter(u => !u.is_self && u.status === 'offline');

                if (onlineFriends.length > 0) {
                    onlineFriends.forEach(function(u) {
                        let statusBadge = (u.status === 'playing') ? '<span class="badge bg-warning text-dark ms-2 small">ในเกม: ' + escapeHtml(u.game || '') + '</span>' : '<span class="badge bg-success ms-2 small">ออนไลน์</span>';
                        html += `
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="online-dot" style="width:10px;height:10px;background:#10b981;border-radius:50%;display:inline-block;"></span>
                                <strong class="text-white">${escapeHtml(u.username)}</strong>
                                ${statusBadge}
                            </div>
                            <button class="btn btn-sm btn-primary" onclick="sendGameInvitation('${escapeHtml(u.username)}')">
                                <i class="fas fa-paper-plane me-1"></i>เชิญเข้าล็อบบี้
                            </button>
                        </div>`;
                    });
                } else {
                    html = '<div class="text-center py-3 text-muted"><i class="fas fa-user-clock me-1"></i> ไม่มีเพื่อนที่ออนไลน์อยู่ในขณะนี้</div>';
                }

                // แสดงเพื่อนที่ออฟไลน์อยู่ไว้ด้านล่างด้วย
                if (otherFriends.length > 0) {
                    html += '<div class="mt-3 mb-2 text-muted small text-start"><i class="fas fa-user-slash me-1"></i> ผู้ใช้อื่นๆ ในระบบ:</div>';
                    otherFriends.forEach(function(u) {
                        html += `
                        <div class="d-flex justify-content-between align-items-center p-2 mb-1" style="background: rgba(255,255,255,0.02); border-radius: 8px; opacity: 0.7;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="online-dot" style="width:8px;height:8px;background:#6b7280;border-radius:50%;display:inline-block;"></span>
                                <span class="text-white small">${escapeHtml(u.username)}</span>
                                <span class="badge bg-secondary small">ออฟไลน์</span>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px;" onclick="sendGameInvitation('${escapeHtml(u.username)}')">
                                ส่งคำเชิญ
                            </button>
                        </div>`;
                    });
                }
            } else {
                html = '<div class="text-center py-4 text-muted">ไม่พบข้อมูลเพื่อนในระบบ</div>';
            }
            $('#lobby-online-friends-list').html(html);
        }, 'json').fail(function(xhr, status, err) {
            console.error('Error loading online friends:', status, err);
            $('#lobby-online-friends-list').html('<div class="text-danger text-center py-3">เกิดข้อผิดพลาดในการโหลดข้อมูลเพื่อน กรุณาลองใหม่อีกครั้ง</div>');
        });
    }

    function sendGameInvitation(targetUsername) {
        $.post('<?= base_url('player/send_invite') ?>', {
            to_username: targetUsername,
            game_key: CURRENT_GAME_KEY,
            game_name: CURRENT_GAME_NAME,
            room_id: CURRENT_ROOM_ID
        }, function(res) {
            if (res && res.status === 'ok') {
                alert('ส่งคำเชิญเข้าล็อบบี้ไปยัง ' + targetUsername + ' สำเร็จแล้ว!');
                $('#inviteOnlineFriendsModal').modal('hide');
            } else {
                alert('ไม่สามารถส่งคำเชิญได้: ' + (res.message || 'เกิดข้อผิดพลาด'));
            }
        }, 'json').fail(function() {
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
        });
    }

    $(document).ready(function() {
        // อัปเดตสถานะว่าอยู่ในล็อบบี้นี้
        if (typeof sendHeartbeat === 'function') {
            sendHeartbeat('online', CURRENT_GAME_NAME, CURRENT_ROOM_ID);
        }

        $('#inviteOnlineFriendsModal').on('show.bs.modal', function() {
            loadLobbyOnlineFriends();
        });

        fetchLobbyState();
        lobbyPollInterval = setInterval(fetchLobbyState, 1500);

        // หากผู้ใช้ปิดหน้าต่างหรือกด Back ออกจากล็อบบี้ ให้เคลียร์ห้องใน Firebase
        window.addEventListener('beforeunload', function() {
            if (navigator.sendBeacon) {
                navigator.sendBeacon('<?= base_url('player/leave_lobby/') ?>' + CURRENT_ROOM_ID);
            }
        });
    });
</script>

<!-- Global Widgets -->
<?php $this->load->view('player/global_widget'); ?>

</body>
</html>
