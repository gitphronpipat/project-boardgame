<!-- ========================================================
     GLOBAL PLAYER WIDGET (Online Presence, Invites & Chat)
     ======================================================== -->

<!-- iziToast CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

<style>
    /* Big Popup iziToast Custom Styles */
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
    }

    .big-popup-lose {
        border-color: rgba(248, 113, 113, 0.6) !important;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9), 0 0 45px rgba(248, 113, 113, 0.4) !important;
    }

    .big-popup-draw {
        border-color: rgba(56, 189, 248, 0.6) !important;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9), 0 0 45px rgba(56, 189, 248, 0.4) !important;
    }

    .text-muted {
        --bs-text-opacity: 1 !important;
        color: #ffffff !important;
    }
    /* Navbar Widget Items */
    .widget-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: #e0e0e0;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        text-decoration: none;
        position: relative;
    }
    .widget-btn:hover {
        background: rgba(129, 140, 248, 0.2);
        color: #fff;
        border-color: rgba(129, 140, 248, 0.4);
    }
    .widget-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        display: none;
        animation: pulse-red 1.5s infinite;
    }
    @keyframes pulse-red {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }

    /* Modal Dark Theme */
    .modal-dark .modal-content {
        background: rgba(26, 26, 46, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #e0e0e0;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
    }
    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 16px 22px;
    }
    .modal-dark .modal-title {
        color: #fff;
        font-weight: 600;
        font-size: 1.15rem;
    }
    .modal-dark .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .btn-close-white {
        filter: invert(1);
    }

    /* Friend List Items */
    .friend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        margin-bottom: 8px;
        transition: all 0.2s;
    }
    .friend-item:hover {
        background: rgba(255, 255, 255, 0.08);
    }
    .friend-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .friend-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(129, 140, 248, 0.2);
        color: #818cf8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        position: relative;
    }
    .status-dot-badge {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        position: absolute;
        bottom: 0;
        right: 0;
        border: 2px solid #1a1a2e;
    }
    .dot-online { background-color: #10b981; }
    .dot-playing { background-color: #f59e0b; }
    .dot-offline { background-color: #6b7280; }

    .status-label {
        font-size: 12px;
        display: block;
    }
    .text-online { color: #34d399; }
    .text-playing { color: #fbbf24; }
    .text-offline { color: #9ca3af; }

    /* Invite & Action Buttons */
    .friend-item.has-unread {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.4);
        box-shadow: 0 0 12px rgba(239, 68, 68, 0.15);
    }
    .btn-action-chat {
        background: rgba(129, 140, 248, 0.15);
        border: 1px solid rgba(129, 140, 248, 0.3);
        color: #a5b4fc;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 12px;
        transition: all 0.2s;
    }
    .btn-action-chat:hover {
        background: rgba(129, 140, 248, 0.3);
        color: #fff;
    }
    .btn-action-chat.active-unread {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }
    .btn-action-chat.active-unread:hover {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }
    .btn-action-invite {
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        border: none;
        color: #fff;
        border-radius: 8px;
        padding: 4px 12px;
        font-size: 12px;
    }
    .btn-action-invite:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    }

    /* Chat Messages Box */
    .chat-messages {
        height: 280px;
        overflow-y: auto;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        margin-bottom: 12px;
    }
    .chat-bubble {
        max-width: 75%;
        padding: 8px 12px;
        border-radius: 14px;
        font-size: 13px;
        line-height: 1.4;
        word-break: break-word;
    }
    .chat-bubble.mine {
        align-self: flex-end;
        background: linear-gradient(135deg, #6366f1, #818cf8);
        color: #fff;
        border-bottom-right-radius: 4px;
    }
    .chat-bubble.theirs {
        align-self: flex-start;
        background: rgba(255, 255, 255, 0.1);
        color: #e0e0e0;
        border-bottom-left-radius: 4px;
    }
    .chat-time {
        font-size: 10px;
        opacity: 0.7;
        margin-top: 3px;
        display: block;
        text-align: right;
    }

    /* Invitations Card */
    .invite-card {
        background: rgba(129, 140, 248, 0.1);
        border: 1px solid rgba(129, 140, 248, 0.25);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 10px;
    }
</style>

<!-- Modal: รายชื่อเพื่อนและสถานะออนไลน์ -->
<div class="modal fade modal-dark" id="onlineFriendsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users me-2 text-primary"></i>รายชื่อเพื่อน & สถานะออนไลน์</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">ผู้ใช้ทั้งหมดในระบบ</span>
                    <span class="badge bg-success" id="modal-online-badge">ออนไลน์ 0 คน</span>
                </div>
                <div id="friends-list-container" style="max-height: 360px; overflow-y: auto;">
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดข้อมูล...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: กล่องข้อความและการแจ้งเตือนคำเชิญเล่นเกม -->
<div class="modal fade modal-dark" id="invitationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-bell me-2 text-warning"></i>คำเชิญเล่นเกม & การแจ้งเตือน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="invitations-list-container">
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-envelope-open me-2"></i>ไม่มีคำเชิญใหม่ในขณะนี้
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: แชทสนทนากับเพื่อน -->
<div class="modal fade modal-dark" id="chatBoxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comments me-2 text-info"></i>แชทกับ: <span id="chat-target-name" class="text-primary">เพื่อน</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="chat-messages" id="chat-messages-container">
                    <div class="text-center py-4 text-muted small">เริ่มการสนทนาได้เลย!</div>
                </div>
                <form id="chat-form" onsubmit="sendChatMessage(event)">
                    <div class="input-group">
                        <input type="text" id="chat-input-text" class="form-control" placeholder="พิมพ์ข้อความที่นี่..." autocomplete="off" required>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane me-1"></i>ส่ง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: แก้ไขข้อมูลส่วนตัว (เปลี่ยนชื่อผู้ใช้ & รหัสผ่าน) -->
<div class="modal fade modal-dark" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-pen me-2 text-primary"></i>แก้ไขข้อมูลส่วนตัว
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-profile-form" onsubmit="submitEditProfile(event)">
                <div class="modal-body">
                    <div id="edit-profile-alert" style="display:none;" class="alert py-2 mb-3"></div>

                    <!-- ชื่อผู้ใช้ (User) -->
                    <div class="mb-3">
                        <label for="profile-username" class="form-label text-white small fw-bold">
                            <i class="fas fa-user me-1 text-info"></i>ชื่อผู้ใช้ (Username)
                        </label>
                        <input type="text" class="form-control" id="profile-username" name="username" required
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); color: #fff; border-radius: 10px;">
                        <div class="form-text text-muted" style="font-size: 11px;">
                            คุณสามารถเปลี่ยนชื่อผู้ใช้ที่ใช้แสดงในเกมและระบบได้
                        </div>
                    </div>

                    <!-- รหัสผ่าน (Password) พร้อมปุ่มกดแสดงรหัสผ่าน -->
                    <div class="mb-3">
                        <label for="profile-password" class="form-label text-white small fw-bold">
                            <i class="fas fa-lock me-1 text-warning"></i>รหัสผ่าน (Password)
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="profile-password" name="password" required
                                   style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); color: #fff; border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                            <button class="btn btn-outline-secondary text-white" type="button" id="btn-toggle-profile-pass" onclick="toggleProfilePasswordVisibility()" title="คลิกเพื่อแสดง/ซ่อนรหัสผ่าน" style="border-color: rgba(255,255,255,0.18); background: rgba(255,255,255,0.05); border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                                <i class="fas fa-eye" id="toggle-profile-pass-icon"></i>
                            </button>
                        </div>
                        <div class="form-text text-muted" style="font-size: 11px;">
                            กดรูปดวงตาเพื่อดูรหัสผ่าน และสามารถพิมพ์เพื่อแก้ไขและเปลี่ยนรหัสผ่านใหม่ได้ทันที
                        </div>
                    </div>

                    <div class="p-2 rounded mt-3" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); font-size: 12px; color: #c7d2fe;">
                        <i class="fas fa-info-circle me-1 text-primary"></i> เมื่อบันทึกแล้ว ข้อมูลและสถิติเกมทั้งหมดจะถูกอัปเดตเป็นชื่อใหม่โดยอัตโนมัติ
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btn-save-profile">
                        <i class="fas fa-save me-1"></i>บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript ประจำ Global Widget -->
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

    window.CURRENT_USER = window.CURRENT_USER || '<?= $this->session->userdata('username') ?>';
    var CURRENT_USER = window.CURRENT_USER;
    window.BASE_URL = window.BASE_URL || '<?= base_url() ?>';
    var BASE_URL = window.BASE_URL;
    let currentChatTarget = '';
    let chatPollInterval = null;

    // 1. Heartbeat อัปเดตสถานะออนไลน์
    function sendHeartbeat(status = 'online', game = '', roomId = '') {
        $.post(BASE_URL + 'player/heartbeat', {
            status: status,
            game: game,
            room_id: roomId
        });
    }

    let previousUnreadChats = -1;

    // 2. ดึงรายชื่อเพื่อนและสถานะออนไลน์
    function fetchOnlineUsers() {
        $.get(BASE_URL + 'player/get_online_users', function(data) {
            if (!data) return;

            // แจ้งเตือน Toast เมื่อมีข้อความแชทใหม่เข้ามา
            let currentUnread = parseInt(data.total_unread_chats) || 0;
            if (previousUnreadChats !== -1 && currentUnread > previousUnreadChats) {
                if (typeof iziToast !== 'undefined' && (!$('#chatBoxModal').hasClass('show'))) {
                    iziToast.show({
                        theme: 'dark',
                        icon: 'fas fa-comments',
                        iconColor: '#38bdf8',
                        title: '💬 ข้อความแชทใหม่!',
                        titleColor: '#38bdf8',
                        message: 'คุณได้รับข้อความใหม่จากเพื่อน',
                        position: 'bottomRight',
                        timeout: 5000
                    });
                }
            }
            previousUnreadChats = currentUnread;

            // อัปเดตตัวเลขบน Navbar
            $('#navbar-online-count').text(data.online_count || 0);

            // แจ้งเตือนแชทที่ยังไม่ได้อ่านบน Navbar
            if (data.total_unread_chats && data.total_unread_chats > 0) {
                $('#navbar-chat-badge').text(data.total_unread_chats).show();
                $('#modal-online-badge').html(`
                    <span>ออนไลน์ ${data.online_count || 0} คน</span>
                    <span class="badge bg-danger ms-2"><i class="fas fa-comment-dots me-1"></i>${data.total_unread_chats} ข้อความใหม่</span>
                `);
            } else {
                $('#navbar-chat-badge').hide();
                $('#modal-online-badge').text('ออนไลน์ ' + (data.online_count || 0) + ' คน');
            }

            let html = '';
            if (data.users && data.users.length > 0) {
                data.users.forEach(function(u) {
                    let dotClass = 'dot-offline';
                    let textClass = 'text-offline';
                    let statusText = 'ออฟไลน์';

                    if (u.status === 'online') {
                        dotClass = 'dot-online';
                        textClass = 'text-online';
                        statusText = 'ออนไลน์';
                    } else if (u.status === 'playing') {
                        dotClass = 'dot-playing';
                        textClass = 'text-playing';
                        statusText = 'กำลังเล่น: ' + (u.game || 'เกม');
                    }

                    let hasUnread = (u.unread_count && u.unread_count > 0);
                    let unreadBadge = hasUnread ? `
                        <span class="badge bg-danger rounded-pill ms-2" style="font-size:11px;">
                            <i class="fas fa-envelope me-1"></i>${u.unread_count} ข้อความใหม่
                        </span>` : '';
                    let unreadMsgPreview = (hasUnread && u.last_unread_message) ? `
                        <div class="small text-warning text-truncate mt-1" style="max-width: 220px; font-size: 11px;">
                            <i class="fas fa-comment me-1 opacity-75"></i>${escapeHtml(u.last_unread_message)}
                        </div>` : '';
                    let chatBtnClass = hasUnread ? 'btn-action-chat active-unread' : 'btn-action-chat';
                    let chatBtnText = hasUnread ? `<i class="fas fa-comment-dots me-1"></i>อ่านข้อความ (${u.unread_count})` : '<i class="fas fa-comment me-1"></i>แชท';

                    html += `
                    <div class="friend-item ${hasUnread ? 'has-unread' : ''}">
                        <div class="friend-info">
                            <div class="friend-avatar">
                                <i class="fas fa-user"></i>
                                <span class="status-dot-badge ${dotClass}"></span>
                            </div>
                            <div>
                                <div class="fw-bold text-white d-flex align-items-center flex-wrap">
                                    <span>${escapeHtml(u.username)}</span>
                                    ${u.is_self ? '<span class="badge bg-secondary small ms-1">คุณ</span>' : ''}
                                    ${unreadBadge}
                                </div>
                                <span class="status-label ${textClass}"><i class="fas fa-circle me-1" style="font-size:8px;"></i>${statusText}</span>
                                ${unreadMsgPreview}
                            </div>
                        </div>
                        <div>
                            ${!u.is_self ? `
                                <button class="${chatBtnClass}" onclick="openChatWith('${escapeHtml(u.username)}')">
                                    ${chatBtnText}
                                </button>
                            ` : ''}
                        </div>
                    </div>`;
                });
            } else {
                html = '<div class="text-center py-4 text-muted">ไม่พบข้อมูลผู้ใช้</div>';
            }

            $('#friends-list-container').html(html);
        }, 'json');
    }

    let alertedInviteIds = {};

    // 3. ตรวจสอบคำเชิญเล่นเกมที่ส่งมาหาเรา (เรียงลำดับใหม่ล่าสุดอยู่บนสุด)
    function fetchInvitations() {
        $.get(BASE_URL + 'player/get_invitations', function(invites) {
            if (!invites || invites.length === 0) {
                $('#navbar-invite-badge').hide();
                $('#invitations-list-container').html('<div class="text-center py-4 text-muted"><i class="fas fa-envelope-open me-2"></i>ไม่มีคำเชิญใหม่ในขณะนี้</div>');
                return;
            }

            // จัดเรียงคำเชิญ: ล่าสุดอยู่บนสุดเสมอ (Newest First)
            invites.sort(function(a, b) {
                return (b.time || 0) - (a.time || 0);
            });

            $('#navbar-invite-badge').text(invites.length).show();

            // ตรวจสอบคำเชิญใหม่เพื่อแสดง iziToast แจ้งเตือนเด้งขึ้นมา
            invites.forEach(function(inv) {
                if (!alertedInviteIds[inv.id]) {
                    alertedInviteIds[inv.id] = true;
                    if (typeof iziToast !== 'undefined') {
                        iziToast.show({
                            theme: 'dark',
                            icon: 'fas fa-gamepad',
                            iconColor: '#818cf8',
                            title: '🎮 คำเชิญเล่นเกมใหม่!',
                            titleColor: '#818cf8',
                            message: `<strong>${escapeHtml(inv.from)}</strong> ชวนคุณเข้าเล่น <strong>${escapeHtml(inv.game_name || inv.game_key)}</strong>`,
                            messageColor: '#ffffff',
                            backgroundColor: '#1e1b4b',
                            position: 'topRight',
                            timeout: 12000,
                            close: true,
                            progressBarColor: '#6366f1',
                            buttons: [
                                ['<button class="btn btn-sm btn-success px-2 py-1 me-1" style="font-size:12px; font-weight:600;"><i class="fas fa-check me-1"></i>ตอบรับ</button>', function (instance, toast) {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    respondInvite(inv.id, 'accept');
                                }, true],
                                ['<button class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size:12px;"><i class="fas fa-times me-1"></i>ปฏิเสธ</button>', function (instance, toast) {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    respondInvite(inv.id, 'decline');
                                }]
                            ]
                        });
                    }
                }
            });

            let html = '';
            invites.forEach(function(inv) {
                html += `
                <div class="invite-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong class="text-white"><i class="fas fa-gamepad text-primary me-1"></i>${escapeHtml(inv.from)}</strong> 
                            <span>ชวนคุณเข้าเล่น</span> 
                            <span class="badge bg-primary">${escapeHtml(inv.game_name || inv.game_key)}</span>
                        </div>
                        <span class="text-muted small">${getTimeAgo(inv.time)}</span>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-success btn-sm w-50" onclick="respondInvite('${escapeHtml(inv.id)}', 'accept')">
                            <i class="fas fa-check me-1"></i>ตอบรับ & เข้าเล่น
                        </button>
                        <button class="btn btn-outline-danger btn-sm w-50" onclick="respondInvite('${escapeHtml(inv.id)}', 'decline')">
                            <i class="fas fa-times me-1"></i>ปฏิเสธ
                        </button>
                    </div>
                </div>`;
            });

            $('#invitations-list-container').html(html);
        }, 'json');
    }

    // ตอบรับ / ปฏิเสธคำเชิญ
    function respondInvite(inviteId, action) {
        $.post(BASE_URL + 'player/respond_invite', {
            invite_id: inviteId,
            action: action
        }, function(res) {
            if (res && res.status === 'ok') {
                if (action === 'accept' && res.redirect_url) {
                    if (typeof iziToast !== 'undefined') {
                        iziToast.info({
                            title: 'เข้าสู่เกม',
                            message: 'กำลังนำคุณเข้าสู่ห้องเล่นเกม...',
                            position: 'topRight',
                            timeout: 1500
                        });
                    }
                    setTimeout(function() {
                        window.location.href = res.redirect_url;
                    }, 400);
                } else {
                    if (typeof iziToast !== 'undefined') {
                        iziToast.info({
                            title: 'ปฏิเสธคำเชิญ',
                            message: 'ปฏิเสธคำเชิญเรียบร้อยแล้ว',
                            position: 'topRight',
                            timeout: 2500
                        });
                    }
                    fetchInvitations();
                }
            }
        }, 'json');
    }

    // 4. แชทพูดคุย
    function openChatWith(username) {
        currentChatTarget = username;
        $('#chat-target-name').text(username);
        $('#onlineFriendsModal').modal('hide');
        $('#chatBoxModal').modal('show');

        // เคลียร์ unread counter สำหรับเพื่อนคนนี้ทันที
        $.post(BASE_URL + 'player/mark_chat_read', { with_username: username }, function() {
            fetchOnlineUsers();
        });

        loadChatMessages();

        if (chatPollInterval) clearInterval(chatPollInterval);
        chatPollInterval = setInterval(loadChatMessages, 4000);
    }

    function loadChatMessages() {
        if (!currentChatTarget) return;
        $.get(BASE_URL + 'player/get_chat', { to_username: currentChatTarget }, function(messages) {
            let html = '';
            if (messages && messages.length > 0) {
                messages.forEach(function(m) {
                    let isMine = (m.sender === CURRENT_USER);
                    html += `
                    <div class="chat-bubble ${isMine ? 'mine' : 'theirs'}">
                        <div>${escapeHtml(m.message)}</div>
                        <span class="chat-time">${formatTime(m.time)}</span>
                    </div>`;
                });
            } else {
                html = '<div class="text-center py-4 text-muted small">ยังไม่มีข้อความ เริ่มทักทายได้เลย!</div>';
            }
            $('#chat-messages-container').html(html);
            $('#chat-messages-container').scrollTop($('#chat-messages-container')[0].scrollHeight);
        }, 'json');
    }

    function sendChatMessage(e) {
        e.preventDefault();
        let text = $('#chat-input-text').val().trim();
        if (!text || !currentChatTarget) return;

        $.post(BASE_URL + 'player/send_chat', {
            to_username: currentChatTarget,
            message: text
        }, function(res) {
            $('#chat-input-text').val('');
            loadChatMessages();
        });
    }

    // Helper functions
    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }
    function formatTime(timestamp) {
        if (!timestamp) return '';
        let d = new Date(timestamp * 1000);
        return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
    }
    function getTimeAgo(timestamp) {
        let diff = Math.floor(Date.now() / 1000) - timestamp;
        if (diff < 60) return diff + ' วินาทีที่แล้ว';
        return Math.floor(diff / 60) + ' นาทีที่แล้ว';
    }

    // 5. ระบบแก้ไขข้อมูลโปรไฟล์ (ชื่อผู้ใช้ และรหัสผ่าน)
    function openEditProfileModal(e) {
        if (e && e.preventDefault) e.preventDefault();
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalEl = document.getElementById('editProfileModal');
            if (modalEl) {
                var inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                inst.show();
                return;
            }
        }
        $('#editProfileModal').modal('show');
    }

    $('#editProfileModal').on('show.bs.modal', function() {
        $('#edit-profile-alert').hide().text('');
        $('#profile-username').val('');
        $('#profile-password').attr('type', 'password').val('');
        $('#toggle-profile-pass-icon').removeClass('fa-eye-slash').addClass('fa-eye');
        $('#btn-save-profile').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>กำลังโหลดข้อมูล...');

        $.get(BASE_URL + 'player/get_profile', function(res) {
            $('#btn-save-profile').prop('disabled', false).html('<i class="fas fa-save me-1"></i>บันทึกการเปลี่ยนแปลง');
            if (res && res.status === 'ok') {
                $('#profile-username').val(res.username);
                $('#profile-password').val(res.password || '');
            } else {
                $('#edit-profile-alert').removeClass('alert-success').addClass('alert-danger').text(res.message || 'ไม่สามารถโหลดข้อมูลโปรไฟล์ได้').show();
            }
        }, 'json').fail(function() {
            $('#btn-save-profile').prop('disabled', false).html('<i class="fas fa-save me-1"></i>บันทึกการเปลี่ยนแปลง');
            $('#edit-profile-alert').removeClass('alert-success').addClass('alert-danger').text('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์').show();
        });
    });

    function toggleProfilePasswordVisibility() {
        let input = $('#profile-password');
        let icon = $('#toggle-profile-pass-icon');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }

    function submitEditProfile(e) {
        e.preventDefault();
        let newUsername = $('#profile-username').val().trim();
        let newPassword = $('#profile-password').val().trim();

        if (!newUsername) {
            $('#edit-profile-alert').removeClass('alert-success').addClass('alert-danger').text('กรุณากรอกชื่อผู้ใช้').show();
            return;
        }
        if (!newPassword) {
            $('#edit-profile-alert').removeClass('alert-success').addClass('alert-danger').text('กรุณากรอกรหัสผ่าน').show();
            return;
        }

        $('#btn-save-profile').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>กำลังบันทึก...');
        $('#edit-profile-alert').hide().text('');

        $.post(BASE_URL + 'player/update_profile', {
            username: newUsername,
            password: newPassword
        }, function(res) {
            $('#btn-save-profile').prop('disabled', false).html('<i class="fas fa-save me-1"></i>บันทึกการเปลี่ยนแปลง');
            if (res && res.status === 'ok') {
                $('#edit-profile-alert').removeClass('alert-danger').addClass('alert-success').text(res.message || 'บันทึกสำเร็จ').show();
                if (typeof iziToast !== 'undefined') {
                    iziToast.success({
                        title: 'สำเร็จ',
                        message: res.message || 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว',
                        position: 'topRight',
                        timeout: 2500
                    });
                }
                setTimeout(function() {
                    window.location.reload();
                }, 800);
            } else {
                $('#edit-profile-alert').removeClass('alert-success').addClass('alert-danger').text(res.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล').show();
            }
        }, 'json').fail(function() {
            $('#btn-save-profile').prop('disabled', false).html('<i class="fas fa-save me-1"></i>บันทึกการเปลี่ยนแปลง');
            $('#edit-profile-alert').removeClass('alert-success').addClass('alert-danger').text('เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์').show();
        });
    }

    // เริ่มต้นระบบอัตโนมัติเมื่อโหลดหน้าเว็บ
    $(document).ready(function() {
        // ส่ง heartbeat ครั้งแรก
        sendHeartbeat();
        fetchOnlineUsers();
        fetchInvitations();

        // ตั้งเวลาทำงานต่อเนื่อง (ปรับความถี่เพื่อลดภาระเครื่องและเพิ่มความเร็วเว็บ)
        setInterval(function() { sendHeartbeat(); }, 30000);
        setInterval(function() { fetchInvitations(); }, 8000);
        setInterval(function() { fetchOnlineUsers(); }, 15000);

        $('#chatBoxModal').on('hidden.bs.modal', function () {
            if (chatPollInterval) clearInterval(chatPollInterval);
        });
    });
</script>
