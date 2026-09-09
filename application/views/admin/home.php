<?php $now = time(); ?>
<style>
    .text-muted {
        --bs-text-opacity: 1 !important;
        color: #ffffff !important;
    }
    .admin-stat-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .admin-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }
    .admin-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .admin-nav-tabs .nav-link {
        color: #9ca3af;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.8rem 1.4rem;
        font-weight: 500;
        transition: all 0.2s;
        border-radius: 0;
    }
    .admin-nav-tabs .nav-link:hover {
        color: #c7d2fe;
    }
    .admin-nav-tabs .nav-link.active {
        color: #818cf8;
        background: transparent;
        border-bottom: 2px solid #818cf8;
        font-weight: 600;
    }
    .table-dark-custom {
        --bs-table-bg: transparent;
        --bs-table-color: #e0e0e0;
        --bs-table-border-color: rgba(255, 255, 255, 0.08);
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-dark-custom thead th {
        background: rgba(255, 255, 255, 0.04);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: #a5b4fc;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px;
    }
    .table-dark-custom tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .table-dark-custom tbody tr:hover td {
        background: rgba(255, 255, 255, 0.03);
    }
    .modal-dark .modal-content {
        background: rgba(26, 26, 46, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        color: #e0e0e0;
    }
    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .modal-dark .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .form-control-dark {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        border-radius: 10px;
        padding: 10px 14px;
    }
    .form-control-dark:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: #818cf8;
        color: #fff;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.25);
    }
</style>

<div class="container-fluid px-4 py-4">

    <!-- Header & Action Buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-white">
                <i class="fas fa-shield-halved text-warning me-2"></i>ระบบจัดการข้อมูลส่วนกลาง (Admin Dashboard)
            </h2>
            <p class="text-muted mb-0">ดูและจัดการฐานข้อมูลทั้งหมดในระบบ: บัญชีผู้ใช้, สิทธิ์การใช้งาน, สถานะออนไลน์, ห้องเล่นเกม และประวัติการทำงาน</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('player') ?>" class="btn btn-outline-primary rounded-pill px-3">
                <i class="fas fa-gamepad me-1"></i>กลับไปหน้าเลือกเกม
            </a>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-1"></i>เพิ่มผู้ใช้ใหม่
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background: rgba(99, 102, 241, 0.2); color: #818cf8;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-muted small">ผู้ใช้ทั้งหมด</div>
                    <div class="h4 fw-bold mb-0 text-white"><?= $stats['total_users'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                    <i class="fas fa-crown"></i>
                </div>
                <div>
                    <div class="text-muted small">แอดมิน</div>
                    <div class="h4 fw-bold mb-0 text-warning"><?= $stats['admin_count'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div>
                    <div class="text-muted small">ผู้เล่น</div>
                    <div class="h4 fw-bold mb-0 text-info"><?= $stats['player_count'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">
                    <i class="fas fa-signal"></i>
                </div>
                <div>
                    <div class="text-muted small">ออนไลน์ขณะนี้</div>
                    <div class="h4 fw-bold mb-0 text-success"><?= $stats['online_count'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background: rgba(236, 72, 153, 0.2); color: #f472b6;">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <div class="text-muted small">ห้องที่เปิดอยู่</div>
                    <div class="h4 fw-bold mb-0 text-danger"><?= $stats['active_rooms'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="admin-stat-card">
                <div class="admin-stat-icon" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa;">
                    <i class="fas fa-dice"></i>
                </div>
                <div>
                    <div class="text-muted small">เกมในระบบ</div>
                    <div class="h4 fw-bold mb-0 text-purple"><?= $stats['games_count'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container with Tabs -->
    <div class="page-card p-0 overflow-hidden" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px;">
        <!-- Tabs Header -->
        <ul class="nav admin-nav-tabs border-bottom border-secondary border-opacity-25 px-3 pt-2" id="adminTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#tab-users" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>จัดการผู้ใช้และสิทธิ์ (<?= count($users) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="presence-tab" data-bs-toggle="tab" data-bs-target="#tab-presence" type="button" role="tab">
                    <i class="fas fa-wifi me-2"></i>สถานะออนไลน์ & Presence (<?= count($presence) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#tab-rooms" type="button" role="tab">
                    <i class="fas fa-door-open me-2"></i>ห้องล็อบบี้ & แมตช์เกม (<?= count($lobbies) + count($game_rooms) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="chats-tab" data-bs-toggle="tab" data-bs-target="#tab-chats" type="button" role="tab">
                    <i class="fas fa-comments me-2"></i>แชท & คำเชิญ (<?= count($chats) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="games-tab" data-bs-toggle="tab" data-bs-target="#tab-games" type="button" role="tab">
                    <i class="fas fa-dice me-2"></i>รายการเกม (<?= count($games) ?>)
                </button>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content p-3 p-md-4" id="adminTabsContent">

            <!-- TAB 1: จัดการผู้ใช้และบทบาท -->
            <div class="tab-pane fade show active" id="tab-users" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-white"><i class="fas fa-table me-2 text-primary"></i>ตารางผู้ใช้ในฐานข้อมูล (Collection: <code>user</code>)</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus me-1"></i>เพิ่มผู้ใช้
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark-custom align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>ชื่อผู้ใช้ (Username)</th>
                                <th>บทบาท (Role)</th>
                                <th>รหัสผ่านจริง (Real Pass)</th>
                                <th>Firebase Key</th>
                                <th class="text-end" style="width: 180px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): foreach ($users as $i => $u): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(129,140,248,0.2);color:#818cf8;display:flex;align-items:center;justify-content:center;font-size:13px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong class="text-white"><?= htmlspecialchars($u['username']) ?></strong>
                                            <?php if ($u['username'] === $this->session->userdata('username')): ?>
                                                <span class="badge bg-secondary ms-1 small">คุณ</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (isset($u['role']) && $u['role'] === 'admin'): ?>
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-crown me-1"></i>Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary px-2 py-1"><i class="fas fa-gamepad me-1"></i>Player</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code class="text-info"><?= htmlspecialchars(!empty($u['real_pass']) ? $u['real_pass'] : '••••••••') ?></code>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($u['id']) ?></small></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning me-1" onclick="openEditUserModal('<?= htmlspecialchars($u['id']) ?>', '<?= htmlspecialchars($u['username']) ?>', '<?= htmlspecialchars($u['role']) ?>', '<?= htmlspecialchars($u['real_pass']) ?>')">
                                        <i class="fas fa-edit me-1"></i>แก้ไข
                                    </button>
                                    <?php if ($u['username'] !== $this->session->userdata('username')): ?>
                                        <a href="<?= base_url('home/delete_user/' . $u['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('คุณแน่ใจว่าต้องการลบผู้ใช้ <?= htmlspecialchars($u['username']) ?> ใช่หรือไม่?')">
                                            <i class="fas fa-trash me-1"></i>ลบ
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูลผู้ใช้ในระบบ</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: สถานะออนไลน์ (Presence) -->
            <div class="tab-pane fade" id="tab-presence" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-white"><i class="fas fa-signal me-2 text-success"></i>สถานะเซสชัน & Presence (Collection: <code>presence</code>)</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark-custom align-middle">
                        <thead>
                            <tr>
                                <th>ชื่อผู้ใช้</th>
                                <th>สถานะ (Status)</th>
                                <th>เกม / หน้าปัจจุบัน</th>
                                <th>รหัสห้อง (Room ID)</th>
                                <th>อัปเดตล่าสุด</th>
                                <th class="text-end">การทำงาน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($presence)): foreach ($presence as $p): 
                                $p_user = isset($p['username']) ? $p['username'] : (isset($p['firebase_key']) ? $p['firebase_key'] : '-');
                                $p_status = isset($p['status']) ? $p['status'] : 'offline';
                                $p_active = isset($p['last_active']) ? (int)$p['last_active'] : 0;
                                $is_active = ($p_status !== 'offline' && ($now - $p_active <= 300));
                            ?>
                            <tr>
                                <td><strong class="text-white"><?= htmlspecialchars($p_user) ?></strong></td>
                                <td>
                                    <?php if ($is_active && $p_status === 'playing'): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-dice me-1"></i>กำลังเล่นเกม</span>
                                    <?php elseif ($is_active): ?>
                                        <span class="badge bg-success"><i class="fas fa-circle me-1" style="font-size:8px;"></i>ออนไลน์</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="fas fa-circle me-1" style="font-size:8px;"></i>ออฟไลน์</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars(!empty($p['game']) ? $p['game'] : '-') ?></td>
                                <td><code><?= htmlspecialchars(!empty($p['room_id']) ? $p['room_id'] : '-') ?></code></td>
                                <td><small class="text-muted"><?= $p_active > 0 ? date('d/m/Y H:i:s', $p_active) : '-' ?></small></td>
                                <td class="text-end">
                                    <a href="<?= base_url('home/reset_presence/' . $p_user) ?>" class="btn btn-sm btn-outline-secondary me-1" onclick="return confirm('รีเซ็ตสถานะของ <?= htmlspecialchars($p_user) ?> ให้เป็นออฟไลน์หรือไม่?')">
                                        <i class="fas fa-power-off me-1"></i>บังคับออฟไลน์
                                    </a>
                                    <a href="<?= base_url('home/delete_presence/' . $p_user) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ลบข้อมูล Presence ของ <?= htmlspecialchars($p_user) ?> ออกจากระบบหรือไม่?')">
                                        <i class="fas fa-trash me-1"></i>ลบ
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">ไม่มีข้อมูล presence</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: ห้องล็อบบี้และแมตช์เกม -->
            <div class="tab-pane fade" id="tab-rooms" role="tabpanel">
                <div class="row g-4">
                    <!-- 1. ผู้เล่นที่กำลังเล่นเกมอยู่ในขณะนี้ (ข้ามทุกเกม) -->
                    <div class="col-12">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-white mb-0"><i class="fas fa-gamepad me-2 text-warning"></i>ผู้เล่นที่กำลังเล่นเกมอยู่ในขณะนี้ (<?= count($playing_users) ?> คน)</h5>
                                <span class="badge bg-warning text-dark"><i class="fas fa-circle text-danger me-1"></i>เรียลไทม์</span>
                            </div>
                            <?php if (!empty($playing_users)): ?>
                                <div class="table-responsive">
                                    <table class="table table-dark-custom small align-middle">
                                        <thead>
                                            <tr>
                                                <th>ผู้เล่น (User)</th>
                                                <th>เกมที่กำลังเล่น</th>
                                                <th>รหัสห้อง (Room ID)</th>
                                                <th>อัปเดตล่าสุด</th>
                                                <th class="text-end">การทำงาน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($playing_users as $pu): 
                                                $pu_name = isset($pu['username']) ? $pu['username'] : (isset($pu['firebase_key']) ? $pu['firebase_key'] : '-');
                                                $pu_game = !empty($pu['game']) ? $pu['game'] : 'เกมกระดาน';
                                                $pu_room = !empty($pu['room_id']) ? $pu['room_id'] : '-';
                                                $pu_active = isset($pu['last_active']) ? (int)$pu['last_active'] : 0;
                                            ?>
                                            <tr>
                                                <td><strong class="text-white"><i class="fas fa-user-astronaut me-2 text-primary"></i><?= htmlspecialchars($pu_name) ?></strong></td>
                                                <td><span class="badge bg-info text-dark fs-6"><?= htmlspecialchars($pu_game) ?></span></td>
                                                <td><code><?= htmlspecialchars($pu_room) ?></code></td>
                                                <td><small class="text-muted"><?= $pu_active > 0 ? date('d/m/Y H:i:s', $pu_active) : '-' ?></small></td>
                                                <td class="text-end">
                                                    <a href="<?= base_url('home/reset_presence/' . $pu_name) ?>" class="btn btn-sm btn-outline-warning py-0 px-2" onclick="return confirm('บังคับให้ออกจากเกมและรีเซ็ตสถานะเป็นออฟไลน์หรือไม่?')">
                                                        <i class="fas fa-power-off me-1"></i>บังคับออก
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted small"><i class="fas fa-coffee me-1"></i> ขณะนี้ยังไม่มีผู้เล่นที่กำลังแข่งขันในเกมใดๆ</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 2. ห้องล็อบบี้เกม (Lobbies) -->
                    <div class="col-12 col-lg-5">
                        <div class="p-3 rounded-4 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-white mb-0"><i class="fas fa-door-open me-2 text-warning"></i>ห้องล็อบบี้รอเพื่อน (Collection: <code>lobbies</code>)</h5>
                                <a href="<?= base_url('home/clear_all_lobbies') ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการล้างห้องล็อบบี้ทั้งหมด?')">
                                    <i class="fas fa-trash-can me-1"></i>ล้างทั้งหมด
                                </a>
                            </div>
                            <?php if (!empty($lobbies)): ?>
                                <div class="table-responsive">
                                    <table class="table table-dark-custom small">
                                        <thead>
                                            <tr>
                                                <th>รหัสห้อง</th>
                                                <th>เกม</th>
                                                <th>Host</th>
                                                <th>ผู้เล่น</th>
                                                <th class="text-end">ลบ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lobbies as $l): ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars(isset($l['id']) ? $l['id'] : (isset($l['firebase_key']) ? $l['firebase_key'] : '-')) ?></code></td>
                                                <td><span class="badge bg-primary"><?= htmlspecialchars(isset($l['game_key']) ? $l['game_key'] : '-') ?></span></td>
                                                <td><strong class="text-white"><?= htmlspecialchars(isset($l['host']) ? $l['host'] : '-') ?></strong></td>
                                                <td><?= isset($l['players']) && is_array($l['players']) ? count($l['players']) : 1 ?> คน</td>
                                                <td class="text-end">
                                                    <a href="<?= base_url('home/delete_lobby/' . (isset($l['id']) ? $l['id'] : (isset($l['firebase_key']) ? $l['firebase_key'] : ''))) ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('ลบห้องล็อบบี้นี้?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted small">ไม่มีห้องล็อบบี้ที่เปิดค้างอยู่</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 3. ห้องเกมที่กำลังแข่งขันทั้งหมด (All Active Game Rooms) -->
                    <div class="col-12 col-lg-7">
                        <div class="p-3 rounded-4 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                            <h5 class="text-white mb-3"><i class="fas fa-dice me-2 text-primary"></i>ห้องเกมที่กำลังแข่งขันทั้งหมด (ทุกเกมในระบบ)</h5>
                            <?php if (!empty($game_rooms)): ?>
                                <div class="table-responsive">
                                    <table class="table table-dark-custom small">
                                        <thead>
                                            <tr>
                                                <th>รหัสห้อง</th>
                                                <th>เกม</th>
                                                <th>ผู้เล่นในห้อง</th>
                                                <th>สถานะ</th>
                                                <th class="text-end">ลบ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($game_rooms as $r): 
                                                $r_type = isset($r['game_type']) ? $r['game_type'] : 'xo';
                                                $r_id = isset($r['room_id']) ? $r['room_id'] : (isset($r['id']) ? $r['id'] : (isset($r['firebase_key']) ? $r['firebase_key'] : ''));
                                                $r_status = isset($r['status']) ? $r['status'] : 'playing';

                                                // คำนวณผู้เล่น
                                                $p_label = '-';
                                                if (isset($r['player_x']) || isset($r['player_o'])) {
                                                    $p_label = '<span class="text-primary fw-bold">' . htmlspecialchars(isset($r['player_x']) ? $r['player_x'] : '-') . '</span> vs <span class="text-danger fw-bold">' . htmlspecialchars(isset($r['player_o']) ? $r['player_o'] : 'รอ...') . '</span>';
                                                } elseif (isset($r['players']) && is_array($r['players'])) {
                                                    $p_label = implode(', ', $r['players']);
                                                }
                                            ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars($r_id) ?></code></td>
                                                <td><span class="badge bg-secondary"><?= strtoupper(htmlspecialchars($r_type)) ?></span></td>
                                                <td><?= $p_label ?></td>
                                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r_status) ?></span></td>
                                                <td class="text-end">
                                                    <a href="<?= base_url('home/delete_room/' . $r_type . '/' . $r_id) ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('ลบห้องเกมนี้?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted small">ไม่มีห้องเกมที่กำลังแข่งขันในขณะนี้</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 4. สถิติจำนวนครั้งที่ชนะในแต่ละเกม (รองรับ Dropdown เลือกเกม) -->
                    <div class="col-12">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div>
                                    <h5 class="text-white mb-0"><i class="fas fa-trophy me-2 text-warning"></i>สถิติจำนวนครั้งที่ชนะในเกม (Leaderboard)</h5>
                                    <small class="text-muted">เลือกเกมเพื่อดูจำนวนครั้งที่ชนะของแต่ละผู้เล่น</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <label class="text-white small mb-0"><i class="fas fa-filter me-1 text-primary"></i>เลือกเกม:</label>
                                    <select id="select_stats_game" class="form-select form-select-sm form-control-dark" style="width: auto; min-width: 200px;" onchange="loadGameWinStats(this.value)">
                                        <?php foreach ($games as $k => $g): ?>
                                            <option value="<?= $k ?>" <?= ($k === 'tictactoe' || $k === 'xo') ? 'selected' : '' ?>>
                                                <?= $g['icon'] ?> <?= htmlspecialchars($g['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-dark-custom small align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 70px;" class="text-center">อันดับ</th>
                                            <th>ผู้ใช้ (Username)</th>
                                            <th>ชนะ (Wins)</th>
                                            <th>แพ้ (Losses)</th>
                                            <th>เล่นทั้งหมด (Played)</th>
                                            <th>ชนะล่าสุดเมื่อ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stats_tbody">
                                        <?php if (!empty($xo_scores)): 
                                            $rank = 1;
                                            foreach ($xo_scores as $s): 
                                                $s_user = isset($s['username']) ? $s['username'] : (isset($s['firebase_key']) ? $s['firebase_key'] : '-');
                                                $s_wins = isset($s['wins']) ? (int)$s['wins'] : 0;
                                                $s_losses = isset($s['losses']) ? (int)$s['losses'] : 0;
                                                $s_played = isset($s['played']) ? (int)$s['played'] : ($s_wins + $s_losses);
                                                $s_last = isset($s['last_win_at']) ? (int)$s['last_win_at'] : 0;
                                                $medal = ($rank === 1) ? '🥇 ' : (($rank === 2) ? '🥈 ' : (($rank === 3) ? '🥉 ' : ''));
                                        ?>
                                        <tr>
                                            <td class="text-center"><strong><?= $medal . $rank ?></strong></td>
                                            <td><strong class="text-white"><i class="fas fa-user me-2 text-primary"></i><?= htmlspecialchars($s_user) ?></strong></td>
                                            <td><span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 fs-6 px-3 py-1"><i class="fas fa-trophy me-1"></i><?= $s_wins ?> ครั้ง</span></td>
                                            <td><span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 fs-6 px-3 py-1"><i class="fas fa-times me-1"></i><?= $s_losses ?> ครั้ง</span></td>
                                            <td><span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 fs-6 px-3 py-1"><i class="fas fa-gamepad me-1"></i><?= $s_played ?> เกม</span></td>
                                            <td><span class="text-muted"><?= $s_last > 0 ? date('d/m/Y H:i:s', $s_last) : '-' ?></span></td>
                                        </tr>
                                        <?php $rank++; endforeach; else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted small">ยังไม่มีบันทึกสถิติการชนะในเกมนี้</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: ข้อความและคำเชิญ -->
            <div class="tab-pane fade" id="tab-chats" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-white mb-0"><i class="fas fa-comments text-info me-2"></i>ห้องแชท (Chats)</h5>
                                <?php if (!empty($chats)): ?>
                                    <a href="<?= base_url('home/clear_chats') ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('คุณแน่ใจว่าต้องการล้างข้อความแชททั้งหมดหรือไม่?')">
                                        <i class="fas fa-trash me-1"></i>ล้างแชททั้งหมด
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($chats)): ?>
                                <ul class="list-group list-group-flush bg-transparent small">
                                    <?php foreach ($chats as $c): 
                                        $channel_name = isset($c['firebase_key']) ? $c['firebase_key'] : 'chat';
                                        $msg_count = isset($c['messages']) ? count($c['messages']) : 0;
                                    ?>
                                    <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span><i class="fas fa-hashtag me-2 text-muted"></i><?= htmlspecialchars($channel_name) ?></span>
                                            <span class="badge bg-secondary ms-2"><?= $msg_count ?> ข้อความ</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-info py-0 px-2" onclick="viewChatMessages('<?= htmlspecialchars($channel_name) ?>')">
                                            <i class="fas fa-eye me-1"></i>ดูข้อความ
                                        </button>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted small">ยังไม่มีประวัติการสนทนาในระบบ</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-white mb-0"><i class="fas fa-paper-plane text-primary me-2"></i>ประวัติคำเชิญ (Invitations)</h5>
                                <?php if (!empty($invites)): ?>
                                    <a href="<?= base_url('home/clear_invites') ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ล้างคำเชิญทั้งหมดหรือไม่?')">
                                        <i class="fas fa-trash me-1"></i>ล้างคำเชิญ
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($invites)): ?>
                                <ul class="list-group list-group-flush bg-transparent small">
                                    <?php foreach ($invites as $inv): ?>
                                    <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars(isset($inv['from']) ? $inv['from'] : '') ?></strong>
                                            <span class="text-muted">ชวน</span>
                                            <strong><?= htmlspecialchars(isset($inv['to']) ? $inv['to'] : '') ?></strong>
                                            <span class="badge bg-primary ms-1"><?= htmlspecialchars(isset($inv['game_name']) ? $inv['game_name'] : '') ?></span>
                                        </div>
                                        <span class="badge bg-secondary"><?= htmlspecialchars(isset($inv['status']) ? $inv['status'] : '') ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted small">ไม่มีคำเชิญค้างอยู่ในระบบ</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: รายการเกมทั้งหมด -->
            <div class="tab-pane fade" id="tab-games" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="text-white mb-0"><i class="fas fa-dice text-warning me-2"></i>รายการเกมในระบบ (โหลดจาก <code>application/views/menugame.php</code>)</h5>
                        <small class="text-muted">สามารถเพิ่มเกมใหม่ๆ ได้โดยตรงที่ไฟล์ <code>views/menugame.php</code></small>
                    </div>
                </div>

                <div class="row g-3">
                    <?php foreach ($games as $key => $g): ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="p-3 rounded-4 h-100" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-top: 4px solid <?= $g['color'] ?>;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span style="font-size: 2rem;"><?= $g['icon'] ?></span>
                                <div>
                                    <h6 class="text-white mb-0 fw-bold"><?= $g['name'] ?></h6>
                                    <small class="badge bg-dark border border-secondary"><?= $key ?></small>
                                </div>
                            </div>
                            <p class="text-muted small mb-3"><?= $g['desc'] ?></p>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-light"><i class="fas fa-users me-1"></i><?= $g['players'] ?></span>
                                <?php if (isset($g['status']) && $g['status'] === 'ready'): ?>
                                    <span class="badge bg-success">พร้อมเล่น</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">เร็วๆ นี้</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal: เพิ่มผู้ใช้ใหม่ -->
<div class="modal fade modal-dark" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('home/add_user') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus text-primary me-2"></i>เพิ่มผู้ใช้ใหม่ในระบบ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">ชื่อผู้ใช้ (Username):</label>
                        <input type="text" name="username" class="form-control form-control-dark" required placeholder="เช่น player99">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">รหัสผ่าน (Password):</label>
                        <input type="password" name="password" class="form-control form-control-dark" required placeholder="กำหนดรหัสผ่าน">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">บทบาท (Role):</label>
                        <select name="role" class="form-select form-control-dark">
                            <option value="player" selected>ผู้เล่นทั่วไป (Player)</option>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: แก้ไขผู้ใช้ -->
<div class="modal fade modal-dark" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= base_url('home/edit_user') ?>" method="post">
                <input type="hidden" name="id" id="edit_user_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit text-warning me-2"></i>แก้ไขข้อมูลผู้ใช้</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white">ชื่อผู้ใช้ (Username):</label>
                        <input type="text" name="username" id="edit_username" class="form-control form-control-dark" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">รหัสผ่านใหม่ (เว้นว่างไว้ถ้าไม่ต้องการเปลี่ยน):</label>
                        <input type="password" name="password" id="edit_password" class="form-control form-control-dark" placeholder="กรอกรหัสผ่านใหม่หากต้องการเปลี่ยน">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white">บทบาท (Role):</label>
                        <select name="role" id="edit_role" class="form-select form-control-dark">
                            <option value="player">ผู้เล่นทั่วไป (Player)</option>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning px-4"><i class="fas fa-save me-1"></i>บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: ดูข้อความแชท -->
<div class="modal fade modal-dark" id="viewChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: rgba(26, 26, 46, 0.98); border: 1px solid rgba(255,255,255,0.15); border-radius: 20px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h5 class="modal-title text-white"><i class="fas fa-comments text-info me-2"></i>ข้อความในช่อง: <span id="chat_modal_channel" class="text-info"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height: 450px; overflow-y: auto;">
                <div id="chat_modal_body">
                    <div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดข้อความ...</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
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

function openEditUserModal(id, username, role, realpass) {
    $('#edit_user_id').val(id);
    $('#edit_username').val(username);
    $('#edit_role').val(role || 'player');
    $('#edit_password').val('');
    $('#editUserModal').modal('show');
}

function viewChatMessages(channel) {
    $('#chat_modal_channel').text(channel);
    $('#chat_modal_body').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดข้อความ...</div>');
    $('#viewChatModal').modal('show');

    $.get('<?= base_url('home/get_chat_messages/') ?>' + channel, function(msgs) {
        if (!msgs || msgs.length === 0) {
            $('#chat_modal_body').html('<div class="text-center py-4 text-muted">ไม่มีข้อความในช่องนี้</div>');
            return;
        }
        let html = '<div class="d-flex flex-column gap-2">';
        msgs.forEach(function(m) {
            let sender = m.sender || 'Unknown';
            let text = m.message || '';
            let timeStr = m.time ? new Date(m.time * 1000).toLocaleString('th-TH') : '';
            html += `
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.05); border-left: 4px solid #818cf8;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-info"><i class="fas fa-user me-1"></i>${escapeHtml(sender)}</strong>
                    <small class="text-muted">${timeStr}</small>
                </div>
                <div class="text-white" style="word-break: break-word;">${escapeHtml(text)}</div>
            </div>`;
        });
        html += '</div>';
    }, 'json').fail(function() {
        $('#chat_modal_body').html('<div class="text-center py-4 text-danger">เกิดข้อผิดพลาดในการโหลดข้อความ</div>');
    });
}

function loadGameWinStats(gameKey) {
    $('#stats_tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดสถิติ...</td></tr>');
    $.get('<?= base_url('home/get_game_scores/') ?>' + gameKey, function(data) {
        if (!data || data.length === 0) {
            $('#stats_tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted small">ยังไม่มีบันทึกสถิติการชนะในเกมนี้</td></tr>');
            return;
        }
        let html = '';
        data.forEach(function(s, idx) {
            let uName = s.username || s.firebase_key || '-';
            let wins = s.wins || 0;
            let losses = s.losses || 0;
            let played = s.played || (wins + losses);
            let lastWin = s.last_win_at ? new Date(s.last_win_at * 1000).toLocaleString('th-TH') : '-';
            let medal = (idx === 0) ? '🥇 ' : ((idx === 1) ? '🥈 ' : ((idx === 2) ? '🥉 ' : ''));
            html += `
            <tr>
                <td class="text-center"><strong>${medal}${idx + 1}</strong></td>
                <td><strong class="text-white"><i class="fas fa-user me-2 text-primary"></i>${escapeHtml(uName)}</strong></td>
                <td><span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 fs-6 px-3 py-1"><i class="fas fa-trophy me-1"></i>${wins} ครั้ง</span></td>
                <td><span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 fs-6 px-3 py-1"><i class="fas fa-times me-1"></i>${losses} ครั้ง</span></td>
                <td><span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 fs-6 px-3 py-1"><i class="fas fa-gamepad me-1"></i>${played} เกม</span></td>
                <td><span class="text-muted">${lastWin}</span></td>
            </tr>`;
        });
        $('#stats_tbody').html(html);
    }, 'json').fail(function() {
        $('#stats_tbody').html('<tr><td colspan="6" class="text-center py-4 text-danger">เกิดข้อผิดพลาดในการโหลดสถิติ</td></tr>');
    });
}
</script>
