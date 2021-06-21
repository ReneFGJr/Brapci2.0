<?php
/********************************** Ciencia da Informação - thesa */
echo 'GENERE '.$th.cr();
$file2 = '../.csv/genre.csv';

$handle = fopen($file2, "r");
$row = 0;
$force = TRUE; /* Força gravação, se já existe dados */
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = troca($line,'"','');
        $fd = splitx(',',$line);

        if ($row == 0)
            {
                $hd = array();
                for ($r=1;$r < count($fd);$r++)
                    {
                        $hd[$r] = $fd[$r];
                    }
            } else {
                $class = 'PersonName';
                $dt = array();
                $dt['skos:prefLabel'] = trim($fd[1]);
                $dt['Class'] = $class;               
                for ($r=1;$r < count($fd);$r++)
                    {
                        $dt[$hd[$r]] = $fd[$r];
                    }
                $ws->save($dt, $dt['skos:prefLabel'],$force);
            }
            $row++;
    }
    fclose($handle);
} else {
    echo "Erro ao ler o arquivo ".$file2;
}
echo "Importado ".$row." linhas ";