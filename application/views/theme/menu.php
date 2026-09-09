<style>
  .sidebar {
    position: fixed;
    top: 57px;
    left: 0;
    width: 220px;
    height: calc(100vh - 57px);
    background: rgba(22, 33, 62, 0.95);
    backdrop-filter: blur(14px);
    border-right: 1px solid rgba(255, 255, 255, 0.08);
    overflow-y: auto;
    padding: 0.5rem 0;
    font-family: 'Sarabun', sans-serif;
    font-size: 14px;
    z-index: 999;
  }

  .sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.75rem 1.1rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.2s;
  }

  .sidebar a i {
    color: #818cf8;
    width: 18px;
    text-align: center;
  }

  .sidebar a:hover {
    background: rgba(255, 255, 255, 0.07);
    color: #fff;
  }

  .sidebar a.active {
    border-left-color: #818cf8;
    background: rgba(129, 140, 248, 0.15);
    color: #c7d2fe;
    font-weight: 600;
  }

  .sidebar a.active i {
    color: #a5b4fc;
  }

  .sidebar .child a {
    padding-left: 2.3rem;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.6);
    background: rgba(15, 23, 42, 0.5);
  }

  .sidebar .child a:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.08);
  }

  .sidebar .toggle {
    display: flex;
    justify-content: space-between;
  }

  .sidebar .toggle .arrow {
    font-size: 10px;
    transition: transform 0.2s;
    color: rgba(255, 255, 255, 0.4);
  }

  .sidebar .toggle[aria-expanded="true"] .arrow {
    transform: rotate(180deg);
  }

  .sidebar hr {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    margin: 0.3rem 1rem;
  }
</style>


<?php
$active = isset($active_menu) ? $active_menu : 'admin';

$menus = [
    [
        'label'    => 'แดชบอร์ดจัดการข้อมูล',
        'icon'     => '<i class="fas fa-gauge-high"></i>',
        'url'      => base_url('home'),
        'key'      => 'admin',
        'children' => []
    ],
    [
        'label'    => 'ไปหน้าเล่นเกม (Hub)',
        'icon'     => '<i class="fas fa-gamepad"></i>',
        'url'      => base_url('player'),
        'key'      => 'player',
        'children' => []
    ],
];
?>


<div class="sidebar">
<!--1 วนลูปในเมนูเพื่อที่จะดึงค่าแต่ละตัวใน menu-->
  <?php foreach ($menus as $menu):
	//2 ถ้า children ไม่ว่างให้เก็บไว้ที่ haschildren
    $hasChildren = !empty($menu['children']);

    //3 เช็คว่า group นี้ active ไหม (ตัวเองหรือลูก) ถ้า !$groupActive && $hasChildren ไม่ตรงกันให้ลูปเข้าไปเช็คในตารางลูก
    $groupActive = ($active === $menu['key']);
    if (!$groupActive && $hasChildren) {
        foreach ($menu['children'] as $c) {
            if ($c['key'] === $active) { $groupActive = true; break; }
        }
    }
  ?>
		
    <?php if ($hasChildren): ?>
				<!--4 ถ้าhasChildren มีค่าเก็บอยู่ ให้แสดงเป็น dropdown -->
      <a class="toggle <?= $groupActive ? 'active' : '' ?>"
         data-bs-toggle="collapse"
         href="#grp-<?= $menu['key'] ?>"
         aria-expanded="<?= $groupActive ? 'true' : 'false' ?>">
        <span><?= $menu['icon'] ?> <?= $menu['label'] ?></span>
        <i class="fas fa-chevron-down arrow"></i>
      </a>

      <div class="collapse <?= $groupActive ? 'show' : '' ?>" id="grp-<?= $menu['key'] ?>">
        <div class="child">
          <?php foreach ($menu['children'] as $child): ?>
            <a href="<?= $child['url'] ?>"
               class="<?= $active === $child['key'] ? 'active' : '' ?>">
              <?= $child['icon'] ?> <?= $child['label'] ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
						<!--5 แต่ถ้าไม่ใช่ให้ แสดงเมนูปกติ-->
    <?php else: ?>

      <a href="<?= $menu['url'] ?>"
         class="<?= $active === $menu['key'] ? 'active' : '' ?>">
        <?= $menu['icon'] ?> <?= $menu['label'] ?>
      </a>

    <?php endif; ?>

  <?php endforeach; ?>
</div>
