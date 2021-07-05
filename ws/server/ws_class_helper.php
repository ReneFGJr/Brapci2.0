<?php
class wsc //extends CI_Model
{
    var $dir = 'source/';
    var $phrase = array();
    var $phrase_ws = array();
    var $link = array();
    var $email = array();
    var $abrv = array();
    /********************************************************* IA */
    function lattesXML()
        {
            if (isset($_GET['q']))
            {
            $id = $_GET['q'];
            if (strlen($id) == 16)
            {
                $filename = 'lattes'.$id.'.zip';
                $dir = '_lattes';
                $file = $dir.'/'.$filename;
                dircheck($dir);
                if (!file_exists(($file)))
                {
                    $client = new SoapClient("http://servicosweb.cnpq.br/srvcurriculo/WSCurriculo?wsdl");
                    $param = array('id'=>$id);
                    $response = $client ->__call('getCurriculoCompactado', $param);
                    #$response = base64_decode($response);                
                    file_put_contents($file,$response);
                    sleep(0.5);
                }

                /********************************************************** */
                header('Cache-control: private');
                header('Content-Type: application/octet-stream');
                header('Content-Length: '.filesize($file));
                header('Content-Disposition: filename='.'lattes_'.$id.'.zip');
                ob_clean();
                flush();
                readfile($file);
                exit;
            } else {
                    $dt['erro'] = "Erro de indicador Size:".strlen($id);
            }
        } else {
            $dt['erro'] = "Parametro não informado";
        }            
        return($dt);
        }
    function genere($name)
    {
        $nf = $name;
        $n = explode(' ', $nf);
        $rst = 'X';
        for ($r = 0; $r < count($n); $r++) {
            $rs = $this->find($n[$r]);
            for ($nr = 0; $nr < count($rs); $nr++) {
                $fn = $rs[$nr];
                if (strpos($fn, 'PersonName') > 0) {
                    $rlt = $this->read($fn);
                    $male = $rlt['hasFrequencyMale'];
                    $female = $rlt['hasFrequencyFemale'];
                    if ($female > 0) {
                        $prob = round(10000 * ($male / ($male + $female))) / 100;
                    } else {
                        $prob = 100;
                    }
                    if ($prob > 90) {
                        $rst = 'Male';
                    }
                    if ($prob < 10) {
                        $rst = 'Female';
                    }
                }
                if ($rst != 'X') {
                    break;
                }
            }
            if ($rst != 'X') {
                break;
            }
        }
        $rs = array();
        $rs['genere'] = $rst;
        $rs['name'] = $nf;
        return ($rs);
    }

    /************************************************** I O Interface */
    function read($f)
    {
        if (file_exists($f)) {
            $txt = file_get_contents($f);
            $txt = (array)json_decode($txt);
        } else {
            $txt = array();
        }
        return ($txt);
    }

    function findshow($n)
    {
        $sx = '';
        $rlt = array();
        $t = $this->find($n);
        for ($r = 0; $r < count($t); $r++) {
            $l = $t[$r];
            $loop = 0;
            while (strpos($l, '/') and ($loop < 100)) {
                $loop++;
                $l = substr($l, strpos($l, '/') + 1, strlen($l));
            }
            $l = troca($l, '.json', '');
            array_push($rlt, $l);
            $sx .= '<a href="#">' . $l . '</a> ';
        }
        return ($sx);
    }

    function find($n)
    {
        $n = $this->trata($n);
        $rst = array();
        $f = $this->dir_t($n, FALSE);
        if (is_dir($f)) {
            $fd = dir($f);
            while ($file = $fd->read()) {
                if (strpos($file, '.json')) {
                    array_push($rst, $f . '/' . $file);
                }
            }
            $fd->close();
        }
        return ($rst);
    }

    function trata($name)
    {
        $name = ascii($name);

        $name = strtolower($name);
        $c = array('(', ')');
        $name = troca($name, $c, '');

        $c = array(' ', '-', ',', '.');
        $name = troca($name, $c, '_');
        return ($name);
    }

    function save($dt, $name, $force = 0)
    {
        $class = $dt['Class'];
        if (strlen($name) == 0) {
            echo "OPS Nome inválido ou vázio: [$name]";
            exit;
        }
        $name = $this->trata($name);

        $dir = $this->dir_t($name);
        $file = $dir . '/' . $class . '.json';

        /**************************** Exluir */
        $excluir=0;
        if ($excluir==1)
        {
            echo '<br>Buscando '.$file;
            if (file_exists($file))
                {
                    echo '==>EXCLUíDO';
                    unlink($file);
                }
            return("");
        }
        if ((!file_exists($file)) or ($force == 1)) {
            $dt['created'] = date("Y-m-d") . 'T' . date("H:i:s");
            $json = json_encode($dt);
            $e = file_put_contents($file, $json);
            //echo '<br>=File Saved ' . $file . '=';
        } else {
            //echo 'Erro';
        }
        return (TRUE);
    }

