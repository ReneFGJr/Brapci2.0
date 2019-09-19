<?php
class handle extends CI_model {
    var $pass = '';
    var $prefix = '20.500.11959';
    var $domain = 'brapci/';
    var $contact = 'brapcici@gmail.com';

    function form() {
        $form = new form;
        $cp = array();
        array_push($cp, array('$H8', '', '', false, false));

        $op = '20.500.11959:20.500.11959 (CEDAP)';
        array_push($cp, array('$O ' . $op, '', 'Handle', true, true));
        array_push($cp, array('$S100', '', 'Handle Nº', true, true));
        array_push($cp, array('$S100', '', 'Link', true, true));
        array_push($cp, array('$S100', '', 'e-mail', true, true));
        $tela = $form -> editar($cp, '');

        if ($form -> saved > 0) {
            $this -> prefix = get("dd1");
            $this -> domain = "";
            $hdl = get("dd2");
            $url = get("dd3");
            $email = get("dd4");
            $c = $this -> cmd_header();
            $c .= $this -> create_handle($hdl, $url);
            $fhdl = fopen('/hs/cmd/cmd1', 'w+');
            fwrite($fhdl, $c);
            fclose($fhdl);
                       
            $sql = "insert into handle 
                        (hdl_name, hdl_url, hdl_status, hdl_email)
                        values
                        ('$hdl','$url',1,'$email')";
            $rlt = $this->db->query($sql);
            $tela = '<h1>Handle registrado</h1>';
        }

        return ($tela);
    }

    function handle_register() {
        $file = '/hs/cmd/status';
        if (file_exists($file)) {
            $sql = "update handle set hdl_status = 2 where hdl_status = 1";
            $this -> db -> query($sql);
            unlink($file);
            echo '<br>=> Processamento ok!';
        } else {
            echo '<br>=> Não processado!';
        }

        /* Seleciona todos os não registrados e em processo de registro */
        $sql = "select * from handle where hdl_status <= 1 limit 5000";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        $c = $this -> cmd_header();

        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $c .= $this -> create_handle($line['hdl_name'], $line['hdl_url']);

            $sql = "update handle set hdl_status = 1 where id_hdl = " . $line['id_hdl'];
            $this -> db -> query($sql);
        }

        $hdl = fopen('/hs/cmd/cmd1', 'w+');
        fwrite($hdl, $c);
        fclose($hdl);

        $output = shell_exec('sh /hs/cmd/c');
        echo "=><pre>$output</pre>";

        echo '<pre>Gerado com ' . strlen($c) . ' bytes</pre>';
        exit ;
    }

    function hdl_register($hdl, $url) {
        $sql = "select * from handle where hdl_name = '$hdl'";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        if (count($rlt) == 0) {
            $sql = "insert into handle (hdl_name,hdl_url) values ('$hdl','$url')";
            $rrr = $this -> db -> query($sql);
        } else {

        }
    }

    function cmd_header() {
        $c = 'AUTHENTICATE PUBKEY:300:0.NA/20.500.11959' . cr();
        ;
        $c .= '/hs/svr_2/admpriv.bin|448545ct' . cr();
        ;
        $c .= cr();

        $c .= 'HOME 143.54.114.150:2641:TCP' . cr();
        $c .= '0.NA/' . $this -> prefix . cr();
        $c .= cr();
        return ($c);
    }

    function create_handle($hdl, $url = '') {
        $pre = $this -> prefix;
        $pre2 = $this -> domain;
        $c = '';
        $c .= 'CREATE ' . $pre . '/' . $pre2 . $hdl . cr();
        $c .= '100 HS_ADMIN 86400 1110 ADMIN 200:111111111111:0.NA/' . $pre . cr();
        $c .= '3 URL 86400 1110 UTF8 ' . $url . cr();
        $c .= '7 EMAIL 86400 1110 UTF8 ' . $this -> contact . cr();
        $c .= '9 DESC 86400 1110 UTF8 Base de Dados Referencial de Artigos de Periocos em Ciencia da Informacao (Brapci)' . cr();
        $c .= cr();

        return ($c);
    }

}
?>
