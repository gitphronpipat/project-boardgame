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

        /* Page header */
        .page-header {
            text-align: left;
            padding: 1rem 0.5rem;
        }
        .page-header h1 {
            font-size: 1.85rem;
            font-weight: 700;
            color: #fff;
        }
        .page-header h1 i { color: #818cf8; }
        .page-header p {
            color: rgba(255,255,255,0.7);
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* Game Grid */
        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 1.2rem;
            padding: 0 0 2rem 0;
        }
        .game-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: #e0e0e0;
            position: relative;
            overflow: hidden;
        }
        .game-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: var(--card-color, #818cf8);
        }
        .game-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
            box-shadow: 0 12px 32px rgba(0,0,0,0.3);
            color: #fff;
        }
        .game-card .game-icon {
            font-size: 3rem;
            margin-bottom: 0.8rem;
            display: block;
        }
        .game-card .game-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        .game-card .game-desc {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
            margin-bottom: 0.6rem;
        }
        .game-card .game-players {
            font-size: 0.75rem;
            background: rgba(255,255,255,0.08);
            display: inline-block;
            padding: 3px 12px;
            border-radius: 12px;
            color: rgba(255,255,255,0.6);
        }
        .game-card .play-btn {
            display: none;
            margin-top: 0.8rem;
        }
        .game-card:hover .play-btn {
            display: inline-block;
            background: var(--card-color, #818cf8);
            color: #fff;
            border: none;
            padding: 6px 20px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .game-card.coming-soon:hover .play-btn {
            display: inline-block;
            background: rgba(245, 158, 11, 0.25);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.2);
        }

        @media (max-width: 576px) {
            .game-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
                padding: 0 0.8rem 2rem;
            }
            .game-card { padding: 1rem; }
            .game-card .game-icon { font-size: 2.2rem; }
        }
    </style>

    <title><?php echo $title; ?></title>
</head>
<body>

<!-- Unified Dynamic Navbar -->
<?php $this->load->view('player/components/navbar', ['nav_mode' => 'hub']); ?>

<!-- Notify -->
<?php $this->load->view('theme/notify'); ?>

