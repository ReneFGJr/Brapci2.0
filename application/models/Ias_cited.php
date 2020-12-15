<?php

class ias_cited extends CI_Model
{
    function neuro_cited($txt,$data)
    {
        //echo '<pre>'.$txt.'</pre>';
        $terms = array(
            'REFERÊNCIAS (ESTILO <SECAOSEMNUM>)',
            'REFERÊNCIAS BIBLIOGRÁFICAS',
            'Referências Bibliográficas',
            'REFERÊNCIAS',
            'Referências',
            'References',
            'Reférences',
            'Referencias',
            'BIBLIOGRAFIA',
        );
        $ref = '';
        $txt = troca($txt,chr(13),chr(10));
        for ($r=0;$r < count($terms);$r++)
        {
            $pos = strpos($txt,$terms[$r].chr(10));
            if ($pos > 0)
            {
                while ($pos > 0)
                {
                    $ref = substr($txt,$pos,strlen($txt));
                    $txt = $ref;
                    $pos = strpos($txt,$terms[$r].chr(10));
                }
            }                    
        }  
        
        /************************************** Ve se termina */
        for ($r=0;$r < count($data['title']);$r++)
        {
            $tit = $data['title'][$r];
            $tite = $this->ias->split_word($tit,' ',7);
            $pos = strpos($ref,$tite);
            if ($pos > 0)
            {
                $ref = substr($ref,0,$pos);
            }
        }
        /************************************** Ve se termina com o título em inglês */
        for ($r=0;$r < count($data['title']);$r++)
        {
            $tit = $data['title'][$r];
            $tite = $this->ias->split_word($tit,' ',7);
            $pos = strpos($ref,$tite);
            if ($pos > 0)
            {
                $ref = substr($ref,0,$pos);
            }
        }                           
        /************************************** Ve se termina com abstract */
        if (
            ($pos = strpos($ref,'Abstract:')) 
            or ($pos = strpos($ref,'Abstract'.chr(10))) 
            or ($pos = strpos($ref,'Resumo:')))
            {
                $ref = substr($ref,0,$pos);
                while (substr($ref,strlen($ref)-1,1) != '.')
                {
                    $ref = substr($ref,0,strlen($ref)-1);
                }
            }                          
            /********************************* Remove so numero */
            $ref = $this->ias->to_line($ref);
            $txt = '';
            for ($rr=1;$rr < count($ref);$rr++)
            {
                $ln = $ref[$rr];
                if ($this->ias->sonumero($ln, false) == 1)
                {
                    /* Nada */
                } else {
                    $txt .= $ref[$rr].chr(10).chr(13);
                }
            }
            $ref = $this->process($txt,$data);
            return($ref);
        }
        
        function process($txt,$data)
        {         
            $sx = '<ol>';
            $ln = $this->ias->to_line($txt);
            $tb = '';
            $tn = '';
            $rsp = '';
            $err = 0;
            $nl = 0; /* Nova linha anterior */
            $cx = 0; /* Terminoi em caixa alta a linha anterior */
            $last_collon = 0; /* Termina em dois pontos*/
            for ($r = 0; $r < count($ln); $r++) {
                $t = $ln[$r];
                //$tb .= $t.';';
                $n = array();
                /* Tem ano no texto */
                $n[0] = $this->ias->tem_ano($t);
                
                /* Tudo em caixa alta */
                $n[1] = $this->ias->caixa_alta($t);
                
                /* Primeira Palavra em caixa alta */
                $n[2] = $this->ias->caixa_alta_palavra($t);
                
                /* So numero */
                $n[3] = $this->ias->sonumero($t);
                
                /* Tamanho mínimo */
                $n[4] = $this->ias->linesize($t, 25);
                
                /* registro anterior termina com ano */
                $n[6] = $this->ias->termina_com_ano($tn);
                $tn = $t;
                
                /* termina com caixa alta */
                $n[7] = $this->ias->termina_com_caixa_alta($tn);
                $tn = $t;    

                /* termina com caixa alta */
                $n[10] = $last_collon;
                $n[11] = $this->ias->termina_com_caracter($tn,':');
                $last_collon = $n[11];
                $tn = $t;                          
                
                /* registro anterior termina com caixa alta */
                $n[8] = $cx;
                $cx = $n[7];
                
                /* registro com titulação do autor */
                $n[9] = $this->ias->tem_titulacao($tn);
                if ($n[9] == 1)
                    {
                        $r = count($ln)+1;
                        $t = '';
                    }
                        
                
                /* Nova linha no registro anterior */
                $n[5] = $nl;
                $nl = $this->rede_neuro($n);   
                
                if ($nl == 1)
                {
                    $rsp .= cr();
                }         
                $t = troca($t,'.,',';');
                $rsp .= troca($t,'.,',';').' ';            
                
                $sx .= '<li>';
                $sx .= '<tt>'.$t.'</tt>';
                $sx .= '<br/>';                           
                $nrn = $this->rede_neuro($n);
                $tb .=  $nrn. ';';
                
                $sx .= '['.$nrn.'] '.$this->ias->show_sensores($n);
                $tb .= $this->ias->show_sensores($n, 'T') . cr();
                $sx .= '</li>';
            }
            $sx .= '</ol>';
            
            /************************************************** Busca por ____ */
            if (strpos($rsp,'___') > 0)
            {
                $ln = $this->ias->to_line($rsp);   
                $last = '';
                $rsp = '';
                for ($r=0;$r < count($ln);$r++)
                {                    
                    $l = trim($ln[$r]);
                    if (substr($l,0,2) == '__')
                    {
                        $ln[$r] = $this->author_citado($ln[$r],$last);
                    }
                    $last = $ln[$r];
                    $rsp .= $ln[$r].cr();
                }
                
            }
            $sx = '<hr><b>Referências</b>
            <form action="'.base_url(PATH.'ia/nlp/save/cited/'.$data['id']).'" method="post">
            <textarea name="dd1" class="form-control" rows=15>' . $rsp . '</textarea>
            <input type="submit" value="salvar referências" name="action">
            </form>
            <textarea class="form-control" rows=5>' . $tb . '</textarea>
            '. $sx;
            return ($sx);
        }
        
        function author_citado($n,$l)
        {
            $nnl = substr($l,0,strpos($l,'.'));
            
            $n = troca($n,'_ ','_.');
            $n = troca($n,'_;','_.');
            $n = troca($n,'_,','_.');
            
            $n = troca($n,'__.',$nnl.'.');
            $n = troca($n,'_','');
            return($n);
        }
        function rede_neuro($n)
        {
            $rs = 0;
            /* Primeira maiusculoa */
            if (($n[2] == 1) and ($n[10] == 0)) {
                /* so numero */
                if ($n[3] == 0) {
                    /* anterior novo linha */
                    if ((($n[5] == 0) or ($n[6] == 1)) and ($n[8] == 0) )
                    {
                        $rs = 1;
                    }
                }
            }
            return ($rs);
        }
    }
