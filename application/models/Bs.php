<?php
class Bs extends CI_model {
    function tools() {
        $sx = '<div class="col-md-12">';
        $sx .= '<a href="' . base_url(PATH . 'basket/clean') . '" class="btn btn-outline-danger" style="margin-right: 10px;">' . msg('clean_selected') . '</a>';
        if ((isset($_SESSION['user'])) and (strlen($_SESSION['user']) > 0)) {
            $sx .= '<a href="' . base_url(PATH . 'basket/save') . '" class="btn btn-outline-primary" style="margin-right: 10px;">' . msg('save_selected') . '</a>';
        }
        $sx .= '<a href="' . base_url(PATH . 'basket/metrics') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('metrics') . '</a>';
        
        
        $sx .= '</br>';
        $sx .= '</br>';        
        $sx .= '<a href="' . base_url(PATH . 'basket/export/csv') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('csv_selected') . '</a>';
        $sx .= '<a href="' . base_url(PATH . 'basket/export/xls') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('xls_selected') . '</a>';
        //$sx .= '<a href="' . base_url(PATH . 'basket/export/rdf') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('rdf_selected') . '</a>';
        $sx .= '<a href="' . base_url(PATH . 'basket/export/doc') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('doc_selected') . '</a>';
        $sx .= '<a href="' . base_url(PATH . 'basket/export/bib') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('bib_selected') . '</a>';
        
        $sx .= '</div>';
        $sx .= '<div class="col-md-12">';
        $sx .= '<hr>';
        $sx .= '</div>';
        return ($sx);
    }
    
    function mark_clear() {
        
        if (isset($_SESSION)) {
            $a = $_SESSION;
            foreach ($a as $key => $value) {
                if (substr($key, 0, 1) == 'm') {
                    unset($_SESSION[$key]);
                }
            }
        }
    }
    
    function ajax_mark_all($key='',$vlr='')
    {
        $this->load->model('searchs');
        $this->load->model('elasticsearch');
        $js = '';
        $dd3 = get("dd3") . ';';
        
        $a = splitx(';', $dd3);
        if (count($a) > 0)
        {
            for ($r = 0; $r < count($a); $r++) {
                $id = 'm' . trim($a[$r]);
                $js .= '$("#chk' . $a[$r] . '").prop("checked", true);';
                $vlr2 = $this -> bs -> mark($a[$r], 'true');
            }
            
            /* Parte 2 */
            $t = get("type");
            $type = 'article';
            $term = get("q");
            $term = troca($term, '¢', '"');
            
            $_SESSION['year_s'] = get("year_s");
            $_SESSION['year_e'] = get("year_e");            
            
            $q = $this -> elasticsearch -> query($type, $term, $t, 0, 1); 
            $hits = $q['hits']['hits'];
            for ($r=0;$r < count($hits);$r++)
            {
                $hit = $hits[$r];
                $ht = round($hit['_id']);
                $vlr2 = $this -> bs -> mark($ht, 'true'); 
            }
        } else {
            echo 'Ops';
        }
        echo $vlr2 . cr();
        echo '<script>' . $js . '</script>';
        
    }
    
    function ajax_mark($key = '', $vlr = '') {
        $js = '';
        if (strlen($key) > 0) {
            $id = 'm' . $key;
            $vlr = 'true';
            if (isset($_SESSION[$id])) {
                if ($_SESSION[$id] == '1') {
                    $vlr = 'false';
                }
                $vlr2 = $this -> bs -> mark($key, $vlr);
                echo $vlr2;
            } else {
                $vlr = get("dd2");
                $vlr2 = $this -> bs -> mark($key, $vlr);
                echo $vlr2;
            }
        } else {
            $dd3 = get("dd3") . ';';
            $a = splitx(';', $dd3);
            for ($r = 0; $r < count($a); $r++) {
                $id = 'm' . trim($a[$r]);
                $js .= '$("#chk' . $a[$r] . '").prop("checked", true);';
                $vlr2 = $this -> bs -> mark($a[$r], 'true');
            }
            echo $vlr2 . cr();
            echo '<script>' . $js . '</script>';
        }
        return ('');
    }
    
