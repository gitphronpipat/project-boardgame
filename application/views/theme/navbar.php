<style>
  .lumiere-navbar {
    background: rgba(26, 26, 46, 0.85);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
  }
  .lumiere-navbar .brand {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: #fff !important;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .lumiere-navbar .brand i {
    color: #818cf8;
  }
  .lumiere-navbar .brand .admin-tag {
    font-size: 11px;
    font-weight: 600;
    background: rgba(129, 140, 248, 0.2);
    color: #a5b4fc;
    padding: 2px 8px;
    border-radius: 6px;
    margin-left: 6px;
  }
  .lumiere-navbar .user-badge {
    background: rgba(129, 140, 248, 0.15);
    color: #a5b4fc;
    font-size: 14px;
    letter-spacing: 0.05em;
    padding: 6px 14px;
    border-radius: 20px;
    cursor: default;
    user-select: none;
  }
  .lumiere-navbar .btn-logout {
    font-size: 14px;
    letter-spacing: 0.05em;
    color: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 20px;
    padding: 6px 16px;
    text-decoration: none;
    transition: all 0.2s;
  }
  .lumiere-navbar .btn-logout:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.3);
  }
</style>

<?php $current_url = current_url(); ?>
<nav class="lumiere-navbar navbar navbar-expand px-4">
  <a class="brand navbar-brand" href="<?= base_url('/') ?>">
    <i class="fas fa-gamepad"></i> BOARD GAME <span class="admin-tag">ADMIN</span>
  </a>

  <div class="ms-auto d-flex align-items-center gap-3">
      <a href="<?= base_url('player') ?>" class="btn btn-sm" style="background: rgba(129, 140, 248, 0.2); border: 1px solid rgba(129, 140, 248, 0.4); color: #c7d2fe; border-radius: 20px; padding: 5px 14px; font-size: 13px; text-decoration: none;">
        <i class="fas fa-gamepad me-1"></i> ไปหน้าเลือกเล่นเกม
      </a>

      <span class="user-badge">
        <i class="fa-solid fa-circle-user me-1"></i>
        <?= $this->session->userdata('username') ?>
      </span>

      <a href="<?= base_url('auth/logout') ?>" class="btn-logout">
        <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
      </a>
  </div>
</nav>
