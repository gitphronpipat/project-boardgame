<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pre-Game Settings Component for Minesweeper
 * ตำแหน่ง: application/views/games/minesweeper/settingforplay.php
 * แสดงผลที่คอลัมน์ซ้ายของหน้า Lobby
 * มีทั้ง:
 * 1) โหมดการเล่น (Game Mode) พร้อมระบบ Smart Lock ตรวจจับจำนวนผู้เล่น 1-4 คน
 * 2) ระดับความยาก (Difficulty) ง่าย, ปานกลาง, ยาก
 */

$game_key = isset($game_key) ? $game_key : 'minesweeper';
$room_id  = isset($room_id) ? $room_id : '';
?>

<style>
    .pregame-settings-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 1.4rem;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
        color: #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .pregame-settings-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #10b981, #06b6d4, #6366f1);
    }

    .pregame-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .pregame-header h5 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .host-role-badge {
        font-size: 0.72rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.35);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .setting-group {
        margin-bottom: 1.25rem;
    }

    .setting-label {
        font-size: 0.86rem;
        font-weight: 600;
        color: #94a3b8;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Option Cards for Modes */
    .option-grid {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }

    .setting-option-btn {
        background: rgba(15, 23, 42, 0.6);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 0.65rem 0.85rem;
        color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        width: 100%;
        position: relative;
    }

    .setting-option-btn:hover:not(.locked):not(.disabled) {
        border-color: rgba(99, 102, 241, 0.5);
        background: rgba(99, 102, 241, 0.12);
        transform: translateY(-1px);
    }

    .setting-option-btn.active {
        border-color: #6366f1;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(16, 185, 129, 0.15));
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
    }

    .setting-option-btn.active .option-check {
        color: #38bdf8;
        opacity: 1;
    }

    /* สไตล์เมื่อโหมดถูกล็อกเพราะจำนวนผู้เล่นไม่ตรงเงื่อนไข */
    .setting-option-btn.locked {
        opacity: 0.45;
        filter: grayscale(0.8);
        cursor: not-allowed;
        background: rgba(15, 23, 42, 0.3);
        border: 1px dashed rgba(255, 255, 255, 0.15);
    }

    .setting-option-btn.disabled {
        cursor: not-allowed;
    }

    .option-main {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
    }

    .option-icon {
        font-size: 1.15rem;
        width: 26px;
        text-align: center;
    }

    .option-text .title {
        font-size: 0.86rem;
        font-weight: 600;
        color: #fff;
    }

    .option-text .sub {
        font-size: 0.72rem;
        color: #94a3b8;
        line-height: 1.3;
        margin-top: 2px;
    }

    .mode-req-tag {
        font-size: 0.68rem;
        font-weight: 600;
        display: inline-block;
        margin-top: 2px;
    }

    .lock-status-badge {
        font-size: 0.65rem;
        padding: 2px 7px;
        border-radius: 10px;
        font-weight: 600;
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
        white-space: nowrap;
    }

    .option-check {
        opacity: 0;
        transition: opacity 0.2s;
        font-size: 0.95rem;
    }

    /* Difficulty Pills Grid */
    .diff-pill-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }

    .diff-pill {
        background: rgba(15, 23, 42, 0.6);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 0.55rem 0.25rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .diff-pill:hover:not(.disabled) {
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    .diff-pill.active[data-diff="easy"] {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.22);
        box-shadow: 0 0 12px rgba(16, 185, 129, 0.3);
    }

    .diff-pill.active[data-diff="medium"] {
        border-color: #f59e0b;
        background: rgba(245, 158, 11, 0.22);
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.3);
    }

    .diff-pill.active[data-diff="hard"] {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.22);
        box-shadow: 0 0 12px rgba(239, 68, 68, 0.3);
    }

    /* Theme Pill Active Highlight & Badges */
    .diff-pill {
        position: relative;
    }

    .theme-check-badge {
        display: none;
        position: absolute;
        top: 6px;
        right: 8px;
        font-size: 0.85rem;
    }

    #pillThemeClassic.active {
        border-color: #38bdf8 !important;
        background: rgba(56, 189, 248, 0.22) !important;
        box-shadow: 0 0 16px rgba(56, 189, 248, 0.45) !important;
        transform: translateY(-2px);
    }
    #pillThemeClassic.active .diff-name {
        color: #38bdf8 !important;
        font-weight: 800;
        text-shadow: 0 0 8px rgba(56, 189, 248, 0.6);
    }
    #pillThemeClassic.active .theme-check-badge {
        display: block;
        color: #38bdf8;
    }

    #pillThemeRezero.active {
        border-color: #c084fc !important;
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.35), rgba(225, 29, 72, 0.25)) !important;
        box-shadow: 0 0 18px rgba(192, 132, 252, 0.55), inset 0 0 10px rgba(225, 29, 72, 0.25) !important;
        transform: translateY(-2px);
    }
    #pillThemeRezero.active .diff-name {
        color: #f43f5e !important;
        font-weight: 800;
        text-shadow: 0 0 10px rgba(244, 63, 94, 0.7);
    }
    #pillThemeRezero.active .theme-check-badge {
        display: block;
        color: #e11d48;
    }

    .diff-pill .diff-name {
        font-size: 0.85rem;
        font-weight: 700;
        color: #fff;
    }

    .diff-pill .diff-info {
        font-size: 0.68rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    .sync-hint {
        font-size: 0.72rem;
        color: #64748b;
        text-align: center;
        margin-top: 0.6rem;
    }
</style>

<div class="pregame-settings-card">
    <div class="pregame-header">
        <h5><i class="fas fa-sliders-h text-warning"></i>ตั้งค่าก่อนเริ่มเกม</h5>
        <span class="host-role-badge" id="pregameRoleBadge">
            <i class="fas fa-crown"></i> กำลังโหลด...
        </span>
    </div>

    <!-- 1. โหมดการเล่น (Game Mode) -->
    <div class="setting-group">
        <div class="setting-label">
            <span><i class="fas fa-gamepad text-primary me-1"></i>โหมดการเล่น</span>
            <span class="badge bg-primary bg-opacity-25 text-info" style="font-size: 0.7rem;" id="selectedModeBadge">Solo</span>
        </div>
        <div class="option-grid">

            <!-- 👤 Solo Mode -->
            <div class="setting-option-btn active" id="btnModeSolo" data-mode="solo" onclick="selectPreGameMode('solo')">
                <div class="option-main">
                    <span class="option-icon">👤</span>
                    <div class="option-text">
                        <div class="title">เล่นคนเดียว (Solo Classic)</div>
                        <div class="sub">กู้ระเบิดจับเวลาคลาสสิก <span class="mode-req-tag text-info">(สำหรับ 1 คนเท่านั้น)</span></div>
                    </div>
                </div>
                <span class="lock-status-badge" id="badgeSoloLock" style="display: none;"><i class="fas fa-lock"></i> ล็อก (มีเพื่อนในห้อง)</span>
                <i class="fas fa-check-circle option-check" id="checkSolo"></i>
            </div>

            <!-- 🤝 Co-op Mode -->
            <div class="setting-option-btn" id="btnModeCoop" data-mode="coop" onclick="selectPreGameMode('coop')">
                <div class="option-main">
                    <span class="option-icon">🤝</span>
                    <div class="option-text">
                        <div class="title">ช่วยกันกู้ / กู้เดี่ยว 3 ชีวิต (Co-op)</div>
                        <div class="sub">มีพลังชีวิตทีม 3 ดวง (3 HP) <span class="mode-req-tag text-success">(เล่นได้ทั้ง 1-4 คน)</span></div>
                    </div>
                </div>
                <i class="fas fa-check-circle option-check" id="checkCoop"></i>
            </div>

            <!-- 🌟 Versus Mode -->
            <div class="setting-option-btn locked" id="btnModeVersus" data-mode="versus" onclick="selectPreGameMode('versus')">
                <div class="option-main">
                    <span class="option-icon">🌟</span>
                    <div class="option-text">
                        <div class="title">แข่งขันชิงแต้ม (Versus)</div>
                        <div class="sub">สลับตาเปิดช่อง ปักธงได้แต้ม <span class="mode-req-tag text-warning">(ต้องมีเพื่อน 2-4 คน)</span></div>
                    </div>
                </div>
                <span class="lock-status-badge" id="badgeVersusLock"><i class="fas fa-lock"></i> ต้องการเพื่อน 2-4 คน</span>
                <i class="fas fa-check-circle option-check" id="checkVersus"></i>
            </div>

            <!-- 🚩 Hunter Mode -->
            <div class="setting-option-btn locked" id="btnModeHunter" data-mode="hunter" onclick="selectPreGameMode('hunter')">
                <div class="option-main">
                    <span class="option-icon">🚩</span>
                    <div class="option-text">
                        <div class="title">หักเหลี่ยมชิงระเบิด (Flag Hunter)</div>
                        <div class="sub">แข่งกันหาและเคลียร์ระเบิด <span class="mode-req-tag text-warning">(ต้องมีเพื่อน 2-4 คน)</span></div>
                    </div>
                </div>
                <span class="lock-status-badge" id="badgeHunterLock"><i class="fas fa-lock"></i> ต้องการเพื่อน 2-4 คน</span>
                <i class="fas fa-check-circle option-check" id="checkHunter"></i>
            </div>

        </div>
    </div>

    <!-- 2. ระดับความยาก (Difficulty) -->
    <div class="setting-group">
        <div class="setting-label">
            <span><i class="fas fa-layer-group text-success me-1"></i>ระดับความยาก</span>
            <span class="badge bg-success bg-opacity-25 text-success" style="font-size: 0.7rem;" id="selectedDiffBadge">ง่าย</span>
        </div>
        <div class="diff-pill-grid">
            <div class="diff-pill active" data-diff="easy" onclick="selectPreGameDiff('easy')">
                <div class="diff-name text-success">ง่าย</div>
                <div class="diff-info">9x9 (10💣)</div>
            </div>
            <div class="diff-pill" data-diff="medium" onclick="selectPreGameDiff('medium')">
                <div class="diff-name text-warning">ปานกลาง</div>
                <div class="diff-info">16x16 (40💣)</div>
            </div>
            <div class="diff-pill" data-diff="hard" onclick="selectPreGameDiff('hard')">
                <div class="diff-name text-danger">ยาก</div>
                <div class="diff-info">16x30 (99💣)</div>
            </div>
        </div>
    </div>

    <!-- 3. ธีมเกม (Theme) -->
    <div class="setting-group">
        <div class="setting-label">
            <span><i class="fas fa-palette text-info me-1"></i>ธีมภาพและเสียง</span>
            <span class="badge bg-info bg-opacity-25 text-info" style="font-size: 0.7rem;" id="selectedThemeBadge">คลาสสิก</span>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;">
            <div class="diff-pill active" id="pillThemeClassic" onclick="selectPreGameTheme('classic')">
                <div class="diff-name text-info"><i class="fas fa-desktop me-1"></i>คลาสสิก</div>
                <div class="diff-info">Retro Sci-Fi</div>
                <div class="theme-check-badge"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="diff-pill" id="pillThemeRezero" onclick="selectPreGameTheme('rezero')">
                <div class="diff-name" style="color: #c084fc;"><i class="fas fa-gem me-1 text-danger"></i>Re:Zero 🖤</div>
                <div class="diff-info">Anime Rem / Satella</div>
                <div class="theme-check-badge"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="sync-hint">
        <i class="fas fa-sync-alt fa-spin me-1"></i> โหมดจะปลดล็อกอัตโนมัติตามจำนวนผู้เล่นในห้อง (1-4 คน)
    </div>
</div>

<script>
// Pre-Game Settings State
let preGameMode = 'solo';
let preGameDiff = 'easy';
let preGameTheme = 'classic';
let isPreGameHost = true;
let currentLobbyPlayerCount = 1;

// เลือกโหมด
function selectPreGameMode(mode) {
    if (!isPreGameHost) return;

    // ตรวจสอบเงื่อนไขผู้เล่น 1 คน
    if (currentLobbyPlayerCount === 1) {
        if (mode === 'versus' || mode === 'hunter') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'info',
                    title: 'โหมดนี้ต้องมีเพื่อน 2-4 คน!',
                    text: 'กรุณาเชิญเพื่อนเข้าร่วมห้องก่อนเลือกโหมดนี้',
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#1e293b',
                    color: '#fff'
                });
            }
            return;
        }
    } else {
        // เมื่อมีเพื่อนมากกว่า 1 คน ห้ามเลือก Solo
        if (mode === 'solo') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'info',
                    title: 'มีเพื่อนในห้องแล้ว!',
                    text: 'โหมด Solo เล่นได้เฉพาะคนเดียว กรุณาเลือก Versus, Co-op หรือ Hunter',
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#1e293b',
                    color: '#fff'
                });
            }
            return;
        }
    }

    preGameMode = mode;
    updatePreGameUI();
    savePreGameSettings();
}

