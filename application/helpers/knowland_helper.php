<?php
class knowland
    {
        var $directory = '_kl/';
        function dialog($args)
            {
                $CI = &get_instance();
                $CI->load->helper('ai');
                $CI->load->helper('nlp');
                $txt = $args['dd1'];

                $sx = 'You say <b>'.$txt.'</b>';
                $sx .= $this->knowledge($txt);
                return($sx);
            }

        function knowledge($t)
            {
                $nlp = new nlp;
                $ai = new ai;

                $t = $this->trata($t);                
                $tn = explode(' ',$t);
                $to = explode(' ',$t);
                $bs = array();
                for($r=0;$r < count($tn);$r++)
                    {
                        $term = $tn[$r];         
                        $term = ascii($term);               
                        $term = $ai->nlp_inflector($term,'S');
                        $term = troca($term,' ','');                        
                        $term = $nlp->stem($term);
                        $dt = $this->json($term,$to[$r]);
                        $bs[$term] = $dt;
                    }
                echo '<pre>';
                print_r($bs);
                echo '</pre>';

            }

        function trata($t)
            {
                $sa = array(':',',','.','!','?','#','$','%','"');
                $t = str_replace($sa,' ',$t);
                return($t);
            }

        function json_upper($term)
            {

            }

        function dbpead($term)
            {
                $url = 'https://dbpedia.org/page/Homem';
                $url = 'https://dbpedia.org/data/Curitiba.json';
                $url = 'http://pt.dbpedia.org/resource/Catolicismo';

            }

        function json($term,$term_ori)
            {
                $term = lowercasesql($term);
                $dir = $this->directory;
                check_dir($dir);
                $dir_term = $dir.$term;
                check_dir($dir_term);
                $file = $dir_term.'/concept.json';

                if (!file_exists($file))
                    {
                        $dt = array();
                        $dt['conecpt'] = $term;
                        $dt['class'] = 'brapci:undefined';
                        $dt['prefLabel'] = $term;
                        $dt['altLabel'][$term_ori] = 1;
                        $dt['created'] = date("Y-m-d H:i:s");
                        $dt['update'] = date("Y-m-d H:i:s");
                        file_put_contents($file,json_encode($dt));
                    } else {
                        $dt = (array)json_decode(file_get_contents($file));
                        $dtl = (array)$dt['altLabel'];
                        if (!isset($dtl[$term_ori]))
                            {
                                $dtl[$term_ori] = 1;
                            }
                        file_put_contents($file,(array)json_encode($dt));
                    }
                
                return($dt);
            }        
    }