<?php
/********************************** Ciencia da Informação - thesa */
$class = 'Month';
echo '<h1>'.$class.'</h1>'.cr();
$s = array(
    'Janeiro',
    'Fevereiro',
    'Março',
    'Abril',
    'Maio',
    'Junho',
    'Julho',
    'Agosto',
    'Setembro',
    'Outubro',
    'Novembro',
    'Dezembro'
     );
$sn = array('jan','fev','mar','abr','maio','jun','jul','ago','set','out','nov','dez');
    for ($r = 0; $r < count($s); $r++) {
         $nome = $s[$r];
         $nnome = $nome;

         $dt = array();
         $name = $ws->trata($nome);
         $dt['skos:prefLabel'] = trim($nome);
         $dt['hasAchronic'] = $sn[$r];
         $dt['hasLanguage'] = 'pt';
         $dt['Class'] = $class;
         $ws->save($dt, $name);
    }

    for ($r = 0; $r < count($s); $r++) {
         $nome = $s[$r];
         $nnome = $nome;

         $dt = array();
         $name = $ws->trata($nome);
         $sname = $ws->trata($sn[$r]);
         $dt['skos:prefLabel'] = trim($nome);
         $dt['use'] = trim($nome);
         $dt['hasAchronic'] = $sn[$r];
         $dt['hasLanguage'] = 'pt';
         $dt['Class'] = $class;
         $ws->save($dt, $sname);
    }    