    function basket($type = '1') {
        $sx = '';
        $s = $_SESSION;
        $tot = 0;
        switch($type) 
        {
            case '1' :
                $a = array();
                foreach ($s as $key => $value) 
                {
                    if (substr($key, 0, 1) == 'm') 
                    {
                        $tot++;
                        $key = substr($key, 1, strlen($key));
                        $file = 'c/' . $key . '/name.nm';
                        if (file_exists($file)) 
                        {
                            $fr = file_get_contents($file);
                            $fr = troca($fr, '<b>', '_b_');
                            $fr = troca($fr, '</b>', '_bb_');
                            $fr = strip_tags($fr);
                            $fr = troca($fr, '_b_', '<b>');
                            $fr = troca($fr, '_bb_', '</b>');
                            
                            $link = '<a href="' . base_url(PATH . 'v/' . $key) . '">';
                            $fr .= ' Disponível em: &lt;' . $link . base_url(PATH . 'v/' . $key) . '</a>' . '&gt;.';
                            $fr .= ' Acesso em: ' . date("d") . '-' . msg('mes_' . date("m")) . '-' . date("Y") . '.';
                            array_push($a, $fr);
                        }
                    }
                }
                asort($a);
                foreach ($a as $key => $value) {
                    $sx .= '<p style="margin-bottom: 10px;">' . $value . '</p>' . cr();
                }
            break;
            
            /****************************** Default **************/
            default :
            foreach ($s as $key => $value) 
            {
                if (substr($key, 0, 1) == 'm') 
                {
                    $key = substr($key, 1, strlen($key));
                    $tot++;
                    $jnl = 0;
                    $img = 'img/cover/cover_issue_' . $jnl . '.jpg';
                    if (!is_file($img)) {
                        //echo '==>' . $img . '<br>';
                        $img = 'img/cover/cover_issue_0.jpg';
                        //$sx .= '['.$jnl.']';
                    }
                    $sx .= '<div class="col-1 " style="margin-bottom: 15px;"><img src="' . HTTP . $img . '" class="img-fluid"></div>';
                    $sx .= '<div class="col-11 " style="margin-bottom: 15px;">';
                    $sx .= $this -> bs -> checkbox($key);
                    $sx .= '<a href="' . base_url(PATH . 'v/' . $key) . '" target="_new' . $key . '" class="refs">';
                    $sx .= $this -> frbr -> show_v($key);
                    $sx .= '</a>';
                    $sx .= '</div>';
                }
            }
        }
        
        if ($tot > 0) {
            
        } else {
            $sx .= '<div class="col-md-12">';
            $sx .= bs_alert('warning', msg('basket_empty'));
            $sx .= '</div>';
        }
        return ($sx);
    }
    
    function sels()
    {
        $a = array();
        $s = $_SESSION;
        foreach ($s as $key => $value) {
            if (substr($key, 0, 1) == 'm') {
                if ($value == '1') {
                    array_push($a,sonumero($key));
                }
            }
        } 
        return($a);           
    }
    
    function selected() {
        $s = $_SESSION;
        $tot = 0;
        foreach ($s as $key => $value) {
            if (substr($key, 0, 1) == 'm') {
                if ($value == '1') {
                    $tot++;
                }
            }
        }
        if ($tot > 0) {
            $tot = '<a href="'.base_url(PATH . 'basket').'">
            <div class="text-center" style="border: 1px solid #8080ff; border-radius: 6px;  padding: 5px;">
            '.msg('Selected').'
            <br>
            <span class="btn-primary" style="padding: 0px 10px; border-radius: 10px;">' . $tot . '</span>
            </div></a>'.cr();            
        } else {
            $tot = '';
        }
        
        return ($tot);
    }
    
    function mark($key, $dd2 = '') {
        if (strlen($dd2) == 0) {
            $dd2 = get("dd2");
        }
        
        $key = trim($key);
        if ($dd2 == 'true') {
            $vlr = 1;
            $_SESSION['m' . $key] = $vlr;
        } else {
            $vlr = 0;
            unset($_SESSION['m' . $key]);
        }
        $vlr = $this -> selected();
        return ($vlr);
    }
    