    function xml($dt)
    {
        $sx = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . cr();
        $sx .= '<rdf:RDF xmlns:cc="http://creativecommons.org/ns#" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:gn="http://www.geonames.org/ontology#" xmlns:owl="http://www.w3.org/2002/07/owl#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">';
        return ($sx);
    }

    function dir_t($t, $create = TRUE)
    {
        $t = ascii($t);
        $t = strtolower($t);
        $d1 = substr($t, 0, 1);
        $d2 = $t;
        $dir = $this->dir;
        if ($create == TRUE) {
            $this->dircheck($dir);
        }
        $dir .= $d1;
        if ($create == TRUE) {
            $this->dircheck($dir);
        }
        $dir .= '/' . $d2;
        if ($create == TRUE) {
            $this->dircheck($dir);
        }
        return ($dir);
    }

    function dircheck($dir)
    {
        $ok = 0;
        if (is_dir($dir)) {
            $ok = 1;
        } else {
            mkdir($dir);
        }
        return ($ok);
    }

    function terms($t)
    {
        $t = ascii($t);
        $t = strtolower($t);
        $t = explode(' ', $t);
    }

    function busca_01()
    {
        $w = $this->phrase;
        for ($r = 0; $r < count($w); $r++) {
            $word = '';
            for ($z = $r; $z < count($w); $z++) {
                if (strlen($word) > 0)
                    { 
                        $word .= '_';
                    }
                $word .= $w[$z];
                $rlt = $this->find($word);
                if (count($rlt) > 0)
                    {
                        $rst[$r] = $rlt;
                        $w[$r] = $word;
                        for ($q=($r+1);$q <= $z;$q++)
                            {
                                $w[$q] = 'xDELETEDx';
                            }
                    }
            }
        }
        $this->phrase = $w;
        $this->phrase_ws = $rst;
    }
    function link_internet($t)
        {
            $loop = 0;
            while ((($pos = strpos($t,'http:')) or ($pos = strpos($t,'https:'))) and ($loop < 50))
                {
                    $link = substr($t,$pos,strlen($t));
                    $link = substr($link.' ',0,strpos($link,' '));
                    array_push($this->link, $link);
                    $t = troca($t,$link,'link_'.strzero(count($this->link),3));
                    $loop++;
                }
            return($t);
        }
    function limpa_CR($t)
        {
            $t = troca($t,chr(13),' ');
            $t = troca($t,chr(10),' ');
            while (strpos($t,'  '))
                {
                    $t = troca($t,'  ',' ');
                }
            return($t);
        }
    function email($t)
        {
            $loop = 0;
            while (($pos = strpos($t,'@'))  and ($loop < 50))
                {
                    echo '===>'.$pos;
                    $link = substr($t,$pos,strlen($t));
                    echo '<h4>'.$link.'</h4>';
                    $link = substr($link.' ',0,strpos($link,' '));
                    array_push($this->link, $link);
                    $t = troca($t,$link,'link_'.strzero(count($this->link),3));
                    $loop++;
                }
       
            return($t);
        }
    function acronicos($t)
        {
            if (count($this->abrv) == 0)
                {
                    $dir = $this->dir.'../indexes';
                    $tb = file_get_contents($dir.'/acronico.json');
                    $tb = json_decode($tb);
                   foreach($tb as $ta => $to)
                        {
                            if ($pos=strpos($t,$ta))
                                {
                                    $t = troca($t,$ta,$to);
                                }
                        }
                }
            return($t);
        }        
    function separator_words($t)
        {
            $t = ascii($t);
            $t = troca($t,',',' ,');
            $t = troca($t,'"',' " ');
            $t = troca($t,chr(8),'');

            /* Trata excessões */
            $t = $this->acronicos($t);
            $t = $this->link_internet($t);
            $t = $this->email($t);

            $sb = array(':','?','#','!','?','"','(',')','{','}','[',']','-');
            for ($r = 0;$r < count($sb);$r++)
                {
                    $t = troca($t,$sb[$r],' '.$sb[$r].' ');
                }
            /* Elimina caracteres duplos */
            $loop = 0;
            while ((strpos($t,'  ') and ($loop++) < 100))
                {
                    $t = troca($t,'  ',' ');
                }
            $t = troca($t,'.',' .');
            $t = strtolower($t);
            $w = explode(' ',$t);
            return($w);
        }

