<?php
class events extends CI_model {
    function click($id)
    {
        $sql = "update event set ev_count = (ev_count+1) where id_ev = ".round($id);
        $rlt =$this->db->query($sql);
        $line = $this->le($id);
        if (count($rlt) > 0)
        {
            redirect($line['ev_url']);
        } else {
            redirect(PATH);
        }
    }
    function events_lista()
    {
        $sx = '<div class="col-md-6" style="border-left: 2px solid #E0E0E0;">';
        $sx .= '<h1>'.msg('Events').'</h1>';
        $sx .= '<ul>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'config/event/row').'">'.msg('event_list').'</a></li>';
        $sx .= '</ul>';
        $sx .= '</div>';
        return($sx);
    }
    function edit($id = '',$i2='',$i3='') {
        $cp = array();
        array_push($cp,array('$H8','id_ev','',false,false));
        array_push($cp,array('$S100','ev_name',msg('ev_name'),true,true));
        array_push($cp,array('$S100','ev_place',msg('ev_place'),true,true));
        array_push($cp,array('$S100','ev_url',msg('ev_url'),false,true));
        array_push($cp,array('$T80:4','  ev_description',msg('ev_description'),false,true));
        array_push($cp,array('$D8','ev_data_start',msg('ev_data_start'),true,true));
        array_push($cp,array('$D8','ev_data_end',msg('ev_data_end'),true,true));
        array_push($cp,array('$O 1:SIM&0:NÃO','ev_ative',msg('ev_ative'),true,true));

        $form = new form;
        $form->id = $id;
        $sx = $form->editar($cp,'event');
        if ($form->saved > 0)
        {
            redirect(base_url(PATH.'config/event/row'));
        }
        return($sx);
    }

    function logo($id = '',$i2='',$i3='') {
        $cp = array();
        array_push($cp,array('$H8','id_ev','',false,false));
        array_push($cp,array('$A','',msg('change_logo'),false,false));
        array_push($cp,array('$FILE','',msg('ev_logo'),true,true));

        $form = new form;
        $form->id = $id;
        $sx = $form->editar($cp,'event');

        if (isset($_FILES['fileToUpload']))
        {
            $temp = $_FILES['fileToUpload']['tmp_name'];
            $name = LowerCaseSql($_FILES['fileToUpload']['name']);
            $name = troca($name,' ','_');
            $dir = 'img';
            dircheck($dir);
            $dir = 'img/events/';
            dircheck($dir);            
            $logo = $dir.$name;
            if (move_uploaded_file($temp, $logo))
            {
                $sql = "update event set ev_image = '$logo' where id_ev = ".round($id);
                $this->db->query($sql);
                redirect(base_url(PATH.'config/event/view/'.$id));
            } else {
                $sx .= '<span style="color:red">Erro ao transferir arquivo</span>';;
            }
        }
        return($sx);
    }    

    function view($id = '',$i2='',$i3='') {
        $data = $this->le($id);
        $sx = '<div class="col-md-9" style="border-left: 2px solid #E0E0E0;">';
        $sx .= '<h1>'.$data['ev_name'].'</h1>';
        $sx .= '</div>';
        $sx .= '<div class="col-md-3 text-center">';
        $img = $data['ev_image'];
        if (!file_exists($img))
        {
            $img = 'img/events/nologo.png';
        }
        $sx .= '<img src="'.base_url($img).'" class="img-fluid">';
        $sx .= '<br/>';
        $sx .= '<a href="'.base_url(PATH.'config/event/logo/'.$data['id_ev']).'" class="small">';
        $sx .= msg('change_logo');
        $sx .= '</a>';
        $sx .= ' | ';
        $sx .= '<a href="'.base_url(PATH.'config/event/edit/'.$data['id_ev']).'" class="small">';
        $sx .= msg('edit');
        $sx .= '</a>';

        $sx .= '</div>';

        $sx .= '<div class="col-md-8" style="border-left: 2px solid #E0E0E0;">';
        $sx .= msg('Date').': '.stodbr($data['ev_data_start']).'-'.stodbr($data['ev_data_end']).'<br/>';
        $sx .= msg('Place').': '.$data['ev_place'].'<br/>';
        $sx .= msg('Site').': '.'<a href="'.$data['ev_url'].'" target="_new">'.$data['ev_url'].'<br/>';
        $sx .= '</div>';

        return($sx);
    } 

    function le($id)
    {
        $sql = "select * from event where id_ev = ".round($id);
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0)
        {
            $line = $rlt[0];    
        } else {
            $line = array();
        }
        return($line);
    }   

    function row($id = '') {
        $form = new form;

        $form -> fd = array('id_ev', 'ev_name', 'ev_place','ev_ative','ev_count');
        $form -> lb = array('id', msg('ev_name'), msg('ev_place'),msg('ev_active'),msg('ev_count'));
        $form -> mk = array('', 'L', 'L', 'L', 'L', 'L');

        $form -> tabela = 'event';
        $form -> see = true;
        $form -> novo = true;
        $form -> edit = true;

        $form -> row_edit = base_url(PATH.'config/event/edit');
        $form -> row_view = base_url(PATH.'config/event/view');
        $form -> row = base_url(PATH.'config/event/row');

        $sx = '<div class="col-md-12">'.row($form, $id).'</div>';
        return ($sx);
    }


    function events_actives($tp = 0) {
        $sql = "select * from event 
        where ev_data_end >= '" . date("Y-m-d") . "' and ev_ative = 1
        order by ev_data_start";
        if ($tp == 1) {
            $sql = "select * from event 
            where ev_ative = 1
            order by ev_data_start";
        }
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $sx = '';

        $sx .= '<h4>' . msg('Next_events') . '</h4>';
        $sx .= '<table width="100%" border=0>' . cr();

        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $url = $line['ev_url'];

            $link = '<a href="' . base_url(PATH.'event/click/'.$line['id_ev']) . '" target="_new" style="font-size: 120%; color: #808080;">';

            $sx .= '<tr valign="top" style="border-top: 1px solid #c0c0c0;">
                    <td width="25%" align="center" style="padding: 10px;">';
            $sx .= '<img src="' . base_url($line['ev_image']) . '" style="border:0px solid #ffffff;" height="40" align="center">';
            $sx .= '</td><td>';
            $sx .= '&nbsp;';
            $sx .= '</td><td style="line-height: 1.2;" style="padding: 10px;" cellalign="center">';
            //$sx .= $link . '<b>'.$line['ev_name'] . '</b>'.'</a>';
            //$sx .= '<br>';

            $sx .= '<i style="line-height: 0.2;">';
            $sx .= $this -> show_datas($line);
            $sx .= '</i>';
            $sx .= '<br>';
            $sx .= '<span class="event_place">' . $line['ev_place'] . '</span>';
            $sx .= '</td>';
            $sx .= '<td style="padding: 10px;">';
            if ($url != '')
            {
                $sx .= $link;
                $sx .= msg('event_site');
                $sx .= '</a>';
            } else {
                $sx .= msg('not_informed');
            }
            $sx .= '</td>';
            $sx .= '</tr>' . cr();
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
        $d1a = stodbr(sonumero($d1));
        $d2a = stodbr(sonumero($d2));
        if (substr($d1, 0, 6) == substr($d2, 0, 6)) {
            $id = 1;
        }
        switch($id) {
            case 1 :
            $sx .= substr($d1a,0,2) . '-' . $d2a;
            break;
            default :
            $sx = $d1a . '-' . $d2a;
            break;
        }

        return ($sx);
    }

}
?>