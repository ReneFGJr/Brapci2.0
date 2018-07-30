<?php
class nets extends CI_model
    {
    function twitter($url) 
        {
            $link = 'http://twitter.com/home?status='.urlencode($url);
            $sx = '
            <a href="'.$link.'">Compartilhar em Twitter</a>';
            return($sx);
        }      
    }
?>
