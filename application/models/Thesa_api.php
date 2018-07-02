<?php
class thesa_api extends CI_model {
    function ajax($id = '') {
        $name = trim(get("dd1"));
        $id = trim(get("id"));
        
        echo msg('find') . ' <b>' . $name . '</b>';

        $data = array("term" => $name, );

        $curl = curl_init();
        curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => 'http://[::1]/projeto/Thesa/index.php/thesa/api/64/'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);

        $xml = simplexml_load_string($result);
        $erro = (string)$xml -> error;

        if (strlen($erro) == 0) {
            $pref = (string)$xml->prefLabel->term;
            $pref_lang = $this->frbr_core->language((string)$xml->prefLabel->lang);
            echo '<hr>'.$pref.' <sup>('.$pref_lang.')</sup>';       
            
            $idt = $this->frbr_core->frbr_name($pref,$pref_lang);
            $rsp = $this->frbr_core->prefTerm_chage($id,$idt);
            if ($rsp == 1)
                {
                    echo " - Atualizado (prefTerm)";
                } else {
                    echo " - Não atualizado (prefTerm)";
                }
            /******** Remissivas *******/
            $hide = $xml->hiddenLabel;
            $hide_lang = (string)$xml->prefLabel->lang;
            //print_r($hide);
            for ($r=0;$r < count($xml->hiddenLabel);$r++)
                {
                    $term = (string)$hide[$r]->term;
                    $lang = $this->frbr_core->language((string)$hide[$r]->lang);
                    echo '==>'.$term.'--'.$lang.'<br>'; 
                    $lit = $this->frbr_core->frbr_name($term,$lang); 
                    $prop = 'altLabel';
                    $this->frbr_core->set_propriety($id, $prop, 0, $lit);      
                }
            
                        
                echo '<h2>'.$idt.'-'.$id.'</h2>';
                echo '<pre>';
                print_r($xml);
                echo '</pre>';
            
            

        } else {
            Echo "ERRO: " . $erro;
        }

    }

    function update_thesa($d) {
        $term = $this -> frbr_core -> prefTerm($d);
        $id = $d[0]['d_r1'];
        $sx = '';
        $sx .= '<div class="col-md-12">';
        $sx .= '<input type="button" class="btn btn-scondary" id="thesa"';
        $sx .= ' value="' . msg('check Thesa') . '">';
        $sx .= '<hr>';
        $sx .= '<div style="width: 400px; height; 100px; display: none;" id="thesa_query">';
        $sx .= 'Consultando...';
        $sx .= '</div>';
        $sx .= '</div>';

        $sx .= '<script>' . cr();
        $sx .= ' $("#thesa").click(function() {' . cr();
        $sx .= '   $("#thesa_query").show(1000);' . cr();
        $sx .= '   $term = "' . $term . '";
                    $id = "'.$id.'";
                   $.ajax({
                      url: "' . base_url(PATH . 'ajax/thesa/') . '",
                      data: { "id": $id, "dd1": $term },
                      context: document.body
                    }).done(function(html) {
                      $("#thesa_query").html(html);
                    });';
        $sx .= ' }); ' . cr();
        $sx .= '</script>' . cr();

        return ($sx);
    }

}
