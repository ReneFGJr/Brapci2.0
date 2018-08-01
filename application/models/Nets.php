<?php
class nets extends CI_model
    {
    function twitter($url) 
        {
            $link = 'http://twitter.com/home?status='.urlencode($url);
			$link = 'https://twitter.com/intent/tweet?text=A%20altmetria%20na%20pr%C3%A1tica%20e%20o%20papel%20dos%20bibliotec%C3%A1rios%20no%20seu%20uso%20e%20aplica%C3%A7%C3%A3o&url=http%3A%2F%2Fseer.ufrgs.br%2Findex.php%2FEmQuestao%2Farticle%2Fview%2F72790%23.W2CauhKdAoQ.twitter&related=';
            $sx = '
            <a href="'.$link.'">Compartilhar em Twitter</a>';
            return($sx);
        }    
		
		/*
		 * https://twitter.com/intent/tweet?text=A%20altmetria%20na%20pr%C3%A1tica%20e%20o%20papel%20dos%20bibliotec%C3%A1rios%20no%20seu%20uso%20e%20aplica%C3%A7%C3%A3o&url=http%3A%2F%2Fseer.ufrgs.br%2Findex.php%2FEmQuestao%2Farticle%2Fview%2F72790%23.W2CauhKdAoQ.twitter&related=
		 */  
    }
?>
