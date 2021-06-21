<?php
/********************************** Ciencia da Informação - thesa */
echo 'THESA '.$th.cr();
    $file2 = 'thesa_'.$th.'.xml';
    
    if (!file_exists($file2)) {
        $url = 'https://www.ufrgs.br/tesauros/index.php/thesa/terms_from_to/'.$th.'/skos';
        $txt = file_get_contents($url);
        $txt = troca($txt, 'xml:', '');
        $txt = troca($txt, 'rdf:', '');
        
        file_put_contents($file2, $txt);
    } else {
        $txt = file_get_contents($file2);
    }
    $xml = simplexml_load_string($txt);
    $collection = $ws->trata((string)$xml->Collection->name);
    $collection_id = $ws->trata((string)$xml->Collection->idth);
    $xml = $xml->Concept;

    for ($r = 0; $r < count($xml); $r++) {
        $el = $xml[$r];
        if (strlen(trim($el->prefLabel)) > 0)
            {
                $idth = (string)$el->attributes()->about;
                $nome = (string)$el->prefLabel;
                $nnome = $nome;
                $lang = (string)$el->prefLabel->attributes()->lang;

                $dt = array();
                $name = $ws->trata($nome);
                $class = 'ConceptThesa'.$th;
                $dt['skos:prefLabel'] = trim($nome).'@'.trim($lang);
                $dt['Class'] = $class;
                $dt['DomainName'] = $collection;
                $dt['hasSource'] = 'thesa:'.sonumero($idth).'#'.$collection_id;
                $dt['hasUriThesa'] = 'https://www.ufrgs.br/tesauros/index.php/thesa/c/'.sonumero($idth).'/'.sonumero($collection_id);                

                for ($z=0;$z < count($el->hiddenLabel);$z++)
                    {
                        $langt = (string)$el->hiddenLabel[$z]->attributes()->lang;
                        $dt['skos:hiddenLabel'][$z] = trim((string)$el->hiddenLabel[$z]).'@'.trim($langt);
                    }
                
                for ($z=0;$z < count($el->altLabel);$z++)
                    {
                        $langt = (string)$el->altLabel[$z]->attributes()->lang;
                        $dt['skos:altLabel'][$z] = trim((string)$el->altLabel[$z]).'@'.trim($langt);
                    }
                $ws->save($dt, $name);

                /************************* AltLabel */
                for ($z=0;$z < count($el->altLabel);$z++)
                    {
                        $dt = array();
                        $class = 'UseThesa'.$th;
                        $nome = (string)$el->altLabel[$z];
                        $lang = (string)$el->altLabel[$z]->attributes()->lang;
                        $dt['Skos:altLabel'] = trim($nome).'@'.trim($lang);
                        $dt['hasSource'] = 'thesa:'.sonumero($idth).'#'.$collection_id;
                        $dt['Redirect'] = $name;
                        $dt['Class']=$class;
                        $dt['skos:prefLabel'] = $nnome;
                        $dt['hasUriThesa'] = 'https://www.ufrgs.br/tesauros/index.php/thesa/c/'.sonumero($idth).'/'.sonumero($collection_id);                    
                        $ws->save($dt, $ws->trata($nome));
                    }

                /************************* AltLabel */
                for ($z=0;$z < count($el->hiddenLabel);$z++)
                    {
                        $dt = array();
                        $class = 'UseThesa'.$th;
                        $nome = (string)$el->hiddenLabel[$z];
                        $lang = (string)$el->hiddenLabel[$z]->attributes()->lang;
                        $dt['Skos:hiddenLabel'] = trim($nome).'@'.trim($lang);
                        $dt['hasSource'] = 'thesa:'.sonumero($idth).'#'.$collection_id;
                        $dt['Redirect'] = $name;
                        $dt['Class']=$class;
                        $dt['skos:prefLabel'] = $nnome;
                        $dt['hasUriThesa'] = 'https://www.ufrgs.br/tesauros/index.php/thesa/c/'.sonumero($idth).'/'.sonumero($collection_id);                    
                        $ws->save($dt, $ws->trata($nome));
                    }                    
            }
    }
