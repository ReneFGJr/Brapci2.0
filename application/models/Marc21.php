<?php
class Marc21 extends CI_model
    {
    function marc21help($t='')
        {
            $tp = '';
            if (strlen($t) == 1)
                {
                    $tp = 'b';
                    break;
                }            
            if (strlen($t) == 3)
                {
                    $tp = 'f';
                    break;
                }            

            switch($tp)
                {
                case 'b':
                    $url = 'https://www.loc.gov/marc/bibliographic/bd'.$b.'xx.html';
                    break;
                case 'f':
                    $url = 'https://www.loc.gov/marc/bibliographic/bd'.$t.'.html';
                    break;
                default:
                    $url = 'https://www.loc.gov/marc/bibliographic/';
                    break;
                }
            $sx = '<a href="'.$url.'">'.$t.'</a>';
            return($sx);   
        }      
    }
?>
