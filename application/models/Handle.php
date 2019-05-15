<?php 
class handle extends CI_model
    {
  
    function create_handle($i,$f=0,$pre='20.500.12287')
        {
            $c = '';
            $es = 100;
            for ($r=$i;$r <= $f;$r++)
                {
                    $es--;
                    if ($es == 0) { $r = $f; }
                    $c .= 'CREATE '.$pre.'/'.$r.cr();
                    $c .= '100 HS_ADMIN 86400 1110 ADMIN 200:111111111111:0.NA/'.$pre.cr();
                    $c .= '3 URL 86400 1110 UTF8 http://dspace.emater.tche.br/xmlui/handle/'.$pre.'/'.$r.cr();
                    $c .= cr();        
                }
            return($c);            
        }
        
    }
?>    
