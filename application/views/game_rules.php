<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Game Rules View Component (application/views/game_rules.php)
 * รวบรวมกฎและกติกาการเล่นของแต่ละบอร์ดเกมในรูปแบบ Array
 * พร้อมดึงมาแสดงผลตามเกมที่เลือกในหน้า Lobby
 */

$game_rules_registry = [
    // -------------------------------------------------------------
    // 1. OX (Tic Tac Toe)
    // -------------------------------------------------------------
    'tictactoe' => [
        'name'        => 'OX (Tic Tac Toe)',
        'icon'        => '❌⭕',
        'color'       => '#6366f1',
        'category'    => 'เกมวางแผนกลยุทธ์',
        'players'     => '2 คน',
        'time'        => '1 - 3 นาที',
        'objective'   => 'ผู้เล่นต้องวางสัญลักษณ์ของตนเอง (X หรือ O) ให้เรียงติดต่อกัน 3 ตัวบนกระดาน 3x3 ก่อนคู่ต่อสู้',
        'rules'       => [
            'ผู้เล่น 2 ฝ่ายจะได้รับสัญลักษณ์ X หรือ O สลับกันเดินคนละ 1 ตา',
            'ในแต่ละตา สามารถคลิกวางสัญลักษณ์ในช่องว่างที่ยังไม่มีใครลงได้ 1 ช่อง',
            'เมื่อวางสัญลักษณ์ลงไปแล้ว จะไม่สามารถเปลี่ยนตำแหน่งหรือยกเลิกได้',
            'ผู้เล่นคนแรกที่สามารถเรียงสัญลักษณ์เหมือนกันครบ 3 ตัวติดกันจะเป็นฝ่ายชนะทันที',
        ],
        'win_conditions'  => 'เรียงสัญลักษณ์เหมือนกัน 3 ตัวในแนวใดก็ได้ (แนวนอน, แนวตั้ง หรือแนวทแยงมุม)',
        'draw_conditions' => 'ลงสัญลักษณ์จนเต็มกระดานทั้ง 9 ช่องแล้ว แต่ไม่มีผู้เล่นฝ่ายใดเรียงครบ 3 ตัว',
        'tips'            => 'พยายามยึด "ช่องกึ่งกลางกระดาน" หรือ "มุมทั้งสี่" ไว้ก่อน เพื่อเปิดโอกาสในการชนะได้พร้อมกัน 2 ทิศทาง (สร้างทางดัก)',
    ],

    // -------------------------------------------------------------
    // 2. หมากรุกสากล (Chess)
    // -------------------------------------------------------------
    'chess' => [
        'name'        => 'หมากรุกสากล (Chess)',
        'icon'        => '♟️',
        'color'       => '#059669',
        'category'    => 'เกมกระดานวางแผนระดับโลก',
        'players'     => '2 คน',
        'time'        => '10 - 30 นาที',
        'objective'   => 'วางแผนเดินตัวหมากเพื่อรุกฆาต (Checkmate) คิงของฝ่ายตรงข้ามไม่ให้มีทางหนีรอด',
        'rules'       => [
            'ฝ่ายหมากสีขาวได้เริ่มเดินก่อนเสมอ และสลับกันเดินคนละ 1 ตา',
            'ตัวหมากแต่ละชนิดมีการเดินเฉพาะตัว: เบี้ย (Pawn), ม้า (Knight), บิชอป (Bishop), เรือ (Rook), ควีน (Queen), และคิง (King)',
            'ห้ามเดินหมากในลักษณะที่ทำให้คิงของตนเองตกอยู่ในสถานะ "ถูกรุก" (In Check)',
            'หากคิงถูกโจมตี (รุก) ผู้เล่นต้องแก้ไขสถานะรุกทันที โดยการเดินหนี, เอาหมากมาบัง หรือกินตัวที่รุก',
            'มีท่าเดินพิเศษ เช่น การเข้าป้อม (Castling) และการกินผ่าน (En Passant)',
        ],
        'win_conditions'  => 'รุกฆาต (Checkmate) คิงของคู่แข่งสำเร็จ หรือคู่แข่งกดยอมแพ้ / เวลาเดินหมด',
        'draw_conditions' => 'คิงไม่ถูกรุกแต่ไม่มีตาเดินที่ถูกกฎ (Stalemate), ตัวหมากไม่พอรุกฆาต, หรือตำแหน่งเดินซ้ำกัน 3 ครั้ง',
        'tips'            => 'ควบคุมพื้นที่ 4 ช่องตรงกลางกระดานตั้งแต่ต้นเกม และรีบนำตัวหมากเบา (ม้าและบิชอป) ออกมาประสานงาน',
    ],

    // -------------------------------------------------------------
    // 3. UNO
    // -------------------------------------------------------------
    'uno' => [
        'name'        => 'UNO (การ์ดเกมอูโน่)',
        'icon'        => '🃏',
        'color'       => '#dc2626',
        'category'    => 'การ์ดเกมปาร์ตี้',
        'players'     => '2 - 6 คน',
        'time'        => '5 - 15 นาที',
        'objective'   => 'ทิ้งการ์ดบนมือให้หมดเป็นคนแรก โดยลงการ์ดที่สีตรงกัน ตัวเลขตรงกัน หรือใช้การ์ดฟังก์ชันพิเศษ',
        'rules'       => [
            'ผู้เล่นทุกคนได้รับการแจกการ์ดเริ่มต้นคนละ 7 ใบ',
            'ในแต่ละรอบ ให้ลงการ์ดที่มี "สีเดียวกัน" หรือ "ตัวเลขเดียวกัน" กับการ์ดใบบนสุดของกองกลาง',
            'หากไม่มีการ์ดที่ลงได้ ต้องจั่วการ์ดจากกองกลาง 1 ใบ (หากการ์ดที่จั่วลงได้ สามารถทิ้งลงได้ทันที)',
            'การ์ดฟังก์ชันพิเศษ: ข้ามเทิร์น (Skip), สลับทิศทางการเล่น (Reverse), บังคับจั่ว 2 ใบ (Draw Two), เปลี่ยนสี (Wild Card), และเปลี่ยนสีพร้อมให้เพื่อนจั่ว 4 ใบ (Wild Draw Four)',
            'เมื่อเหลือการ์ดในมือเพียงใบเดียว ต้องกดปุ่ม "UNO!" ทันที หากลืมและเพื่อนจับได้จะต้องจั่วการ์ดทำโทษ 2 ใบ',
        ],
        'win_conditions'  => 'ทิ้งการ์ดในมือจนหมดเกลี้ยงเป็นคนแรก',
        'draw_conditions' => 'ไม่มีผลเสมอ (เล่นจนกว่าจะมีผู้ชนะทิ้งการ์ดหมด)',
        'tips'            => 'เก็บการ์ดเปลี่ยนสี (Wild) หรือ Wild Draw Four ไว้ใช้ช่วงท้ายเกมเพื่อปิดเกมอย่างรวดเร็ว',
    ],

    // -------------------------------------------------------------
    // 4. บันไดงู (Snake & Ladder)
    // -------------------------------------------------------------
    'snake-ladder' => [
        'name'        => 'บันไดงู (Snake & Ladder)',
        'icon'        => '🐍🪜',
        'color'       => '#d97706',
        'category'    => 'เกมกระดานลูกเต๋าคลาสสิก',
        'players'     => '2 - 4 คน',
        'time'        => '5 - 10 นาที',
        'objective'   => 'ทอยลูกเต๋าแล้วเดินตัวหมากผจญภัยไปถึงช่องเส้นชัย (ช่องที่ 100) ให้ได้เป็นคนแรก',
        'rules'       => [
            'ผู้เล่นผลัดกันทอยลูกเต๋า และเดินหมากไปข้างหน้าตามจำนวนแต้มที่ทอยได้',
            '🪜 เจอฐานบันได: ตัวหมากจะได้ปีนบันไดลัดขึ้นไปยังช่องปลายบันไดด้านบนทันที',
            '🐍 เจอหัวงู: ตัวหมากจะถูกงูกลืนและลื่นไถลตกลงมายังช่องหางงูด้านล่าง',
            'หากทอยลูกเต๋าได้แต้ม 6 จะได้รับสิทธิ์ทอยลูกเต๋าเพื่อเดินเพิ่มอีก 1 ครั้ง',
        ],
        'win_conditions'  => 'เดินตัวหมากเข้าสู่ช่องเส้นชัย (ช่อง 100) พอดีเป๊ะเป็นคนแรก',
        'draw_conditions' => 'ไม่มีผลเสมอ',
        'tips'            => 'เกมนี้อาศัยดวงและโชคเป็นหลัก ลุ้นบันไดทองคำและระวังหัวงูยักษ์ที่อยู่ใกล้เส้นชัย!',
    ],

    // -------------------------------------------------------------
    // 5. หมากฮอส (Checkers)
    // -------------------------------------------------------------
    'checkers' => [
        'name'        => 'หมากฮอส (Checkers)',
        'icon'        => '⚫🔴',
        'color'       => '#be185d',
        'category'    => 'เกมกระดานวางแผน',
        'players'     => '2 คน',
        'time'        => '5 - 15 นาที',
        'objective'   => 'กินตัวหมากของฝ่ายตรงข้ามให้หมดกระดาน หรือปิดทางไม่ให้ฝ่ายตรงข้ามสามารถเดินหมากต่อได้',
        'rules'       => [
            'หมากเบี้ยธรรมดาเดินเฉียงไปข้างหน้าได้ครั้งละ 1 ช่องบนช่องตารางสีเข้ม',
            'การกินหมาก: หากมีหมากคู่แข่งอยู่ติดด้านหน้าเฉียง และมีช่องว่างถัดไป ให้กระโดดข้ามเพื่อกินตัวนั้นออกนอกกระดาน',
            'หากกินแล้วยังมีหมากคู่แข่งให้กินต่อได้อีก ต้องทำการ "กินต่อเนื่อง (กินสอง/กินสาม)" ทันที',
            'เมื่อหมากเดินไปถึงแถวสุดท้ายของฝั่งตรงข้าม จะได้รับการอัปเกรดเป็น "ฮอส" (King) ซึ่งสามารถเดินเฉียงและกินได้หลายช่องทั้งหน้าและหลัง',
        ],
        'win_conditions'  => 'กินหมากของคู่ต่อสู้จนหมดกระดาน หรือคู่ต่อสู้ไม่เหลือตาเดินที่ถูกกฎ',
        'draw_conditions' => 'ทั้งสองฝ่ายเหลือหมากจำนวนน้อยเท่ากันและไม่มีฝ่ายใดสามารถเข้าทำได้',
        'tips'            => 'พยายามป้องกันแถวหลังสุดของตนเองไว้ให้นานที่สุด เพื่อไม่ให้คู่แข่งนำหมากมาขึ้นฮอสได้ง่ายๆ',
    ],

    // -------------------------------------------------------------
    // 6. Connect Four (หยอดเหรียญ 4 ช่อง)
    // -------------------------------------------------------------
    'connect4' => [
        'name'        => 'Connect Four',
        'icon'        => '🔵🟡',
        'color'       => '#2563eb',
        'category'    => 'เกมวางแผนเรียงเหรียญ',
        'players'     => '2 คน',
        'time'        => '3 - 8 นาที',
        'objective'   => 'หยอดเหรียญสีของตนเองลงในช่องตารางแนวตั้ง ให้เรียงติดกันครบ 4 เหรียญก่อนคู่แข่ง',
        'rules'       => [
            'กระดานมีขนาด 7 คอลัมน์ x 6 แถว (แนวตั้ง)',
            'ผู้เล่นผลัดกันเลือกหยอดเหรียญลงในคอลัมน์ใดก็ได้ 1 เหรียญต่อตา',
            'เหรียญจะตกลงไปที่ตำแหน่งล่างสุดที่ยังว่างอยู่ในคอลัมน์นั้นด้วยแรงโน้มถ่วง',
            'ไม่สามารถถอนหรือสลับตำแหน่งเหรียญที่หยอดลงไปแล้วได้',
        ],
        'win_conditions'  => 'มีเหรียญสีของตนเองเรียงติดกัน 4 ช่องในแนวใดก็ได้ (แนวนอน, แนวตั้ง หรือแนวทแยง)',
        'draw_conditions' => 'หยอดเหรียญเต็มทั้ง 42 ช่องบนกระดานแล้ว แต่ไม่มีใครเรียงได้ 4 ช่อง',
        'tips'            => 'สร้างจังหวะ "ดับเบิ้ลล็อค" (Double Threat) คือการทำให้เกิดทางชนะ 2 ทางพร้อมกันเพื่อให้คู่ต่อสู้บล็อกได้เพียงทางเดียว',
    ],

    // -------------------------------------------------------------
    // 7. Memory Match (เกมจับคู่ความจำ)
    // -------------------------------------------------------------
    'memory' => [
        'name'        => 'Memory Match (จับคู่การ์ดความจำ)',
        'icon'        => '🧠',
        'color'       => '#7c3aed',
        'category'    => 'เกมฝึกสมองและความจำ',
        'players'     => '1 - 4 คน',
        'time'        => '3 - 7 นาที',
        'objective'   => 'จดจำตำแหน่งของการ์ดและเปิดจับคู่ภาพที่เหมือนกันให้ได้จำนวนมากที่สุด',
        'rules'       => [
            'การ์ดภาพทั้งหมดจะถูกสับและคว่ำหน้าอยู่บนกระดาน',
            'ในแต่ละตา ผู้เล่นสามารถคลิกเปิดการ์ดได้ 2 ใบ',
            'หากการ์ดทั้ง 2 ใบมีรูป "เหมือนกัน": ผู้เล่นจะได้แต้มคู่นั้น และได้รับสิทธิ์เปิดการ์ดต่ออีกรอบ',
            'หากการ์ดทั้ง 2 ใบ "ไม่เหมือนกัน": การ์ดจะถูกคว่ำกลับไปที่เดิม และสลับตาให้อีกฝ่ายเปิด',
        ],
        'win_conditions'  => 'ผู้เล่นที่จับคู่การ์ดและสะสมคะแนนได้มากที่สุดเมื่อเปิดการ์ดครบทุกคู่บนกระดาน',
        'draw_conditions' => 'ผู้เล่นทั้งสองฝ่ายมีคะแนนจับคู่เท่ากันเมื่อจบเกม',
        'tips'            => 'จดจำตำแหน่งของการ์ดที่เพื่อนเปิดเสมอ แม้จะเปิดไม่ตรงคู่ เพราะข้อมูลนั้นจะมีประโยชน์มากในตาของคุณ!',
    ],

    // -------------------------------------------------------------
    // 8. Quiz Battle (ประลองคำถาม)
    // -------------------------------------------------------------
    'quiz' => [
        'name'        => 'Quiz Battle (ตอบคำถามชิงไหวพริบ)',
        'icon'        => '❓',
        'color'       => '#0891b2',
        'category'    => 'เกมประลองความรู้รอบตัว',
        'players'     => '2 - 8 คน',
        'time'        => '5 - 10 นาที',
        'objective'   => 'ตอบคำถามให้ถูกต้องและทำเวลาให้ไวที่สุดเพื่อสะสมคะแนนสูงสุด',
        'rules'       => [
            'แต่ละข้อจะมีคำถามพร้อมตัวเลือกคำตอบ 4 ข้อ',
            'มีเวลานับถอยหลังจำกัดในแต่ละข้อ (เช่น 10-15 วินาที)',
            'ยิ่งตอบถูกเร็วเท่าไร จะยิ่งได้รับคะแนนโบนัสความเร็ว (Speed Bonus) มากขึ้นเท่านั้น',
            'หากตอบผิดหรือไม่ทันเวลา จะไม่ได้รับคะแนนในข้อนั้น',
        ],
        'win_conditions'  => 'ผู้เล่นที่ได้คะแนนรวมสูงสุดหลังจากตอบคำถามครบทุกข้อในรอบนั้น',
        'draw_conditions' => 'ผู้เล่นทำคะแนนรวมได้เท่ากันเมื่อจบทุกข้อ',
        'tips'            => 'อ่านโจทย์และคีย์เวิร์ดให้แม่นยำ อย่ารีบกดจนพลาดตัวเลือกหลอก!',
    ],
];

