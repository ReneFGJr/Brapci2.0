<?php
class NLP
    {
        function stem($t,$lg="pt")
            {
                $sx = '';
                $CI = &get_instance();

                $CI->load->helper('utf8');
                $CI->load->helper('stemmer'); 
                $manager = new StemmerManager();
                $sx = $manager->stem($t, 'pt');
                

                //require("stemmer/Portuguese.php");
                //$stemmer = new Portuguese();
                //$sx = $stemmer->stem('anticonstitutionnellement');

                return($sx);
            }
    }


