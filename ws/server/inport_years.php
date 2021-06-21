<?php
/********************************** Ciencia da Informação - thesa */
echo 'YEARS'.cr();

    for ($r = 1900; $r < (date("Y")+1); $r++) {
         $nome = $r;
         $nnome = $nome;

         $dt = array();
         $name = $ws->trata($nome);
         $class = 'DateYear';
         $dt['skos:prefLabel'] = trim($nome);
         $dt['Class'] = $class;
         $ws->save($dt, $name);
    }
