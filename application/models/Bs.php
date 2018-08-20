<?php
class Bs extends CI_model {
    function tools() {
        $sx = '<div class="col-md-12">';
        $sx .= '<a href="' . base_url(PATH . 'basket/clean') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('clean_selected') . '</a>';
        $sx .= '<a href="' . base_url(PATH . 'basket/export/xls') . '" class="btn btn-outline-secondary" style="margin-right: 10px;">' . msg('xls_selected') . '</a>';
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
                if (substr($key,0,1) == 'm')
                    {
                        unset($_SESSION[$key]);
                    }
            }
        }
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

    function basket() {
        $sx = '';
        $s = $_SESSION;
        $tot = 0;
        foreach ($s as $key => $value) {
            if (substr($key, 0, 1) == 'm') {
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
        
        if ($tot > 0)
            {
                
            } else {
                $sx .= '<div class="col-md-12">';
                $sx .= bs_alert('warning',msg('basket_empty'));
                $sx .= '</div>';
            }
        return ($sx);
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
            $tot = '<span class="btn-primary" style="padding: 0px 10px; border-radius: 10px;">' . $tot . '</span>';
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

    function script_all($s) {
        $sx = '<div class="col-md-12">';
        $sx .= '<span onclick="mark_all();" style="cursor: pointer;">' . msg('select_all') . '</span>' . cr();
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
    function mark_export_csv()
        {
            $file = 'brapci_'.date("YmdHi").'.csv';
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename='.$file);
            
            
            
        }
}
?>
