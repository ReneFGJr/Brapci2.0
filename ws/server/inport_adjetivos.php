<?php
/********************************** Ciencia da Informação - thesa */
$class = 'Adjective';
echo '<h1>'.$class.'</h1>'.cr();
$file2 = '../.csv/adjetivos_pt.csv';

$handle = fopen($file2, "r");
$row = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = troca($line,'"','');
        $line = troca($line,';',',');
        $line = utf8_encode($line);
        $fd = splitx(',',$line);

        if ($row == 0)
            {
                $hd = array();
                for ($r=1;$r < count($fd);$r++)
                    {
                        $hd[$r] = $fd[$r];
                    }
            } else {                
                $dt = array();
                $dt['skos:prefLabel'] = trim($fd[0]);
                $dt['Class'] = $class;               
                for ($r=1;$r < count($fd);$r++)
                    {
                        $dt[$hd[$r]] = $fd[$r];
                    }
                echo '<br>=>'.$dt['skos:prefLabel'];
                $ws->save($dt, $dt['skos:prefLabel'],$force);
                
            }
            $row++;
    }
    fclose($handle);
} else {
    echo "Erro ao ler o arquivo ".$file2;
}
echo "Importado ".$row." linhas ";