    function script_all($s,$q='',$t='') {
        $sx = '<div class="col-md-12">';
        $sx .= '<span onclick="mark_all();" style="cursor: pointer;">' . msg('select_page') . '</span>' . cr();
        $sx .= ' | <span onclick="mark_all_pages();" style="cursor: pointer;">' . msg('select_all_page') . '</span>' . cr();
        
        $sx .= '
        <script>
        function mark_all()
        {                                
            var ok = "' . $s . '";
            $.ajax({
                type: "POST",
                url: "' . base_url(PATH . 'mark/') . '",
                data: { dd1: "", dd2: "", dd3: ok }
            }).done(function( data ) {
                $("#basket").html(data);
            });                     
        }                
        </script>
        ';
        
        $dts = '';
        if (strlen(get("year_s")) > 0) { $dts .= ', year_s :"'.round(get("year_s")).'"'; }
        if (strlen(get("year_e")) > 0) { $dts .= ', year_e :"'.round(get("year_e")).'"'; }                
        $sx .= '
        <script>
        function mark_all_pages()
        {                                
            var ok = "' . $s . '";
            
            $.ajax({
                type: "POST",
                url: "' . base_url(PATH . 'mark_all/') . '",
                data: { q: "'.troca($q,'"','¢').'", dd3: ok, type: "'.$t.'", p: 0 '.$dts.' }
            }).done(function( data ) {
                $("#basket").html(data);
            });                     
        }                
        </script>
        ';                
        $sx .= '</div>';
        return ($sx);
    }
    
    function script() {
        $sx = '';
        if (!isset($jss)) {
            $sx = '<script>
            function mark(ms,ta)
            {                                
                var ok = ta.checked;
                $.ajax({
                    type: "POST",
                    url: "' . base_url(PATH . 'mark/') . '"+ms,
                    data: { dd1: ms, dd2: ok }
                }).done(function( data ) {
                    $("#basket").html(data);
                });                     
            }               
            function mark_ch(ms)
            {
                var ok = $("#check").prop("checked");
                if (ok == true)
                {
                    ok = "false";
                    $("#check").prop("checked", false);
                    $("#select").attr("src","' . base_url('img/icone/icone_select_on.png') . '");
                } else {
                    ok = "true";
                    $("#check").prop("checked", true);
                    $("#select").attr("src","' . base_url('img/icone/icone_select_off.png') . '");
                }
                
                $.ajax({
                    type: "POST",
                    url: "' . base_url(PATH . 'mark/') . '"+ms,
                    data: { dd1: ms, dd2: ok }
                }).done(function( data ) {
                    $("#basket").html(data);
                });
            }        
            </script>
            ';
        }
        return ($sx);
    }
    
    function change($key) {
        $sx = $this -> script();
        $cd = 'on';
        $chk = '';
        if (strlen($key) > 0) {
            $id = 'm' . $key;
            if (isset($_SESSION[$id])) {
                if ($_SESSION[$id] == '1') {
                    $cd = 'off';
                    $chk = 'checked';
                }
            }
        }
        $img = '<img src="' . base_url('img/icone/icone_select_' . $cd . '.png') . '" height="42" id="select" onclick="mark_ch(' . $key . ');" class="icone_nets">';
        $sx .= $img;
        $sx .= '<input type="checkbox" id="check" ' . $chk . ' style="display: none;">';
        return ($sx);
    }
    
    function checkbox($key) {
        global $jss;
        $sx = $this -> script();
        
        if (strlen($key) > 0) {
            $id = 'm' . $key;
            if (isset($_SESSION[$id])) {
                if ($_SESSION[$id] == '1') {
                    $chk = 'checked';
                } else {
                    $chk = '';
                }
                
            } else {
                $chk = '';
            }
            $sx .= '<input type="checkbox" id="chk' . $key . '" onchange="mark(' . $key . ',this);" ' . $chk . '> ';
            //$sx .= $key . ' - ';
        }
        return ($sx);
    }
    
    function mark_export_csv() {
        $file = 'brapci_csv_' . date("YmdHi") . '.txt';
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $file);
        $sx = '';
        $s = $_SESSION;
        $tot = 0;
        
        $a = array();
        $sx = '';
        $sx .= '' . msg('author');
        $sx .= ',' . msg('title');
        $sx .= ',' . msg('source');
        $sx .= ',' . msg('issue');
        $sx .= ',' . msg('year');
        $sx .= ',' . msg('session');
        $sx .= ',' . msg('keywords');
        $sx .= ',' . msg('abstract');
        $sx .= ',' . msg('id');
        $sx .= ',' . msg('link');
        $sx .= ',' . cr();
        foreach ($s as $key => $value) 
        {
            if (substr($key, 0, 1) == 'm') 
            {
                $tot++;
                $key = substr($key, 1, strlen($key));
                $file = 'c/' . $key . '/name.csv';
                if (file_exists($file)) {
                    $fr = file_get_contents($file);
                    $sx .= $fr . cr();
                }
            }
        }
        echo utf8_decode($sx);
    }