// เลือกระดับความยาก
function selectPreGameDiff(diff) {
    if (!isPreGameHost) return;
    preGameDiff = diff;
    updatePreGameUI();
    savePreGameSettings();
}

// เลือกธีม
function selectPreGameTheme(theme) {
    if (!isPreGameHost) return;
    preGameTheme = theme;
    updatePreGameUI();
    savePreGameSettings();
}

// อัปเดต UI หน้าตาปุ่มและสถานะล็อก/ปลดล็อก
function updatePreGameUI() {
    const isSolo = (currentLobbyPlayerCount === 1);

    // อัปเดตปุ่ม Solo
    const btnSolo = document.getElementById('btnModeSolo');
    const badgeSolo = document.getElementById('badgeSoloLock');
    if (btnSolo && badgeSolo) {
        btnSolo.classList.toggle('locked', !isSolo);
        btnSolo.classList.toggle('active', preGameMode === 'solo');
        badgeSolo.style.display = isSolo ? 'none' : 'inline-block';
    }

    // อัปเดตปุ่ม Versus
    const btnVersus = document.getElementById('btnModeVersus');
    const badgeVersus = document.getElementById('badgeVersusLock');
    if (btnVersus && badgeVersus) {
        btnVersus.classList.toggle('locked', isSolo);
        btnVersus.classList.toggle('active', preGameMode === 'versus');
        badgeVersus.style.display = isSolo ? 'inline-block' : 'none';
    }

    // อัปเดตปุ่ม Hunter
    const btnHunter = document.getElementById('btnModeHunter');
    const badgeHunter = document.getElementById('badgeHunterLock');
    if (btnHunter && badgeHunter) {
        btnHunter.classList.toggle('locked', isSolo);
        btnHunter.classList.toggle('active', preGameMode === 'hunter');
        badgeHunter.style.display = isSolo ? 'inline-block' : 'none';
    }

    // อัปเดตปุ่ม Co-op (เล่นได้ทั้ง 1 คนและหลายคน)
    const btnCoop = document.getElementById('btnModeCoop');
    if (btnCoop) {
        btnCoop.classList.toggle('active', preGameMode === 'coop');
    }

    // Difficulty Pills
    document.querySelectorAll('.diff-pill-grid .diff-pill').forEach(pill => {
        const d = pill.getAttribute('data-diff');
        pill.classList.toggle('active', d === preGameDiff);
        pill.classList.toggle('disabled', !isPreGameHost);
    });

    // Theme Pills
    const pillClassic = document.getElementById('pillThemeClassic');
    const pillRezero  = document.getElementById('pillThemeRezero');
    if (pillClassic && pillRezero) {
        pillClassic.classList.toggle('active', preGameTheme === 'classic');
        pillRezero.classList.toggle('active', preGameTheme === 'rezero');
        pillClassic.classList.toggle('disabled', !isPreGameHost);
        pillRezero.classList.toggle('disabled', !isPreGameHost);
    }

    // Text Badges
    const modeNames = { versus: 'Versus', coop: 'Co-op (3 HP)', hunter: 'Flag Hunter', solo: 'Solo' };
    const diffNames = { easy: 'ง่าย (9x9)', medium: 'ปานกลาง (16x16)', hard: 'ยาก (16x30)' };
    const themeNames = { classic: 'คลาสสิก', rezero: 'Re:Zero 🖤' };
    const modeEl = document.getElementById('selectedModeBadge');
    const diffEl = document.getElementById('selectedDiffBadge');
    const themeEl = document.getElementById('selectedThemeBadge');
    if (modeEl) modeEl.innerText = modeNames[preGameMode] || preGameMode;
    if (diffEl) diffEl.innerText = diffNames[preGameDiff] || preGameDiff;
    if (themeEl) {
        themeEl.innerText = themeNames[preGameTheme] || preGameTheme;
        if (preGameTheme === 'rezero') {
            themeEl.className = 'badge bg-purple bg-opacity-25 text-warning';
            themeEl.style.backgroundColor = 'rgba(168, 85, 247, 0.25)';
            themeEl.style.color = '#c084fc';
        } else {
            themeEl.className = 'badge bg-info bg-opacity-25 text-info';
            themeEl.style.backgroundColor = '';
            themeEl.style.color = '';
        }
    }

    // Role Badge
    const roleBadge = document.getElementById('pregameRoleBadge');
    if (roleBadge) {
        if (isPreGameHost) {
            roleBadge.className = 'host-role-badge';
            roleBadge.innerHTML = '<i class="fas fa-crown"></i> คุณเป็น Host (ปรับได้)';
        } else {
            roleBadge.className = 'host-role-badge';
            roleBadge.style.borderColor = 'rgba(148, 163, 184, 0.3)';
            roleBadge.style.background = 'rgba(148, 163, 184, 0.15)';
            roleBadge.style.color = '#94a3b8';
            roleBadge.innerHTML = '<i class="fas fa-lock"></i> โฮสต์เป็นผู้ตั้งค่า';
        }
    }
}

