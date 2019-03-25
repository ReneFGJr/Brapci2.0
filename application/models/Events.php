<?php
class events extends CI_model {

    function events_actives($tp = 0) {
        $sql = "select * from event 
                where ev_data_end >= " . date("Ymd") . " and ev_ative = 1
                order by ev_data_start";
        if ($tp == 1) {
            $sql = "select * from event 
                where ev_ative = 1
                order by ev_data_start";
        }
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';

        //$sx = '<div class="col-md-6" style="border-left: 2px solid #E0E0E0;">';
        $sx .= '<h4>' . msg('Next_events') . '</h4>';
        $sx .= '<table width="100%" style="margin-bottom: 30px;">' . cr();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $url = $line['ev_url'];
            if (strlen($url) == 0) {
                $url = '#';
            }
            $link = '<a href="' . $url . '" target="_new" style="font-size: 120%; color: #808080;">';

            $sx .= '<tr valign="top"><td width="25%" align="right">';
            $sx .= $link . '<img src="' . base_url($line['ev_image']) . '" style="border:0px solid #ffffff;" height="40" align="right"></a>';
            $sx .= '</td><td>';
            $sx .= '&nbsp;';
            $sx .= '</td><td style="line-height: 1.2;">';
            //$sx .= $link . '<b>'.$line['ev_name'] . '</b>'.'</a>';
            //$sx .= '<br>';

            $sx .= '<i style="line-height: 0.2;">';
            $sx .= $this -> show_datas($line);
            $sx .= '</i>';
            $sx .= '<br>';
            $sx .= '<span class="event_place">' . $line['ev_place'] . '</span>';
            $sx .= '</td></tr>' . cr();
            $sx .= '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>' . cr();
        }
        $sx .= '</table>';
        $sx .= '<a href="' . base_url(PATH . 'event/') . '" class="small"><i>' . msg('event_all') . '</i></a>';
        $sx .= ' | ';
        $sx .= '<a href="' . base_url(PATH . 'event/register/') . '" class="small"><i>' . msg('register_our_event') . '</i></a>';
        //$sx .= '</div>';
        $sx .= '<style> .event_place { font-size: 110%; font-weight: bold; color: #a0a0a0; } </style>';
        //$sx .= '<style> .row { border: 1px solid #ff0000; } </style>';
        return ($sx);
    }

    function show_datas($line, $id = 0) {
        $sx = '';
        $d1 = $line['ev_data_start'];
        $d2 = $line['ev_data_end'];
        $mes = meses_short();
        $d1a = substr($d1, 6, 2) . '/' . $mes[round(substr($d1, 4, 2))] . '/' . substr($d1, 0, 4);
        $d2a = substr($d2, 6, 2) . '/' . $mes[round(substr($d2, 4, 2))] . '/' . substr($d2, 0, 4);
        if (substr($d1, 0, 6) == substr($d2, 0, 6)) {
            $id = 1;
        }
        switch($id) {
            case 1 :
                $sx .= substr($d1, 6, 2) . '-' . $d2a;
                break;
            default :
                $sx = $d1a . '-' . $d2a;
                break;
        }

        return ($sx);
    }

}
?>