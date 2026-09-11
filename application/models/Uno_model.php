<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uno_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * สร้างสถานะเริ่มต้นของเกม UNO (แจกไพ่ 7 ใบ, การ์ดเริ่มต้นตัวเลข)
     */
    public function create_game_state($room_id, $host, $players)
    {
        $players = array_values(array_unique(array_filter($players)));
        $num_players = count($players);

        // หากผู้เล่นเกิน 6 คน ใช้ 2 สำรับ (216 ใบ) ตามมาตรฐานสากล
        $deck_count = ($num_players > 6) ? 2 : 1;
        $deck = $this->generate_deck($deck_count);

        // แจกการ์ดให้ผู้เล่นคนละ 7 ใบ
        $hands = [];
        $called_uno = [];
        foreach ($players as $p) {
            $hands[$p] = array_values(array_splice($deck, 0, 7));
            $called_uno[$p] = false;
        }

        // เปิดการ์ดเริ่มต้นจากกองจั่ว วางเป็นใบแรกของกองทิ้ง
        // กติกาสากล: หากใบแรกเป็นการ์ดแอ็กชัน ให้เปิดใหม่จนกว่าจะได้การ์ดตัวเลข
        $start_card = null;
        $temp_drawn = [];
        while (count($deck) > 0) {
            $candidate = array_shift($deck);
            if (is_numeric($candidate['value'])) {
                $start_card = $candidate;
                break;
            } else {
                $temp_drawn[] = $candidate;
            }
        }

        // นำการ์ดแอ็กชันที่เปิดข้ามกลับใส่คืนเข้ากองจั่วแล้วสับใหม่
        if (!empty($temp_drawn)) {
            $deck = array_merge($deck, $temp_drawn);
            shuffle($deck);
        }

        // กรณีหาไม่ได้จริงๆ ให้กำหนดการ์ดเริ่มต้นตัวเลข
        if (!$start_card) {
            $start_card = ['id' => 'c_init', 'color' => 'red', 'value' => '7'];
        }

        $discard_pile = [$start_card];
        $active_color = $start_card['color'];

        return [
            'id'             => $room_id,
            'host'           => $host,
            'players'        => $players,
            'current_turn'   => 0,
            'direction'      => 1, // 1 = ตามเข็ม, -1 = ทวนเข็ม
            'status'         => ($num_players >= 2) ? 'playing' : 'waiting',
            'deck'           => array_values($deck),
            'discard_pile'   => array_values($discard_pile),
            'top_card'       => $start_card,
            'active_color'   => $active_color,
            'hands'          => $hands,
            'called_uno'     => $called_uno,
            'turn_has_drawn' => false,
            'winner'         => null,
            'scores'         => null,
            'round_points'   => 0,
            'last_action'    => "เริ่มเกม UNO! การ์ดเริ่มต้นคือ {$start_card['color']} {$start_card['value']}",
            'created_at'     => time(),
            'updated_at'     => time(),
        ];
    }

    /**
     * สร้างสำรับไพ่ UNO มาตรฐานสากล (108 ใบ หรือทวีคูณ)
     */
    public function generate_deck($multipliers = 1)
    {
        $deck = [];
        $colors = ['red', 'blue', 'green', 'yellow'];

        for ($m = 0; $m < $multipliers; $m++) {
            foreach ($colors as $color) {
                // 1 ใบ: เลข 0
                $deck[] = ['id' => uniqid('c_'), 'color' => $color, 'value' => '0'];

                // 2 ใบต่อสี: เลข 1-9
                for ($v = 1; $v <= 9; $v++) {
                    $deck[] = ['id' => uniqid('c_'), 'color' => $color, 'value' => (string)$v];
                    $deck[] = ['id' => uniqid('c_'), 'color' => $color, 'value' => (string)$v];
                }

                // 2 ใบต่อสี: แอ็กชัน (skip, reverse, draw2)
                foreach (['skip', 'reverse', 'draw2'] as $action) {
                    $deck[] = ['id' => uniqid('c_'), 'color' => $color, 'value' => $action];
                    $deck[] = ['id' => uniqid('c_'), 'color' => $color, 'value' => $action];
                }
            }

            // การ์ดพิเศษ Wild: 4 ใบ wild, 4 ใบ wild4
            for ($w = 0; $w < 4; $w++) {
                $deck[] = ['id' => uniqid('c_'), 'color' => 'wild', 'value' => 'wild'];
                $deck[] = ['id' => uniqid('c_'), 'color' => 'wild', 'value' => 'wild4'];
            }
        }

        shuffle($deck);
        return array_values($deck);
    }

    /**
     * จั่วการ์ดให้ผู้เล่น พร้อมรีไซเคิลกองทิ้งเมื่อกองจั่วหมด
     */
    public function draw_cards_for_player(&$deck, &$discard_pile, &$hands, $player, $count = 1)
    {
        $drawn = [];
        for ($i = 0; $i < $count; $i++) {
            if (empty($deck)) {
                // นำกองทิ้ง (ยกเว้นใบบนสุด) มาสับเป็นกองจั่วใหม่
                if (count($discard_pile) > 1) {
                    $top = array_pop($discard_pile);
                    $deck = $discard_pile;
                    shuffle($deck);
                    $discard_pile = [$top];
                } else {
                    break;
                }
            }
            if (!empty($deck)) {
                $c = array_shift($deck);
                $drawn[] = $c;
                if (!isset($hands[$player])) {
                    $hands[$player] = [];
                }
                $hands[$player][] = $c;
            }
        }
        $deck = array_values($deck);
        $discard_pile = array_values($discard_pile);
        if (isset($hands[$player])) {
            $hands[$player] = array_values($hands[$player]);
        }
        return $drawn;
    }
}
