<?php
class wsc //extends CI_Model
{
    var $dir = 'source/';
    /********************************************************* IA */
    function genere($name)
        {
            $nf = $name;
            $n = explode(' ',$nf);
            $rst = 'X';
            for ($r=0;$r < count($n);$r++)
            {
                $rs  = $this->find($n[$r]);                
                for ($nr=0;$nr < count($rs);$nr++)
                    {
                        $fn = $rs[$nr];
                        if (strpos($fn,'PersonName') > 0)
                            {
                                $rlt = $this->read($fn);
                                $male = $rlt['hasFrequencyMale'];
                                $female = $rlt['hasFrequencyFemale'];
                                if ($female > 0)
                                    {
                                        $prob = round(10000 * ($male / ($male + $female)))/100;
                                    } else {
                                        $prob = 100;
                                    }
                                if ($prob > 90) { $rst = 'Male'; }
                                if ($prob < 10) { $rst = 'Female'; }
                                //echo '<br>'.$prob.' => '.$n[$r]. '==>'.$rst;
                            }                       
                        if ($rst != 'X') { break; }
                    }                    
                if ($rst != 'X') { break; }
            }
            $rs = array();
            $rs['genere'] = $rst;    
            $rs['name'] = $nf;
            return($rs);
        }

/************************************************** I O Interface */
    function read($f)
        {
            if (file_exists($f))
                {
                    $txt = file_get_contents($f);
                    $txt = (array)json_decode($txt);
                } else {
                    $txt = array();
                }
            return($txt);
        }
    function find($n)
        {
            $rst = array();
            $f = $this->dir_t($n);
            if (is_dir($f))
                {
                    $fd = dir($f);
                    while($file = $fd -> read()){
                        if (strpos($file,'.json'))
                            {
                                array_push($rst,$f.'/'.$file);
                            }
                    }
                    $fd -> close();                    
                }
            return($rst);
        }

    function save($dt,$name,$class='')
        {
            $class = $dt['Class'];
            $dir = $this->dir_t($name);
            $file = $dir.'/'.$class.'.json';
            if (!file_exists($file))
            {
                $dt['created'] = date("Y-m-d").'T'.date("H:i:s");
                $json = json_encode($dt);
                file_put_contents($file,$json);
            }
            return(TRUE);
        }

    function xml($dt)
    {
        $sx = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . cr();
        $sx .= '<rdf:RDF xmlns:cc="http://creativecommons.org/ns#" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:gn="http://www.geonames.org/ontology#" xmlns:owl="http://www.w3.org/2002/07/owl#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">';
        return($sx);
    }

    function dir_t($t)
    {
        $t = ascii($t);
        $t = strtolower($t);
        $d1 = substr($t, 0, 1);
        $d2 = $t;
        $dir = $this->dir;
        dircheck($dir);
        $dir .= $d1;
        dircheck($dir);
        $dir .= '/' . $d2;
        dircheck($dir);
        return ($dir);
    }

    function terms($t)
    {
        echo '==>' . $t . cr();
        $t = ascii($t);
        echo '==>' . $t . cr();
        $t = strtolower($t);
        echo '==>' . $t . cr();
        $t = explode(' ', $t);
        echo '--->' . $this->dir_t($t[0]);
        print_r($t);
    }
}
