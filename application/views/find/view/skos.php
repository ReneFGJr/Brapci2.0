<?php
for ($r=0;$r < count($skos);$r++)
    {
        $line = $skos[$r];
        echo msg($line['c_class']).': ';
        echo $line['n_name'];
        echo '<br>';
    }
?>
