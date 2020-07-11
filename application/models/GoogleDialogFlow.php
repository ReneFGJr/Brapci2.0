<?php
class GoogleDialogFlow extends CI_model
{
    var $agent_id = '255556ef-ead7-4579-b726-2eac5feaab48';
    var $table = 'brapci_altmetrics.dialogflow';
    function run($token='')
    {
        global $rso;
        $json = file_get_contents('php://input');
        //$json = $this->save_request();
        $rso = $this->proccess($json);
        //$rsp = 'Resposta OK';
        
        
        $sx = '{ "fulfillmentMessages": [ '.$rso.' ] } ';       
        
        //header('Content-Type: application/json');
        echo $sx;   
        exit;                
    }
    
    /********************************* INTENT - AJUDA */
    function intent($in,$ar)
    {         
        $intent = $in[0];
        $sx = '';
        switch($intent)
        {
            case 'brapci_autores':
                $sx = $this->Intent_brapci_autores($in,$ar);
            break;
            /************************************* Default */
            default:            
            $intent = 'Buscando Intent '.$in[0];
            for ($r=1;$r < count($in);$r++)
            {
                $intent .= cr().'===>'.$in[$r];
            }
            $parameters = '';
            if (is_array($ar) > 0)
            {
                $parameters .= cr().'== parametros ===============';
                foreach($ar as $key=>$value)
                {
                    $parameters .= cr().$key.'==>'.$value;
                }
            } else {
                $parameters .= cr().'Sem parametros informados';
            }
            $sx = '';
            $sx .= $this->response_text('Intente '.$intent);
            $sx .= $this->response_text('Parameters '.$parameters);
            $sx .= $this->response_card("Brapci");            
        }
        return($sx);
    }   
    
    function Intent_brapci_autores($in,$ar)
    {
        global $rso;
        $rso = '';
        if (isset($ar['entity_author']))
        {
            $rso .= $this->response_card(nbr_autor($ar['entity_author'],7),'autor');
            $rso .= $this->response_text("Mostrando informações sobre ".$ar['entity_author']);
            $rso .= $this->response_text("Total de trabalhos ".date("s"));
        } else {
            $rso .= $this->response_text('Qual o nome do autor que está procurando?');
        }
        
        return($rso);
        
    } 
    
    function proccess($t)
    {   
        $test = 0;
        if ($test==1)
        {
            $t = file_get_contents('_ia/_temp.json');
        } else {
            file_put_contents('_ia/_temp.json',date("Y-m-d H:i:s").cr().'========'.cr().$t);
        }      
        $tp = (array)json_decode($t);
        if (isset($tp['queryResult']))
        {
            $tpr = (array)$tp['queryResult'];
        } else {
            $tpr = array();
        }
        
        $sx = 'Ops, não localizei nada sobre o assunto.';
        
        if (isset($tp['queryResult']))
        {
            $qr = (array)$tp['queryResult'];
            
            $args = $this->parameters($qr);
            $intent = $this->intents($qr);               
            
            $sx = $this->intent($intent,$args);
            
        } else {
            $sx = 'Qual é sua dúvida?';
        }
        return($sx);
    }
    
    /******************************************** RESPONSES ***************/
    function comma()
    {
        global $rso;
        $sx = '';
        if (strlen($rso) > 0)
        {
            $sx .= cr().','.cr();
        }
        return($sx);
    }
    function response_text($t='')            
    {
        global $rso;
        $sx = $this->comma().'{ "text": { "text": ["'.$t.'"] } }';
        return($sx);
    }
    
    function response_card($title="BrapciBot",$sub="Base de Dados em Ciência da Informação",$img='https://brapci.inf.br/img/logo/logo-brapci.png',$btns=array())
        {
            global $rso;
            $sx = $this->comma().'{"card": {
                "title": "'.$title.'",
                "subtitle": "'.$sub.'",
                "imageUri": "'.$img.'"
                ';  
                if (count($btns) > 0)
                {
                    $sx .= ', '.cr();
                    $sx .= '"buttons": [ ';
                    $in = 0;
                    foreach($btns as $cap=>$link)
                    {
                        if ($in > 0) { $sx .= ', ';}
                        $sx .= cr(). '{ "text": "'.$cap.'", "postback": "'.$link.'" }';
                        $in++;
                    }
                    $sx .= ']';
                }          
                
                $sx .= '
            } }';
            return($sx);
        }
        
        
        
        
        function intents($qr)
        {
            if (isset($qr['intent']))
            {
                $qr = (array)$qr['intent'];
                if (isset($qr['displayName']))
                {
                    $p = $qr['displayName'];
                    $p = explode('-',$p);
                } else { 
                    $p = array('no_intent'); 
                }
            } else { 
                $p = array('no_intent'); 
            }
            return($p);
        }
        
        function parameters($qr)
        {
            $p = (array)$qr['parameters'];
            return($p);
        }
        
        function show_array($input)
        {
            $json = '';
            foreach($input as $key=>$value)
            {
                if (is_array($value))
                {
                    $json .= $key .' ==> '.cr();
                    $json .= $this->show_array($value);
                } else {
                    $json .= $key .' => '.$value.cr();
                }            
            }
            return($json);
        }
        function save_request()
        {
            $json = '';
            //$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            //$uri = explode( '/', $uri );
            //$json .= $uri.cr();
            //$json .= 'Request:'.$_SERVER["REQUEST_METHOD"].cr();        
            $json = file_get_contents('php://input');
            //$json .= $this->show_array($input);
            $sql = "insert into ".$this->table."
            (df_request)
            values
            ('$json')";
            $this->db->query($sql);
            return($json);
        }
        function bot()
        {
            $sx = '
            <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
            <df-messenger
            intent="WELCOME"
            chat-title="Brapci_Ajuda"
            agent-id="31e54c28-4130-4c99-9712-d3b330327b0a"
            language-code="pt-br"
            ></df-messenger>';
            return($sx);
        }
    }