// -------------------------------------------------------------
// ตรวจสอบและค้นหากฎของเกมตาม Key หรือ Route
// -------------------------------------------------------------
$current_key = isset($game_key) ? strtolower(trim((string)$game_key)) : '';
if (empty($current_key) && isset($game['route'])) {
    $current_key = strtolower(trim((string)$game['route']));
}

// Map alias ถ้ามี เช่น xo -> tictactoe, snake_ladder -> snake-ladder
$alias_map = [
    'xo'           => 'tictactoe',
    'snake_ladder' => 'snake-ladder',
];
if (isset($alias_map[$current_key])) {
    $current_key = $alias_map[$current_key];
}

// ดึงข้อมูลกฎ ถ้าไม่มีใช้ค่าเริ่มต้น
$rule = isset($game_rules_registry[$current_key]) ? $game_rules_registry[$current_key] : null;

// ถ้าไม่พบใน registry ให้ใช้ข้อมูลจาก $game ที่ส่งมาแสดงเป็น Fallback
if (!$rule && isset($game)) {
    $rule = [
        'name'            => isset($game['name']) ? $game['name'] : 'กฎและกติกาการเล่น',
        'icon'            => isset($game['icon']) ? $game['icon'] : '🎮',
        'color'           => isset($game['color']) ? $game['color'] : '#6366f1',
        'category'        => isset($game['category']) ? ucfirst($game['category']) : 'บอร์ดเกม',
        'players'         => isset($game['players']) ? $game['players'] : '2 คน',
        'time'            => '5 - 10 นาที',
        'objective'       => isset($game['desc']) ? $game['desc'] : 'สนุกไปกับการเล่นเกมและประลองฝีมือกับเพื่อนๆ',
        'rules'           => [
            'ผู้เล่นเข้าร่วมห้องตามจำนวนที่กำหนดและรอ Host เริ่มเกม',
            'สลับกันเล่นตามรอบและเวลาที่กำหนดในเกม',
            'ปฏิบัติตามกติกามาตรฐานของเกมเพื่อเก็บคะแนนและคว้าชัยชนะ',
        ],
        'win_conditions'  => 'ทำตามเงื่อนไขเป้าหมายของเกมให้สำเร็จก่อนคู่แข่ง',
        'draw_conditions' => 'หมดเวลาหรือกระดานไม่สามารถดำเนินต่อได้โดยไม่มีผู้ชนะ',
        'tips'            => 'วางแผนล่วงหน้าและสังเกตการเล่นของคู่ต่อสู้เพื่อชิงความได้เปรียบ',
    ];
}
?>