// บันทึกการตั้งค่าลง Firebase Lobby
function savePreGameSettings() {
    if (!isPreGameHost || typeof CURRENT_ROOM_ID === 'undefined') return;
    $.post('<?= base_url('player/update_lobby_settings/') ?>' + CURRENT_ROOM_ID, {
        settings: {
            mode: preGameMode,
            difficulty: preGameDiff,
            theme: preGameTheme
        }
    });
}

// รับค่าซิงค์จาก Server (เมื่อ fetchLobbyState() ใน lobby.php ดึงข้อมูลมา)
window.syncPreGameSettingsFromLobby = function(lobby, hostStatus) {
    isPreGameHost = hostStatus;

    const newCount = (lobby && lobby.players && Array.isArray(lobby.players)) ? lobby.players.length : 1;

    // ตรวจสอบการเปลี่ยนจำนวนผู้เล่นเพื่อสลับโหมดอัตโนมัติ
    if (currentLobbyPlayerCount === 1 && newCount > 1) {
        if (preGameMode === 'solo') {
            preGameMode = 'versus';
            if (isPreGameHost) savePreGameSettings();
        }
    } else if (currentLobbyPlayerCount > 1 && newCount === 1) {
        if (preGameMode === 'versus' || preGameMode === 'hunter') {
            preGameMode = 'solo';
            if (isPreGameHost) savePreGameSettings();
        }
    }

    currentLobbyPlayerCount = newCount;

    if (lobby && lobby.settings) {
        if (lobby.settings.mode) {
            const m = lobby.settings.mode;
            if (currentLobbyPlayerCount === 1 && (m === 'versus' || m === 'hunter')) {
                preGameMode = 'solo';
            } else if (currentLobbyPlayerCount > 1 && m === 'solo') {
                preGameMode = 'versus';
            } else {
                preGameMode = m;
            }
        }
        if (lobby.settings.difficulty) preGameDiff = lobby.settings.difficulty;
        if (lobby.settings.theme) preGameTheme = lobby.settings.theme;
    }

    updatePreGameUI();
};

// Global Hook: ส่งการตั้งค่าให้ปุ่มเริ่มเกม
window.getPreGameSettings = function() {
    return {
        mode: preGameMode,
        difficulty: preGameDiff,
        theme: preGameTheme
    };
};

$(document).ready(function() {
    updatePreGameUI();
});
</script>