    function mark_export_bib() {
        $file = 'brapci_bib_' . date("YmdHi") . '.bib';
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $file);
        $sx = '';
        $s = $_SESSION;
        $tot = 0;
        
        foreach ($s as $key => $value) 
        {
            if (substr($key, 0, 1) == 'm') 
            {
                $tot++;
                $key = substr($key, 1, strlen($key));
                $file = 'c/' . $key . '/name.bib';
                if (file_exists($file)) {
                    $fr = file_get_contents($file);
                    $sx .= $fr . cr();
                }
            }
        }
        echo (utf8_encode($sx));
    }

    function mark_export_xls() {
        $file = 'brapci_' . date("YmdHi") . '.xls';
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $file);
        $sx = '';
        $s = $_SESSION;
        $tot = 0;
        
        $a = array();
        $sx = '<table>' . cr();
        $sx .= '<tr><th>' . msg('author') . '</th>';
        $sx .= '<th>' . msg('title') . '</th>';
        $sx .= '<th>' . msg('source') . '</th>';
        $sx .= '<th>' . msg('issue') . '</th>';
        $sx .= '<th>' . msg('year') . '</th>';
        $sx .= '<th>' . msg('session') . '</th>';
        $sx .= '<th>' . msg('keywords') . '</th>';
        $sx .= '<th>' . msg('abstract') . '</th>';
        $sx .= '<th>' . msg('id') . '</th>';
        $sx .= '<th>' . msg('link') . '</th>';
        $sx .= '</tr>' . cr();
        foreach ($s as $key => $value) {
            if (substr($key, 0, 1) == 'm') {
                $tot++;
                $key = substr($key, 1, strlen($key));
                $file = 'c/' . $key . '/name.xls';
                if (file_exists($file)) {
                    $fr = file_get_contents($file);
                    $sx .= $fr . cr();
                }
            }
        }
        $sx .= '</table>';
        
        echo '<html>';
        echo '<body>';
        echo utf8_decode($sx);
        echo '</body>';
        echo '</html>';
        
    }
    function mark_export_doc() {
        $file = 'brapci_' . date("YmdHi") . '.doc';
        header('Content-Encoding: UTF-8');
        //header('Content-type: application/vnd.ms-word; charset=UTF-8');
        header('Content-type: application/vnd.ms-word; charset=ISO-8859-1');
        header('Content-Disposition: attachment; filename=' . $file);
        $sx = '';
        $s = $_SESSION;
        $tot = 0;
        
        $a = array();
        foreach ($s as $key => $value) {
            if (substr($key, 0, 1) == 'm') {
                $tot++;
                $key = substr($key, 1, strlen($key));
                $file = 'c/' . $key . '/name.nm';
                if (file_exists($file)) {
                    $fr = file_get_contents($file);
                    $fr = troca($fr, '<b>', '_b_');
                    $fr = troca($fr, '</b>', '_bb_');
                    $fr = strip_tags($fr);
                    $fr = troca($fr, '_b_', '<b>');
                    $fr = troca($fr, '_bb_', '</b>');
                    
                    $link = '<a href="' . base_url(PATH . 'v/' . $key) . '">';
                    $fr .= ' Disponível em: &lt;' . $link . base_url(PATH . 'v/' . $key) . '</a>' . '&gt;.';
                    $fr .= ' Acesso em: ' . date("d") . '-' . msg('mes_' . date("m")) . '-' . date("Y") . '.';
                    array_push($a, $fr);
                }
            }
        }
        asort($a);
        foreach ($a as $key => $value) {
            $sx .= '<p style="margin-bottom: 10px;">' . $value . '</p>' . cr();
        }
        echo '<html>';
        echo '<body>';
        echo '<h1>' . utf8_decode(msg('References')) . '</h1>' . cr();
        echo utf8_decode($sx);
        echo '</body>';
        echo '</html>';
    }
    
    function mark_form_inport() {
        $form = new form;
        $cp = array();
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$T80:10', '', msg('IDs'), true, true));
        array_push($cp, array('$M1', '', msg('IDs_info'), false, false));
        array_push($cp, array('$B8', '', msg('inport'), false, true));
        
        $tela = $form -> editar($cp, '');
        $tela = '<div class="row"><div class="col-md-12 col-12">' . $tela . '</div></div>';
        
        if ($form -> saved > 0) {
            $n = troca(get("dd1"), chr(13), ';');
            $n = troca($n, chr(10), '');
            $ln = splitx(';', $n);
            for ($r = 0; $r < count($ln); $r++) {
                $nn = sonumero($ln[$r]);
                $_SESSION['m' . $nn] = 1;
            }
            $tela .= bs_alert('success', msg('Was') . ' ' . count($ln) . ' ' . msg('inported_registers'));
        }
        
        return ($tela);
    }
    
    function mark_save($id = 0) {
        if (!(isset($_SESSION['id'])) or (sonumero($_SESSION['id']) == 0)) {
            return (bs_alert('danger', 'Session not initialized'));
        }
        $form = new form;
        $cp = array();
        
        $s = '';
        $a = $_SESSION;
        $total = 0;
        foreach ($a as $key => $value) {
            if (substr($key, 0, 1) == 'm') {
                $s .= sonumero($key) . '; ';
                $total++;
            }
        }
        $_POST['dd2'] = $s;
        array_push($cp, array('$H8', 'id_bb', '', false, false));
        array_push($cp, array('$S100', 'bb_title', msg('ID_save_name'), true, true));
        array_push($cp, array('$T100:5', 'bb_sel', msg('IDs'), true, true));
        array_push($cp, array('$HV', 'bb_user', $_SESSION['id'], false, true));
        array_push($cp, array('$HV', 'bb_update', date("Y-m-d"), false, true));
        array_push($cp, array('$C1', 'bb_public', msg('bb_public'), false, true));
        array_push($cp, array('$HV', 'bb_total', $total, false, true));
        //array_push($cp,array('$HV','bb_session',$_SESSION['session'],true,true));
        
        array_push($cp, array('$B8', '', msg('save_selection'), false, true));
        
        $tela = $form -> editar($cp, '_bibliographic_selections');
        $tela = '<div class="row"><div class="col-md-12 col-12">' . $tela . '</div></div>';
        
        if ($form -> saved > 0) {
            $tela = bs_alert('success', msg('bb_success'));
        }
        
        return ($tela);
    }
    
    function mark_saved() {
        if (!(isset($_SESSION['id'])) or (sonumero($_SESSION['id']) == 0)) {
            return (bs_alert('danger', 'Session not initialized'));
        }
        $id = $_SESSION['id'];
        $sql = "select * from _bibliographic_selections 
        where bb_user = $id 
        order by bb_update";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<ul>';
        for ($r=0;$r < count($rlt);$r++)
        {
            $line = $rlt[$r];
            $sx .= '<li>';
            $sx .= $line['bb_title'];
            $sx .= ' ';
            $sx .= '('.$line['bb_total'].')';
            $sx .= ' ';
            $sx .= msg('saved_in').' '.stodbr($line['bb_update']);
            $sx .= '<hr><code>'.$line['bb_sel'].'</code>';
            $sx .= '</li>';
        }
        $sx .= '</ul>';
        return($sx);
    }
}
?>