    function analyse($t)
        {
            $t = $this->limpa_CR($t);
            $this->phrase = $this->separator_words($t);
            $this->busca_01();
            $sx = $this->show_phrase();
            return($sx);
        }

    function show_phrase()
        {
            $fr = $this->phrase;
            $sx = '<table width="100%" align="center">';
            for ($r=0;$r < count($fr);$r++)
                {
                    $sx .= '<tr>';
                    $sx .= '<td style="border-bottom: 1px solid #808080;">'.$fr[$r].'</td>';
                    $sx .= '<td style="border-bottom: 1px solid #808080;">';
                    if (isset($this->phrase_ws[$r]))
                        {
                            $ln = $this->phrase_ws[$r];
                            for ($z=0;$z < count($ln);$z++)
                            {
                                if ($z > 0) { $sx .= '<br>';}
                                $sx .= $ln[$z];
                            }
                        }
                    $sx .= '</td>';
                    $sx .= '</tr>';
                }
            $sx .= '</table>';
            return($sx);
        }

    function web()
    {
        $sx = '';
        $sx .= "<style>
                @import url('https://fonts.googleapis.com/css?family=Orbitron&display=swap');
                @import url('https://fonts.googleapis.com/css?family=Hind&display=swap');
                body, html { background: #e2e9f4; 
                font-family: Hind, Orbitron; }
                h1, h2, h3, h4, h5, h6, h7 { font-family: Orbitron;}
                </style>
                ";
        //$sx .= '<style> div { border: 1px solid #000000; } </style>'.cr();
        $ws = get("ws");
        $rlt = array();
        if (strlen($ws) > 0) {
            $txt = $this->analyse($ws);

            $sx .= '<div class="row">' . cr();
            $sx .= '<div class="' . bscol(12) . '">' . cr();
            $sx .= '<h3>' . $ws . '</h3>' . cr();
            $sx .= '</div>' . cr();

            $sx .= '<div class="' . bscol(12) . '">' . cr();
            $sx .= 'Classe: ';        

            $sx .= $txt;
            $sx .= '</div>' . cr();
            $sx .= '</div>' . cr();
        } else {
            $sx .= '<div class="row">' . cr();
            $sx .= '<div class="' . bscol(12) . '" style="margin-top: 40px; margin-bottom: 100px;">' . cr();
            $sx .= '<h1>Brapci WS - UI</h1>';
            $sx .= '</div>' . cr();

            $sx .= '<div class="' . bscol(1) . ' text-right">' . cr();
            $sx .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#657789" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
            $sx .= '</div>' . cr();
            $sx .= '<div class="' . bscol(11) . '">' . cr();
            $sx .= '<form>';
            $sx .= '<textarea name="ws" rows=5 placeholder="WS Search" style="width: 100%; font-size: 200%;"/>' . cr();
            if (isset($_GET['ws']))
                {
                    $sx .= $_GET['ws'];
                }            
            $sx .= '</textarea>';
            $sx .= '<input type="submit" class="btn btn-primary" value="Analyse >>>">';
            $sx .= '</form>';
            $sx .= '</div>' . cr();
            $sx .= '</div>' . cr(); /* row */
        }
        return ($sx);
    }

    function show_classes()
    {
        $c = get("ws");
        $w = explode(' ', $c);
        $sx = '<table width="100%">';
        for ($r = 0; $r < count($w); $r++) {
            $sx .= '<tr>';
            $sx .= '<td class="text-right" width="10%">';
            $sx .= '<h6>' . $w[$r] . '</h6>';
            $sx .= '</td>';

            $sx .= '<td class="text-left">';
            $sx .= $this->findshow($w[$r]);
            $sx .= '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        return ($sx);
    }
    function show_classes2($c)
    {
        $rlt = array();
        $sx = '';
        for ($r = 0; $r < count($c); $r++) {
            $l = $c[$r];
            $loop = 0;
            while (strpos($l, '/') and ($loop < 100)) {
                $loop++;
                $l = substr($l, strpos($l, '/') + 1, strlen($l));
            }
            $l = troca($l, '.json', '');
            array_push($rlt, $l);
            $sx .= '<a href="#">' . $l . '</a> ';
        }
        return ($sx);
    }
}