<!-- Main Content: Game Grid (Left) & Ranking Leaderboard (Right) -->
<div class="container-fluid px-3 px-xl-4 py-3">
    <div class="row g-4">
        <!-- ฝั่งซ้าย: รายการเกมที่เลือกเล่น (9 ส่วน) -->
        <div class="col-12 col-xl-9 col-xxl-9">
            <div class="page-header">
                <h1><i class="fas fa-dice me-2"></i>เลือกเกมที่อยากเล่น</h1>
                <p>เลือกเกมแล้วชวนเพื่อนมาเล่นด้วยกันได้เลย!</p>
            </div>

            <!-- Game Grid -->
            <div class="game-grid">
                <?php foreach ($games as $key => $game): 
                    $is_coming_soon = (isset($game['status']) && $game['status'] === 'coming_soon');
                ?>
                <?php if ($is_coming_soon): ?>
                    <div class="game-card coming-soon" style="--card-color: <?= $game['color'] ?>; cursor: not-allowed; opacity: 0.85;">
                        <span class="game-icon"><?= $game['icon'] ?></span>
                        <div class="game-name"><?= $game['name'] ?></div>
                        <div class="game-desc"><?= $game['desc'] ?></div>
                        <span class="game-players"><i class="fas fa-users me-1"></i><?= $game['players'] ?></span>
                        <div class="play-btn"><i class="fas fa-clock me-1"></i>เร็วๆ นี้</div>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url('player/lobby/' . $key) ?>" class="game-card" style="--card-color: <?= $game['color'] ?>">
                        <span class="game-icon"><?= $game['icon'] ?></span>
                        <div class="game-name"><?= $game['name'] ?></div>
                        <div class="game-desc"><?= $game['desc'] ?></div>
                        <span class="game-players"><i class="fas fa-users me-1"></i><?= $game['players'] ?></span>
                        <div class="play-btn"><i class="fas fa-play me-1"></i>เริ่มเกม</div>
                    </a>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ฝั่งขวา: สถิติจำนวนครั้งที่ชนะในเกม (Ranking / Leaderboard) (3 ส่วน) -->
        <div class="col-12 col-xl-3 col-xxl-3">
            <div class="ranking-panel p-3 rounded-4 mt-xl-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.12); backdrop-filter: blur(16px); box-shadow: 0 10px 30px rgba(0,0,0,0.25);">
                <div class="mb-3 pb-2 border-bottom border-white border-opacity-10">
                    <h6 class="text-white mb-2 fw-bold d-flex align-items-center"><i class="fas fa-trophy me-2 text-warning"></i>สถิติชนะ (Leaderboard)</h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-white small mb-0 fw-bold text-nowrap"><i class="fas fa-filter me-1 text-primary"></i>เกม:</label>
                        <select id="select_ranking_game" class="form-select form-select-sm" style="background: rgba(26,26,46,0.95); color: #fff; border: 1px solid rgba(255,255,255,0.25); border-radius: 8px; font-size: 0.82rem;" onchange="loadRankingStats(this.value)">
                            <?php foreach ($games as $k => $g): ?>
                                <option value="<?= $k ?>" <?= ($k === 'tictactoe' || $k === 'xo') ? 'selected' : '' ?>>
                                    <?= $g['icon'] ?> <?= htmlspecialchars($g['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-dark table-hover small align-middle mb-0" style="background: transparent; border-color: rgba(255,255,255,0.08); font-size: 0.82rem;">
                        <thead>
                            <tr style="border-bottom: 2px solid rgba(255,255,255,0.15); color: #fff;">
                                <th style="width: 45px;" class="text-center px-1">#</th>
                                <th class="px-1">ชื่อ</th>
                                <th class="text-center px-1">ชนะ</th>
                                <th class="text-center px-1">แพ้</th>
                                <th class="text-center px-1">เล่น</th>
                            </tr>
                        </thead>
                        <tbody id="ranking_tbody">
                            <?php if (!empty($ranking_scores)): 
                                $rank = 1;
                                foreach ($ranking_scores as $s): 
                                    $s_user = isset($s['username']) ? $s['username'] : (isset($s['firebase_key']) ? $s['firebase_key'] : '-');
                                    $s_wins = isset($s['wins']) ? (int)$s['wins'] : 0;
                                    $s_losses = isset($s['losses']) ? (int)$s['losses'] : 0;
                                    $s_played = isset($s['played']) ? (int)$s['played'] : ($s_wins + $s_losses);
                                    $medal = ($rank === 1) ? '🥇' : (($rank === 2) ? '🥈' : (($rank === 3) ? '🥉' : $rank));
                            ?>
                            <tr>
                                <td class="text-center px-1"><strong><?= $medal ?></strong></td>
                                <td class="px-1 text-truncate" style="max-width: 85px;" title="<?= htmlspecialchars($s_user) ?>"><strong class="text-white"><?= htmlspecialchars($s_user) ?></strong></td>
                                <td class="text-center px-1"><span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-1 py-1"><?= $s_wins ?></span></td>
                                <td class="text-center px-1"><span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-1 py-1"><?= $s_losses ?></span></td>
                                <td class="text-center px-1"><span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 px-1 py-1"><?= $s_played ?></span></td>
                            </tr>
                            <?php $rank++; endforeach; else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">ยังไม่มีบันทึกสถิติในเกมนี้</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadRankingStats(gameKey) {
    $('#ranking_tbody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> กำลังโหลดสถิติ...</td></tr>');
    $.get('<?= base_url('player/get_game_scores/') ?>' + gameKey, function(data) {
        if (!data || data.length === 0) {
            $('#ranking_tbody').html('<tr><td colspan="5" class="text-center py-4 text-muted small">ยังไม่มีบันทึกสถิติในเกมนี้</td></tr>');
            return;
        }
        let html = '';
        data.forEach(function(s, idx) {
            let uName = s.username || s.firebase_key || '-';
            let wins = s.wins || 0;
            let losses = s.losses || 0;
            let played = s.played || (wins + losses);
            let medal = (idx === 0) ? '🥇' : ((idx === 1) ? '🥈' : ((idx === 2) ? '🥉' : (idx + 1)));
            let safeName = window.escapeHtml ? escapeHtml(uName) : uName;
            html += `
            <tr>
                <td class="text-center px-1"><strong>${medal}</strong></td>
                <td class="px-1 text-truncate" style="max-width: 85px;" title="${safeName}"><strong class="text-white">${safeName}</strong></td>
                <td class="text-center px-1"><span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-1 py-1">${wins}</span></td>
                <td class="text-center px-1"><span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 px-1 py-1">${losses}</span></td>
                <td class="text-center px-1"><span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 px-1 py-1">${played}</span></td>
            </tr>`;
        });
        $('#ranking_tbody').html(html);
    }, 'json').fail(function() {
        $('#ranking_tbody').html('<tr><td colspan="5" class="text-center py-4 text-danger">เกิดข้อผิดพลาดในการโหลดสถิติ</td></tr>');
    });
}
</script>

<!-- Global Widgets: Presence, Invites, and Chat -->
<?php $this->load->view('player/global_widget'); ?>

</body>
</html>
