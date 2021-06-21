<?php
/********************************** Ciencia da Informação - thesa */
$class = 'Simbols';
echo '<h1>'.$class.'</h1>'.cr();
$s = array('Ponto de Interrogação','Ponto de exclamação',
    'Hashtag','Porcentagem','Asterisco',
    'Parênteses','Abre parênteses','Fecha parênteses',
    'Colchetes','Abre colchetes','Fecha colchetes',
    'Chaves','Abre chaves','Fecha chaves',
    'Vigula',
    'Ponto',
    'Ponto final',
    'Dois pontos',
    'Reticencias',
    'Ponto e virgula'
     );
$sn = array('?','!',
        '#','%','*',
        '()','(',')',
        '[]','[',']',
        '{}','{','}',
        ',',
        '.',
        '.',
        ':',
        '...',
        ';'
        );
    for ($r = 0; $r < count($s); $r++) {
         $nome = $s[$r];
         $nnome = $nome;

         $dt = array();
         $name = $ws->trata($nome);
         $dt['skos:prefLabel'] = trim($nome);
         $dt['hasAchronic'] = $sn[$r];
         $dt['Class'] = $class;
         $ws->save($dt, $name);
    }
