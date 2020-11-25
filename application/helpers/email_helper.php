<?php
/**
* CodeIgniter Form Helpers
*
* @package     CodeIgniter
* @subpackage  EMAIL
* @category    Helpers
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.sisdoc.com.br/CodIgniter
* @version     v0.20.05.12
*/

/* GMAIL
https://myaccount.google.com/u/1/security?hl=pt-BR
Acesso APP menos seguro
*/

function email($para,$assunto,$texto,$de=1)
{
    /* de */
    $sql = "select * from mensagem_own where id_m = " . round($de);
    $CI = &get_instance();
    $rlt = $CI -> db -> query($sql);
    $rlt = $rlt -> result_array();
    $line = $rlt[0];

    echo '<h1>Credenciais</h1>';
    echo '<pre>';
    print_r($line);
    echo '</pre>';

    $CI->load->library('email');
    $config = array();
    $config['protocol'] = 'smtp';
    $config['smtp_host'] = $line['smtp_host'];
    $config['smtp_user'] = $line['smtp_user'];
    $config['smtp_pass'] = $line['smtp_pass'];
    $config['smtp_port'] = $line['smtp_port'];
    $config['validate']  = TRUE;
    $config['mailtype']  = 'html';
    $config['charset']   = 'utf-8';
    $config['newline']   = "\r\n";   
         
    $CI->email->initialize($config);
    $CI->email->set_newline("\r\n");

    $CI->email->from($line['m_email'], $line['m_descricao']);
    $CI->email->subject($assunto);
    $CI->email->reply_to($line['m_email']);
    $CI->email->to($para); 
    //$this->email->cc('email_copia@dominio.com');
    //$this->email->bcc('email_copia_oculta@dominio.com');
    $CI->email->message($texto);
    $CI->email->send();

    echo '<h1>Dados do retorno</h1>';
    echo '<pre>';
    print_r($CI->email);
    echo '</pre>';
}

function enviaremail($para, $assunto, $texto, $de=1, $anexos = array()) {
    global $sem_copia;

    if (!isset($sem_copia)) { $sem_copia = 0;
    }
    if (!is_array($para)) {
        $para = array($para);
    }
    $CI = &get_instance();
    
    /* de */
    $sql = "select * from mensagem_own where id_m = " . round($de);
    $rlt = $CI -> db -> query($sql);
    $rlt = $rlt -> result_array();
    $line = $rlt[0];    

    $config = Array('protocol' => $line['smtp_protocol'], 'smtp_host' => $line['smtp_host'], 'smtp_port' => $line['smtp_port'], 'smtp_user' => $line['smtp_user'], 'smtp_pass' => $line['smtp_pass'], 'mailtype' => 'html', 'charset' => 'iso-8859-1', 'wordwrap' => TRUE);

    $CI -> load -> library('email', $config);
    $CI -> email -> subject($assunto);
    $CI -> email -> message($texto);

    for ($r = 0; $r < count($anexos); $r++) {
        $CI -> email -> attach($anexos[$r]);
    }

    /* Header & footer */
    $email_header = '';
    $email_footer = '';

    /***************************************************/
    if (count($rlt) == 1) {
        $line = $rlt[0];
        $e_mail = trim($line['m_email']);
        $e_nome = trim($line['m_descricao']);

        /***************** HEADER AND FOOTER */
        $email_header = $line['m_header'];
        $email_footer = $line['m_foot'];

        if (strlen($email_header) > 0) {
            $email_header = '<table width="550"><tr><td><img src="' . base_url($email_header) . '"></td><tr><tr><td><br><br>';
        }
        if (strlen($email_footer) > 0) {
            $email_footer = '</td></tr><tr><td><img src="' . base_url($email_footer) . '"></td></tr></table>';
        }

        $CI -> email -> from($e_mail, $e_nome);
        $CI -> email -> to($para[0]);
        $CI -> email -> subject($assunto);
        $CI -> email -> message($email_header . $texto . $email_footer);
        $CI -> email -> mailtype = 'html';
        if ($sem_copia != 1) {
            array_push($para, trim($line['m_email']));
            //array_push($para, 'renefgj@gmail.com');
        }

        /* e-mail com copias */
        $bcc = array();
        for ($r = 1; $r < count($para); $r++) {
            array_push($bcc, $para[$r]);
        }

        if (count($bcc) > 0) {
            $CI -> email -> bcc($bcc);
        }

        $sx = '<div id="email_enviado">';
        $sx .= '<h3>' . msg('email_enviado') . '</h3>';
        for ($r = 0; $r < count($para); $r++) {
            $sx .= $para[$r];
            $sx .= '<br>';
        }
        $sx .= '<br>';
        $sx .= '</div>';
        $sx .= '<script>
        setTimeout(function() { $(\'#email_enviado\').fadeOut(\'fast\');}, 3000);
        </script>
        ';
        $proto = $CI->email->protocol;
        switch($proto)
        {
            case 't':
            $to      = 'renefgj@gmail.com';
            $subject = 'Assunto sem caracteres especiais';
            $message = 'conteudo do email. 
            Atencao para codificacao do texto, clientes de email podem interpretar errado';

            $headers = 'From: Brapci.inf.br <brapcici@gmail.com> ' . "\r\n" .
            'Reply-To: brapcici@gmail.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

            $real_sender = '-f brapcici@gmail.com';

            mail($to, $subject, $message, $headers, $real_sender);
            break;
            
            case 'm':
                $message = $email_header . $texto . $email_footer;
    
                $headers = 'From: ppgcin@ufrgs.br <cedap@ufrgs.br> ' . "\r\n" .
                'Reply-To: cedap@ufrgs.br' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
                
                $e_mail = utf8_decode($e_mail);
                $subject = "Content-Type:text/html; charset=UTF-8\n";
    
                mail($e_mail, $assunto, $message, $headers);
                break;             

            default:
            return($CI -> email -> send());
        }
        return (1);
    } else {
        echo('<font color="red">Proprietário do e-mail (' . $de . ') não configurado (veja mensagem_own)</font>');
        exit ;
    }
}

?>