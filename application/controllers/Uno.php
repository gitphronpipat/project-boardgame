<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uno extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        $this->load->library('firebase_lib');
        $this->load->model('Uno_model', 'uno_model');
    }

    /**
     * หน้าแรก UNO — สร้างห้องใหม่และ redirect
     */
    public function index()
    {
        $room_id = 'uno_' . time() . '_' . rand(100, 999);
        redirect('uno/room/' . $room_id);
    }

    /**
     * หน้าห้องเล่นเกม UNO
     */
    public function room($room_id = null)
    {
        if (empty($room_id)) {
            redirect('player');
        }

        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);

        if (!$room) {
            // ตรวจสอบข้อมูลจากล็อบบี้ถ้ามี
            $lobby = $this->firebase_lib->get_by_key('lobbies', $room_id);
            $players = [];
            $host = $username;

            if ($lobby && isset($lobby['players']) && is_array($lobby['players']) && count($lobby['players']) > 0) {
                $players = array_values(array_unique($lobby['players']));
                $host = isset($lobby['host']) ? $lobby['host'] : $players[0];
            } else {
                $players = [$username];
            }

            // สร้างห้องใหม่
            $room = $this->uno_model->create_game_state($room_id, $host, $players);
            $this->firebase_lib->set('games/uno', $room_id, $room);
        } else {
            // ถ้ามีห้องอยู่แล้ว และผู้เล่นยังไม่อยู่ในรายชื่อ (กรณีเข้ามาทีหลังและเกมยังไม่เริ่ม)
            $players = isset($room['players']) && is_array($room['players']) ? $room['players'] : [];
            if (!in_array($username, $players) && count($players) < 12 && isset($room['status']) && $room['status'] === 'waiting') {
                $players[] = $username;
                $room['players'] = $players;
                $this->firebase_lib->update('games/uno', $room_id, ['players' => $players]);
            }
        }

        // อัปเดต Presence ว่ากำลังเล่น UNO
        $this->firebase_lib->update('presence', $username, [
            'username'    => $username,
            'status'      => 'playing',
            'game'        => 'UNO',
            'room_id'     => $room_id,
            'last_active' => time(),
        ]);

        // ดึงสถิติของผู้เล่นในเกม UNO
        $user_stats = $this->firebase_lib->get_by_key('game/uno', $username);
        $my_wins   = (is_array($user_stats) && isset($user_stats['wins'])) ? (int)$user_stats['wins'] : 0;
        $my_losses = (is_array($user_stats) && isset($user_stats['losses'])) ? (int)$user_stats['losses'] : 0;
        $my_played = (is_array($user_stats) && isset($user_stats['played'])) ? (int)$user_stats['played'] : ($my_wins + $my_losses);

        $data = [
            'title'      => 'เกม UNO — ห้อง ' . $room_id,
            'room_id'    => $room_id,
            'username'   => $username,
            'room'       => $room,
            'user_stats' => [
                'wins'   => $my_wins,
                'losses' => $my_losses,
                'played' => $my_played,
            ],
        ];

        $this->load->view('games/uno/game', $data);
    }

    /**
     * API: ดึงสถานะปัจจุบันของเกม UNO
     */
    public function get_state($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);

        // หากไม่พบในครั้งแรก ให้ retry 1 ครั้ง เพื่อป้องกันปัญหาเน็ตเวิร์ก timeout ชั่วคราว
        if (!$room) {
            usleep(120000); // รอ 120ms
            $room = $this->firebase_lib->get_by_key('games/uno', $room_id);
        }

        if (!$room) {
            // เช็คว่ามีข้อมูลในล็อบบี้หรือไม่ (เผื่อกำลังโหลดหรือสร้างห้อง)
            $lobby = $this->firebase_lib->get_by_key('lobbies', $room_id);
            if ($lobby) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => 'syncing',
                    'message' => 'กำลังเตรียมห้องเกม...',
                ]));
            }

            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'closed',
                'message' => 'ห้องเกมนี้ถูกปิดหรือไม่มีอยู่',
            ]));
        }

        $players = isset($room['players']) && is_array($room['players']) ? $room['players'] : [];
        $hands   = isset($room['hands']) && is_array($room['hands']) ? $room['hands'] : [];
        $called_uno = isset($room['called_uno']) && is_array($room['called_uno']) ? $room['called_uno'] : [];

        $current_turn_idx = isset($room['current_turn']) ? (int)$room['current_turn'] : 0;
        $num_players = count($players);
        $current_turn_player = ($num_players > 0) ? $players[$current_turn_idx % $num_players] : '';

        // ส่งเฉพาะข้อมูลไพ่ของตัวเอง และจำนวนไพ่ของผู้เล่นคนอื่น เพื่อความปลอดภัย
        $my_hand = isset($hands[$username]) ? array_values($hands[$username]) : [];

        $players_info = [];
        foreach ($players as $idx => $p) {
            $p_hand = isset($hands[$p]) ? $hands[$p] : [];
            $players_info[] = [
                'username'       => $p,
                'card_count'     => count($p_hand),
                'called_uno'     => !empty($called_uno[$p]),
                'is_current_turn'=> ($p === $current_turn_player),
                'is_me'          => ($p === $username),
                'is_host'        => ($p === (isset($room['host']) ? $room['host'] : '')),
            ];
        }

        $deck_count = isset($room['deck']) && is_array($room['deck']) ? count($room['deck']) : 0;

        $response = [
            'status'             => isset($room['status']) ? $room['status'] : 'playing',
            'room_id'            => $room_id,
            'host'               => isset($room['host']) ? $room['host'] : '',
            'players'            => $players_info,
            'current_player'     => $current_turn_player,
            'is_my_turn'         => ($current_turn_player === $username),
            'direction'          => isset($room['direction']) ? (int)$room['direction'] : 1,
            'top_card'           => isset($room['top_card']) ? $room['top_card'] : null,
            'active_color'       => isset($room['active_color']) ? $room['active_color'] : 'red',
            'my_hand'            => $my_hand,
            'draw_pile_count'    => $deck_count,
            'turn_has_drawn'     => !empty($room['turn_has_drawn']),
            'winner'             => isset($room['winner']) ? $room['winner'] : null,
            'scores'             => isset($room['scores']) ? $room['scores'] : null,
            'round_points'       => isset($room['round_points']) ? $room['round_points'] : 0,
            'last_action'        => isset($room['last_action']) ? $room['last_action'] : 'เริ่มเกมแล้ว',
            'updated_at'         => isset($room['updated_at']) ? $room['updated_at'] : time(),
        ];

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    /**
     * API: วางการ์ด (Play Card)
     */
    public function play_card($room_id)
    {
        $username     = $this->session->userdata('username');
        $card_id      = $this->input->post('card_id');
        $chosen_color = $this->input->post('chosen_color');

        if (!$card_id) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ระบุการ์ดไม่ถูกต้อง']));
        }

        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);
        if (!$room || !isset($room['status']) || $room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เกมไม่ได้อยู่ในสถานะกำลังเล่น']));
        }

        $players          = $room['players'];
        $num_players      = count($players);
        $current_turn_idx = (int)$room['current_turn'];
        $current_player   = $players[$current_turn_idx % $num_players];

        if ($current_player !== $username) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ยังไม่ใช่ตาของคุณ']));
        }

        $hands = isset($room['hands']) ? $room['hands'] : [];
        $my_hand = isset($hands[$username]) ? $hands[$username] : [];

        // ค้นหาการ์ดในมือ
        $card_index = -1;
        $card_to_play = null;
        foreach ($my_hand as $i => $c) {
            if ($c['id'] === $card_id) {
                $card_index = $i;
                $card_to_play = $c;
                break;
            }
        }

        if ($card_to_play === null) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่พบการ์ดนี้ในมือของคุณ']));
        }

        // ตรวจสอบความถูกต้องตามกติกา UNO:
        $active_color = isset($room['active_color']) ? $room['active_color'] : '';
        $top_card     = isset($room['top_card']) ? $room['top_card'] : null;

        $is_wild = ($card_to_play['color'] === 'wild');
        $color_match = ($card_to_play['color'] === $active_color);
        $value_match = ($top_card && $card_to_play['value'] === $top_card['value']);

        if (!$is_wild && !$color_match && !$value_match) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'การ์ดนี้ไม่ตรงกับสีหรือตัวเลขบนกองทิ้ง']));
        }

        // จัดการกรณีการ์ด Wild / Wild Draw 4
        if ($is_wild) {
            if (!in_array($chosen_color, ['red', 'blue', 'green', 'yellow'])) {
                return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'กรุณาเลือกสีที่ต้องการเปลี่ยน']));
            }
            $new_active_color = $chosen_color;
        } else {
            $new_active_color = $card_to_play['color'];
        }

        // นำการ์ดออกจากมือ
        array_splice($my_hand, $card_index, 1);
        $hands[$username] = array_values($my_hand);

        // วางลงบนกองทิ้ง
        $discard_pile = isset($room['discard_pile']) ? $room['discard_pile'] : [];
        $discard_pile[] = $card_to_play;
        $room['discard_pile'] = array_values($discard_pile);
        $room['top_card']     = $card_to_play;
        $room['active_color'] = $new_active_color;

        $deck = isset($room['deck']) ? $room['deck'] : [];
        $called_uno = isset($room['called_uno']) ? $room['called_uno'] : [];
        $direction = isset($room['direction']) ? (int)$room['direction'] : 1;

        // ข้อความแอ็กชัน
        $color_th = [
            'red'    => 'สีแดง',
            'blue'   => 'สีน้ำเงิน',
            'green'  => 'สีเขียว',
            'yellow' => 'สีเหลือง',
            'wild'   => 'การ์ดเปลี่ยนสี',
        ];
        $val_th = [
            'skip'    => 'ข้าม (Skip)',
            'reverse' => 'ย้อนกลับ (Reverse)',
            'draw2'   => 'จั่ว 2 ใบ (+2)',
            'wild'    => 'เปลี่ยนสี (Wild)',
            'wild4'   => 'จั่ว 4 ใบ (+4)',
        ];
        $disp_val = isset($val_th[$card_to_play['value']]) ? $val_th[$card_to_play['value']] : $card_to_play['value'];
        $disp_col = isset($color_th[$card_to_play['color']]) ? $color_th[$card_to_play['color']] : $card_to_play['color'];
        $action_msg = "{$username} วางการ์ด {$disp_col} {$disp_val}";
        if ($is_wild) {
            $action_msg .= " (เปลี่ยนเป็น " . $color_th[$new_active_color] . ")";
        }

        // ตรวจสอบชัยชนะ (ไพ่หมดมือ)
        if (count($my_hand) === 0) {
            $total_points = 0;
            $player_scores = [];
            foreach ($players as $p) {
                $p_cards = isset($hands[$p]) ? $hands[$p] : [];
                $p_pts = 0;
                foreach ($p_cards as $c) {
                    if (is_numeric($c['value'])) {
                        $p_pts += (int)$c['value'];
                    } else if (in_array($c['value'], ['skip', 'reverse', 'draw2'])) {
                        $p_pts += 20;
                    } else if (in_array($c['value'], ['wild', 'wild4'])) {
                        $p_pts += 50;
                    }
                }
                $player_scores[$p] = $p_pts;
                $total_points += $p_pts;
            }

            $room['status']       = 'finished';
            $room['winner']       = $username;
            $room['scores']       = $player_scores;
            $room['round_points'] = $total_points;
            $room['hands']        = $hands;
            $room['last_action']  = "🏆 {$username} ชนะเกม UNO! ได้รับแต้มรวม {$total_points} แต้ม";
            $room['updated_at']   = time();

            // ใช้ set (PUT) เพื่อบันทึกข้อมูลทับแบบปลอดภัย
            $this->firebase_lib->set('games/uno', $room_id, $room);

            // บันทึกสถิติ
            foreach ($players as $p) {
                $stat = $this->firebase_lib->get_by_key('game/uno', $p);
                $wins   = (is_array($stat) && isset($stat['wins'])) ? (int)$stat['wins'] : 0;
                $losses = (is_array($stat) && isset($stat['losses'])) ? (int)$stat['losses'] : 0;
                $played = (is_array($stat) && isset($stat['played'])) ? (int)$stat['played'] : 0;
                $score  = (is_array($stat) && isset($stat['total_score'])) ? (int)$stat['total_score'] : 0;

                if ($p === $username) {
                    $wins++;
                    $score += $total_points;
                } else {
                    $losses++;
                }
                $played++;

                $this->firebase_lib->update('game/uno', $p, [
                    'wins'        => $wins,
                    'losses'      => $losses,
                    'played'      => $played,
                    'total_score' => $score,
                    'last_played' => time(),
                ]);
            }

            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'       => 'ok',
                'game_over'    => true,
                'winner'       => $username,
                'round_points' => $total_points,
            ]));
        }

        // สถานะ UNO
        if (count($my_hand) === 1) {
            if (empty($called_uno[$username])) {
                $called_uno[$username] = false;
            }
        } else {
            $called_uno[$username] = false;
        }

        // Action Effects
        $step = 1;

        if ($card_to_play['value'] === 'skip') {
            $step = 2;
            $skipped_player = $players[($current_turn_idx + ($direction * 1) + $num_players * 100) % $num_players];
            $action_msg .= " — ข้ามตาของ {$skipped_player}!";
        } else if ($card_to_play['value'] === 'reverse') {
            if ($num_players === 2) {
                $step = 2;
                $action_msg .= " — กฎ 2 คน Reverse ทำหน้าที่ข้ามตา!";
            } else {
                $direction = $direction * -1;
                $room['direction'] = $direction;
                $step = 1;
                $action_msg .= " — สลับทิศทางการเล่น!";
            }
        } else if ($card_to_play['value'] === 'draw2') {
            $victim_idx = ($current_turn_idx + ($direction * 1) + $num_players * 100) % $num_players;
            $victim = $players[$victim_idx];
            $this->uno_model->draw_cards_for_player($deck, $discard_pile, $hands, $victim, 2);
            $step = 2;
            $action_msg .= " — {$victim} โดน +2 และข้ามตา!";
        } else if ($card_to_play['value'] === 'wild4') {
            $victim_idx = ($current_turn_idx + ($direction * 1) + $num_players * 100) % $num_players;
            $victim = $players[$victim_idx];
            $this->uno_model->draw_cards_for_player($deck, $discard_pile, $hands, $victim, 4);
            $step = 2;
            $action_msg .= " — {$victim} โดน +4 และข้ามตา!";
        }

        $next_turn_idx = ($current_turn_idx + ($step * $direction) + ($num_players * 100)) % $num_players;

        $room['hands']           = $hands;
        $room['deck']            = array_values($deck);
        $room['discard_pile']    = array_values($discard_pile);
        $room['current_turn']    = $next_turn_idx;
        $room['direction']       = $direction;
        $room['called_uno']      = $called_uno;
        $room['turn_has_drawn']  = false;
        $room['last_action']     = $action_msg;
        $room['updated_at']      = time();

        $this->firebase_lib->set('games/uno', $room_id, $room);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => 'ok',
            'action' => $action_msg,
        ]));
    }

    /**
     * API: จั่วการ์ด 1 ใบ (Draw Card)
     */
    public function draw_card($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);

        if (!$room || !isset($room['status']) || $room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เกมไม่ได้อยู่ในสถานะกำลังเล่น']));
        }

        $players          = $room['players'];
        $num_players      = count($players);
        $current_turn_idx = (int)$room['current_turn'];
        $current_player   = $players[$current_turn_idx % $num_players];

        if ($current_player !== $username) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ยังไม่ใช่ตาของคุณ']));
        }

        if (!empty($room['turn_has_drawn'])) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'คุณจั่วการ์ดในตานี้ไปแล้ว ให้เลือกลงการ์ดหรือกดผ่านตา']));
        }

        $deck         = isset($room['deck']) ? $room['deck'] : [];
        $discard_pile = isset($room['discard_pile']) ? $room['discard_pile'] : [];
        $hands        = isset($room['hands']) ? $room['hands'] : [];

        $drawn_card = $this->uno_model->draw_cards_for_player($deck, $discard_pile, $hands, $username, 1);
        $room['deck']           = array_values($deck);
        $room['discard_pile']   = array_values($discard_pile);
        $room['hands']          = $hands;
        $room['turn_has_drawn'] = true;
        $room['last_action']    = "{$username} จั่วการ์ด 1 ใบ";
        $room['updated_at']     = time();

        $this->firebase_lib->set('games/uno', $room_id, $room);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'     => 'ok',
            'drawn_card' => $drawn_card ? $drawn_card[0] : null,
            'message'    => 'จั่วการ์ดสำเร็จ',
        ]));
    }

    /**
     * API: ผ่านตา (Pass Turn) หลังจากจั่วแล้ว
     */
    public function pass_turn($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);

        if (!$room || !isset($room['status']) || $room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เกมไม่ได้อยู่ในสถานะกำลังเล่น']));
        }

        $players          = $room['players'];
        $num_players      = count($players);
        $current_turn_idx = (int)$room['current_turn'];
        $current_player   = $players[$current_turn_idx % $num_players];

        if ($current_player !== $username) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ยังไม่ใช่ตาของคุณ']));
        }

        if (empty($room['turn_has_drawn'])) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'คุณต้องจั่วการ์ดก่อนจึงจะสามารถผ่านตาได้']));
        }

        $direction = isset($room['direction']) ? (int)$room['direction'] : 1;
        $next_turn_idx = ($current_turn_idx + (1 * $direction) + ($num_players * 100)) % $num_players;

        $room['current_turn']   = $next_turn_idx;
        $room['turn_has_drawn'] = false;
        $room['last_action']    = "{$username} เลือกผ่านตา";
        $room['updated_at']     = time();

        $this->firebase_lib->set('games/uno', $room_id, $room);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => 'ok',
            'message'=> 'ผ่านตาเรียบร้อยแล้ว',
        ]));
    }

    /**
     * API: พูด UNO! (Call UNO)
     */
    public function call_uno($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);

        if (!$room || !isset($room['status']) || $room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เกมไม่ได้อยู่ในสถานะกำลังเล่น']));
        }

        $hands = isset($room['hands']) ? $room['hands'] : [];
        $my_hand = isset($hands[$username]) ? $hands[$username] : [];

        // พูด UNO ได้เมื่อเหลือ 1 หรือ 2 ใบ
        if (count($my_hand) > 2) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'คุณสามารถพูด UNO ได้เมื่อมีไพ่เหลือ 1 หรือ 2 ใบในมือเท่านั้น']));
        }

        $called_uno = isset($room['called_uno']) ? $room['called_uno'] : [];
        $called_uno[$username] = true;

        $room['called_uno']   = $called_uno;
        $room['last_action']  = "📢 {$username} ตะโกนว่า 'UNO!' 🔥";
        $room['updated_at']   = time();

        $this->firebase_lib->set('games/uno', $room_id, $room);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'ok',
            'message' => 'คุณได้พูด UNO เรียบร้อยแล้ว!',
        ]));
    }

    /**
     * API: จับคนลืมพูด UNO (Catch UNO)
     */
    public function catch_uno($room_id)
    {
        $username = $this->session->userdata('username');
        $target   = $this->input->post('target_user');

        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);
        if (!$room || !isset($room['status']) || $room['status'] !== 'playing') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เกมไม่ได้อยู่ในสถานะกำลังเล่น']));
        }

        if (empty($target) || $target === $username) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'เป้าหมายไม่ถูกต้อง']));
        }

        $hands = isset($room['hands']) ? $room['hands'] : [];
        $target_hand = isset($hands[$target]) ? $hands[$target] : [];
        $called_uno = isset($room['called_uno']) ? $room['called_uno'] : [];

        if (count($target_hand) === 1 && empty($called_uno[$target])) {
            $deck         = isset($room['deck']) ? $room['deck'] : [];
            $discard_pile = isset($room['discard_pile']) ? $room['discard_pile'] : [];

            $this->uno_model->draw_cards_for_player($deck, $discard_pile, $hands, $target, 2);

            $room['deck']         = array_values($deck);
            $room['discard_pile'] = array_values($discard_pile);
            $room['hands']        = $hands;
            $room['last_action']  = "🚨 {$username} จับ {$target} ที่ลืมพูด UNO สำเร็จ! โดนปรับจั่ว 2 ใบ";
            $room['updated_at']   = time();

            $this->firebase_lib->set('games/uno', $room_id, $room);

            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => 'ok',
                'message' => "จับ {$target} สำเร็จ! ถูกทำโทษจั่ว 2 ใบ",
            ]));
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'error',
            'message' => 'ไม่สามารถจับได้ (ผู้เล่นไม่ได้เหลือ 1 ใบ หรือได้พูด UNO ไปแล้ว)',
        ]));
    }

    /**
     * API: ขอเล่นอีกครั้ง (Rematch)
     */
    public function rematch($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);

        if (!$room) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'ไม่พบห้องนี้']));
        }

        $host = isset($room['host']) ? $room['host'] : $username;
        $players = isset($room['players']) ? $room['players'] : [$username];

        $new_room = $this->uno_model->create_game_state($room_id, $host, $players);
        $new_room['last_action'] = "🔄 เริ่มเกมใหม่อีกรอบแล้ว!";
        $this->firebase_lib->set('games/uno', $room_id, $new_room);

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'status'  => 'ok',
            'message' => 'เริ่มเกมรอบใหม่เรียบร้อยแล้ว',
        ]));
    }

    /**
     * API: ออกจากห้อง UNO
     */
    public function leave($room_id)
    {
        $username = $this->session->userdata('username');
        $room = $this->firebase_lib->get_by_key('games/uno', $room_id);

        if ($room) {
            $players = isset($room['players']) ? $room['players'] : [];
            $players = array_values(array_filter($players, function($p) use ($username) {
                return $p !== $username;
            }));

            // นำการ์ดของผู้เล่นที่ออกกลับเข้ากองจั่ว
            $hands = isset($room['hands']) ? $room['hands'] : [];
            if (isset($hands[$username])) {
                $deck = isset($room['deck']) ? $room['deck'] : [];
                $deck = array_merge($deck, $hands[$username]);
                shuffle($deck);
                $room['deck'] = array_values($deck);
                unset($hands[$username]);
                $room['hands'] = $hands;
            }

            if (count($players) <= 1) {
                if (count($players) === 1 && isset($room['status']) && $room['status'] === 'playing') {
                    // ผู้เล่นที่เหลืออยู่คนเดียวเป็นผู้ชนะโดยอัตโนมัติ
                    $winner = $players[0];
                    $room['status']      = 'finished';
                    $room['winner']      = $winner;
                    $room['last_action'] = "{$username} ออกจากห้อง ทำให้ {$winner} ชนะการแข่งขัน!";
                    $room['players']     = $players;
                    $this->firebase_lib->set('games/uno', $room_id, $room);
                } else {
                    // ปิดห้องอย่างปลอดภัยโดยไม่ลบทิ้งทันที เพื่อให้ผู้เล่นอื่นเห็นสถานะ
                    $room['status']      = 'closed';
                    $room['players']     = [];
                    $room['last_action'] = "ห้องเกมถูกปิดแล้ว";
                    $this->firebase_lib->set('games/uno', $room_id, $room);
                }
            } else {
                $room['players'] = $players;
                $room['current_turn'] = (int)$room['current_turn'] % count($players);
                $room['last_action']  = "{$username} ได้ออกจากห้อง";
                $this->firebase_lib->set('games/uno', $room_id, $room);
            }
        }

        // คืนสถานะ Presence เป็น Online ว่าง
        $this->firebase_lib->update('presence', $username, [
            'status'      => 'online',
            'game'        => '',
            'room_id'     => '',
            'last_active' => time(),
        ]);

        if ($this->input->is_ajax_request()) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'ok']));
        }
        redirect('player');
    }
}