<!-- ============================================================= -->
<!-- Game Rules Card Component UI                                  -->
<!-- ============================================================= -->
<style>
.rules-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 1.75rem;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
    color: #e2e8f0;
    position: relative;
    overflow: hidden;
}

.rules-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: <?= htmlspecialchars($rule['color']) ?>;
    box-shadow: 0 0 15px <?= htmlspecialchars($rule['color']) ?>;
}

.rules-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-bottom: 1.1rem;
    margin-bottom: 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.rules-icon-badge {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    flex-shrink: 0;
}

.rules-section-title {
    font-size: 0.88rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #94a3b8;
    margin-bottom: 0.55rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rules-objective-box {
    background: rgba(99, 102, 241, 0.08);
    border-left: 3px solid <?= htmlspecialchars($rule['color']) ?>;
    border-radius: 0 12px 12px 0;
    padding: 0.9rem 1rem;
    margin-bottom: 1.25rem;
    font-size: 0.92rem;
    line-height: 1.55;
    color: #f8fafc;
}

.rules-list {
    list-style: none;
    padding-left: 0;
    margin-bottom: 1.25rem;
}

.rules-list li {
    position: relative;
    padding-left: 2rem;
    margin-bottom: 0.7rem;
    font-size: 0.88rem;
    line-height: 1.5;
    color: #cbd5e1;
}

.rules-list li::before {
    content: counter(item);
    counter-increment: item;
    position: absolute;
    left: 0;
    top: 1px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    color: <?= htmlspecialchars($rule['color']) ?>;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.rules-outcome-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 1.25rem;
}

.rules-outcome-item {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 12px;
    padding: 0.75rem 0.9rem;
}

.rules-outcome-item.win {
    border-color: rgba(34, 197, 94, 0.25);
    background: rgba(34, 197, 94, 0.05);
}

.rules-outcome-item.draw {
    border-color: rgba(234, 179, 8, 0.25);
    background: rgba(234, 179, 8, 0.05);
}

.rules-outcome-item h6 {
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 0.35rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.rules-outcome-item.win h6 { color: #4ade80; }
.rules-outcome-item.draw h6 { color: #facc15; }

.rules-outcome-item p {
    font-size: 0.8rem;
    color: #cbd5e1;
    margin-bottom: 0;
    line-height: 1.4;
}

.rules-tips-box {
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: 12px;
    padding: 0.85rem 1rem;
    font-size: 0.84rem;
    color: #fef08a;
    line-height: 1.45;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.rules-tips-box i {
    color: #fbbf24;
    font-size: 1rem;
    margin-top: 2px;
    flex-shrink: 0;
}
</style>

<div class="rules-card text-start">
    <!-- Header -->
    <div class="rules-header">
        <div class="d-flex align-items-center gap-3">
            <div class="rules-icon-badge">
                <?= $rule['icon'] ?>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h5 class="text-white mb-0 fw-bold" style="font-size: 1.15rem;"><?= htmlspecialchars($rule['name']) ?></h5>
                </div>
                <span class="badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 mt-1" style="font-size: 0.72rem; font-weight: 500;">
                    <i class="fas fa-book-open me-1 text-info"></i>กฎและกติกาการเล่น
                </span>
            </div>
        </div>
        <div class="d-none d-sm-flex flex-column align-items-end">
            <span class="badge rounded-pill bg-light bg-opacity-10 text-white px-2 py-1" style="font-size: 0.75rem;">
                <i class="fas fa-users me-1 text-warning"></i><?= htmlspecialchars($rule['players']) ?>
            </span>
            <span class="text-white-50 mt-1" style="font-size: 0.72rem;">
                <i class="far fa-clock me-1"></i><?= htmlspecialchars($rule['time']) ?>
            </span>
        </div>
    </div>

    <!-- เป้าหมายของเกม (Objective) -->
    <div class="rules-section-title">
        <i class="fas fa-bullseye text-danger"></i>เป้าหมายของเกม
    </div>
    <div class="rules-objective-box">
        <?= htmlspecialchars($rule['objective']) ?>
    </div>

    <!-- กฎการเล่น (How to play) -->
    <div class="rules-section-title">
        <i class="fas fa-list-check text-primary"></i>กติกาและวิธีเล่น
    </div>
    <ol class="rules-list" style="counter-reset: item;">
        <?php foreach ($rule['rules'] as $r_item): ?>
            <li><?= htmlspecialchars($r_item) ?></li>
        <?php endforeach; ?>
    </ol>

    <!-- เงื่อนไขการชนะ / เสมอ -->
    <div class="rules-section-title">
        <i class="fas fa-flag-checkered text-success"></i>เงื่อนไขการตัดสินผล
    </div>
    <div class="rules-outcome-grid">
        <div class="rules-outcome-item win">
            <h6><i class="fas fa-trophy"></i>เงื่อนไขชนะ</h6>
            <p><?= htmlspecialchars($rule['win_conditions']) ?></p>
        </div>
        <div class="rules-outcome-item draw">
            <h6><i class="fas fa-handshake"></i>เงื่อนไขเสมอ</h6>
            <p><?= htmlspecialchars($rule['draw_conditions']) ?></p>
        </div>
    </div>

    <!-- เทคนิคและคำแนะนำ (Tips) -->
    <?php if (!empty($rule['tips'])): ?>
        <div class="rules-tips-box">
            <i class="fas fa-lightbulb"></i>
            <div>
                <strong class="d-block text-white mb-1" style="font-size: 0.84rem;">เคล็ดลับการเล่น:</strong>
                <span><?= htmlspecialchars($rule['tips']) ?></span>
            </div>
        </div>
    <?php endif; ?>
</div>
