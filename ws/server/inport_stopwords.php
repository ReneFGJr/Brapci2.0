<?php
/********************************** stopwords - thesa */
echo 'STOPWORD'.cr();

    $url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/69/skos';
    $txt = file_get_contents($url);
    $txt = troca($txt, 'xml:lang', 'lang');
    $xml = simplexml_load_string($txt);
    $xml = $xml->Concept->hiddenLabel;
    for ($r = 0; $r < count($xml); $r++) {
        $el = $xml[$r];
        foreach ($el->attributes() as $a => $b) {
            $dt = array();
            $name = $ws->trata(((string)$el));
            $class = 'Stopword';
            $dt['prefLabel'] = ((string)$el);
            $dt['haslanguage'] = 'brapci:' . $ws->trata($b);
            $dt['Class'] = $class;
            $ws->save($dt, $name);
        }
    }
