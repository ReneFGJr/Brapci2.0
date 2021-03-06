<?php
class frad extends CI_model {

    function production($id) {
        $rs = $this -> frbr_core -> le_data($id, 'hasAuthor');
        $pub = array();
        $sub = array();
        $aut = array();
        $year = array();
        $auth = array();
        
        for ($r = 0; $r < count($rs); $r++) {
            $ln = $rs[$r];

            $id = $ln['d_r1'];
            $re = $this -> frbr_core -> le_data($id);
            for ($n = 0; $n < count($re); $n++) {
                $ns = $re[$n];
                $ids = $ns['d_r1'];
                $idr = $ns['d_r2'];
                $name = trim($ns['n_name']);
                $prop = trim($ns['c_class']);
                switch($prop) {
                    case 'hasIssueOf' :
                        $ya = $this -> frbr_core -> le_data($ids, 'dateOfPublication');
                        if (count($ya) > 0) {
                            array_push($year, $ya[0]['n_name']);
                        }
                        break;
                    case 'hasAuthor' :
                        array_push($aut, $name . ':' . $idr);
                        if (isset($auth[$name]))
                            {
                                $auth[$name]++;
                            } else {
                                $auth[$name] = 1;
                            }
                        break;
                    case 'hasSubject' :
                        array_push($sub, $name);
                        break;
                    case 'isPubishIn' :
                        array_push($pub, $name);
                        break;
                }
            }
        }
        asort($pub);
        asort($aut);
        asort($sub);
        asort($year);

        /**** Autores ***/
        $name = '';
        $sxa = '';
        foreach ($aut as $key => $value) {
            $value = trim($value);
            if (($value != $name) and (strlen($value) > 8)) {
                
                $vl = substr($value, 0, strpos($value, ':'));
                $vn = sonumero(substr($value, strpos($value, ':') + 1, strlen($value)));
                $tot = ' <span style="color: #d0d0d0;">'.$auth[$vl].'</span>';
                $link = '<a href="' . base_url(PATH . 'v/' . $vn) . '">';
                $sxa .= $link . $vl . '</a>' . $tot. '<br> ';
                $name = $value;
            }
        }
        /**** Subject ***/
        $name = '';
        $sb = array();
        foreach ($sub as $key => $value) {
            $name = $value;
            if (!(isset($sb[$name]))) {
                $sb[$name] = 1;
            } else {
                $sb[$name] = $sb[$name] + 1;
            }
        }
        $data = array();
        $data['authors'] = $sxa;
        $data['subject'] = $sb;
        return ($data);
    }

    function find_remissiva($id,$type='Person') {        
        switch($type)
            {
            case 'CorporateBody':
                $url = base_url(PATH . 'frad_corporate/' . $id);
                $sx = '<br><span class="btn btn-outline-primary" onclick="newxy(\'' . $url . '\',800,600);">Remissive</span>';
                break;
            default:
                $url = base_url(PATH . 'frad/' . $id);
                $sx = '<br><span class="btn btn-outline-primary" onclick="newxy(\'' . $url . '\',800,600);">Remissive</span>';
                break;                
            }
        
        return ($sx);
    }

    function find_remissiva_form($id, $name, $class="Person") {
        $ini = 0;
        $meth = 'frad';
        
        if ($class == 'CorporateBody')
            {
                $meth = 'frad_corporate';
            }
        
        if (strlen(get("ini") > 0)) {
            $ini = get("ini");
        }
        $d1 = get("dd1");
        $ac = get("action");
        if (strlen($d1) > 0) {
            $sql = "update rdf_concept set cc_use = " . $id . " where id_cc = " . $d1;
            $this -> db -> query($sql);
        }

        $nouse = 1;
        $f = $this -> frbr_core -> find_class($class);

        $wh = '';
        $name = troca($name, ',', '');
        $name = troca($name, ';', '');
        $name = troca($name, '-', ' ');
        $name = troca($name, '@', '');
        $fx = splitx(';', troca($name, ' ', ';') . ';');

        //for ($r = 0; $r < count($fx); $r++) {
        for ($r = $ini; $r < ($ini + 1); $r++) {
            if (strlen($wh) > 0) {
                $wh .= ' OR ';
            }
            $wh .= "(N1.n_name like '%" . $fx[$r] . "%') ";
        }
        $sql = "select N1.n_name as n_name, N1.n_lang as n_lang, C1.id_cc as id_cc,
                       N2.n_name as n_name_use, N2.n_lang as n_lang_use, C2.id_cc as id_cc_use         
                        FROM rdf_concept as C1
                        INNER JOIN rdf_name as N1 ON C1.cc_pref_term = N1.id_n
                        LEFT JOIN rdf_concept as C2 ON C1.cc_use = C2.id_cc
                        LEFT JOIN rdf_name as N2 ON C2.cc_pref_term = N2.id_n
                        where C1.cc_class = " . $f . " AND C1.cc_use = 0 AND ($wh) 
                        AND (C1.id_cc <> $id)
                        ORDER BY N1.n_name";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();

        $sx = '';
        if (count($rlt) > 0) {
            $sx .= '<div class="row">' . cr();
            $sx .= '<div class="col-md-12">';

            $sx .= '<form method="post">' . cr();
            $sx .= '<select size="10" style="width: 100%; font-size: 150%;" name="dd1">';
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                if (round($line['id_cc']) > 0) {
                    $sx .= '<option value="' . $line['id_cc'] . '">' . $line['n_name'] . '</option>' . cr();
                }
            }
            $sx .= '<option value=""></option>' . cr();
            $sx .= '</select>';
            $sx .= '<input type="submit" name="action" class="btn btn-outline-primary" value="' . msg('link') . '">' . cr();
            $sx .= '</form>';
            $sx .= '</div>';
            $sx .= '</div>';
        } else {
            $sx .= '<div class="row">' . cr();
            $sx .= '<div class="col-md-12">';
            $sx .= bs_alert("danger", msg('no_match'));
            $sx .= '</div>';
            $sx .= '</div>';
        }
        for ($r = 0; $r < count($fx); $r++) {
            $sx .= '<a href="' . base_url(PATH .$meth.'/' . $id) . '?ini=' . $r . '">Mth ' . (1 + $r) . '</a> | ';
        }

        return ($sx);
    }

}
?>