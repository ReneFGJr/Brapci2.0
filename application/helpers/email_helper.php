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

function email_menu($path)
    {
        $sx = '';        
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/email_ed/').'">'.msg("Email_edit").'</a></li>';
        $sx .= '<li>'.'<a href="'.base_url(PATH.'admin/email_test/').'">'.msg("Email_test").'</a></li>';
        return($sx);
    }

function email_le($id=1)
    {
    $sql = "select * from mensagem_own where id_m = " . round($id);
    $CI = &get_instance();
    $rlt = $CI -> db -> query($sql);
    $rlt = $rlt -> result_array();
    $line = $rlt[0];
    return($line);
    }

function email($para,$assunto,$texto,$de=1)
{
    $sx = enviaremail($para,$assunto,$texto,$de);
    return($sx);
}

function email_test($act, $d1)
    {
        $CI = &get_instance();
        $sx = '';
        
        switch ($d1) {
            case 'confirm':
                $para = 'renefgj@gmail.com';
                $assunto = 'e-mail de teste';
                $texto = 'teste de e-mail';
                $de = 1;
                echo email($para, $assunto, $texto, $de);
            break;

            /************** DEFAULT */
            default:
                $sx .= '<h1>Teste de e-mail</h1>';
                $sx .= '<li><a href="' . base_url(PATH . 'social/email/test/confirm') . '">' . msg('email_test_confirm') . '</a></li>';
            break;
        }
        return ($sx);
    }

function email_edit($id=1)
    {
        $form = new form;
        $form->id = $id;
        $cp = array();
        array_push($cp,array('$H8','id_m','',false,false));
        array_push($cp,array('$S80','m_descricao',msg('m_descricao'),True,True));
        array_push($cp,array('$S80','m_email',msg('m_email'),false,True));
        array_push($cp,array('$S80','smtp_host',msg('smtp_host'),false,True));
        array_push($cp,array('$S80','smtp_user',msg('smtp_user'),false,True));
        array_push($cp,array('$P80','smtp_pass',msg('smtp_pass'),false,True));
        $op = 'mail:mail&smtp:smtp';
        array_push($cp,array('$O '.$op,'smtp_protocol',msg('smtp_protocol'),True,True));
        array_push($cp,array('$S80','smtp_port',msg('smtp_port'),false,True));
        $op = 'html:html&text:text';
        array_push($cp,array('$O'.$op,'mailtype',msg('mailtype'),True,True));

        array_push($cp,array('$T80:6','m_header',msg('m_header'),false,True));        
        array_push($cp,array('$T80:6','m_foot',msg('m_foot'),false,True));        
        
        $sx = $form->editar($cp,'mensagem_own');
        return(array($sx,$form));
    }

function email_data($id=0)
    {
        $sx = '';
        $CI = &get_instance();
        $sql = "select * from mensagem_own ";
        if ($id > 0)
            {
                $sql = "where id_m = $id";
            }
        $sql .= " limit 1";
        $rlt = $CI->db->query($sql);
        $rlt = $rlt->result_array();

        if (count($rlt) > 0)
            {
                $line = $rlt[0];
                $sx .= '<div class="big" style="margin-top: 20px;"><b>'.msg('email_info').'</b></div>';
                $sx .= '<div>'.msg('m_descricao').': <b>'.$line['m_descricao'].'</b></div>';
                $sx .= '<div>'.msg('m_email').': <b>'.$line['m_email'].'</b></div>';
                $sx .= '<div>'.msg('smtp_host').': <b>'.$line['smtp_host'].'</b></div>';
                $sx .= '<div>'.msg('smtp_user').': <b>'.$line['smtp_user'].'</b></div>';
                $sx .= '<div>'.msg('smtp_pass').': <b>'.'***********'.'</b></div>';
                $sx .= '<div>'.msg('smtp_protocol').': <b>'.$line['smtp_protocol'].'</b></div>';
                $sx .= '<div>'.msg('smtp_port').': <b>'.$line['smtp_port'].'</b></div>';
                $sx .= '<div>'.msg('mailtype').': <b>'.$line['mailtype'].'</b></div>';
            } else {
                $sx = message(msg('Configuration not found'),3);
            }
        return($sx);
    }

function is_email($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        list($alias, $domain) = explode("@", $email);
        $domain = trim(ascii($domain));
        if (checkdnsrr($domain, "MX")) {
            return 1;
        } else {
            return -1;
        }
    } else {
        return -2;
    }
}

function enviaremail($para, $assunto, $texto, $de=1, $anexos = array()) {
    global $sem_copia,$sender;

    $CI = &get_instance();
    if (!isset($sem_copia)) { $sem_copia = 0; }
    if (!is_array($para)) { $para = array($para);}    
    
    if (isset($sender)) 
        {
            $config = $sender;
            $email_footer = '';
            $email_header = '';
            $e_mail = $sender['smtp_user'];
            $e_nome = $sender['smtp_user'];
            $line['smtp_protocol'] = $sender['smtp_protocol'];
            $line['m_email'] = $sender['smtp_user'];
            $line['m_descricao'] = $sender['smtp_user'];
        } else {
            /* de */
            $line = email_le($de);

            $config = Array('protocol' => $line['smtp_protocol'], 
                        'smtp_host' => $line['smtp_host'], 
                        'smtp_port' => $line['smtp_port'], 
                        'smtp_user' => $line['smtp_user'], 
                        'smtp_pass' => $line['smtp_pass'], 
                        'mailtype' => 'html', 
                        'charset' => 'iso-8859-1', 
                        'wordwrap' => TRUE);

                /* Header & footer */
                /***************************************************/
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
        }
    

    $CI -> load -> library('email', $config);
    $CI -> email -> subject($assunto);
    $CI -> email -> message($texto);

    for ($r = 0; $r < count($anexos); $r++) {
        $CI -> email -> attach($anexos[$r]);
    }



    $CI -> email -> from($e_mail, $e_nome);
    $CI -> email -> to($para[0]);
    $CI -> email -> subject($assunto);
    $CI -> email -> message($email_header . $texto . $email_footer);
    $CI -> email -> mailtype = 'html';
    if ($sem_copia != 1) {
        array_push($para, trim($e_mail));
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

    $proto = $line['smtp_protocol'];
    $headers = 'From: '.$line['m_descricao'].' <'.$line['m_email'].'> ' . "\r\n" .
    'Reply-To: '.$line['m_email']. "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    switch($proto)
        {
            case 'mail':
                $to      = $para[0];
                $subject = $assunto;
                $message = $email_header . $texto . $email_footer;

                $headers = array();
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=iso-8859-1';   
                $headers[] = 'To: '.$para[0];
                $headers[] = 'From: '.$line['m_descricao'].' <'.$line['m_email'].'>'; 
                $headers = implode("\r\n", $headers);

                //$real_sender = '-f brapcici@gmail.com';
                $real_sender = '';
                $rst = mail($to, $subject, $message, $headers, $real_sender);
                if ($rst == 1)
                {
                    $sx .= 'Send to '.$to;
                } else {
                    $sx = 'Erro ao enviar para '.$to;
                }            
                return($sx);
            break; 

            case 'PHPMailer':
                echo 'PHPmailer';
                //Instantiation and passing `true` enables exceptions
                require("mail/PHPMailer.php");
                require("mail/SMTP.php");
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                    $mail->isSMTP();                                            //Send using SMTP
                    $mail->Host       = $sender['smtp_host'];                     //Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                    $mail->Username   = $sender['smtp_user'];                     //SMTP username
                    $mail->Password   = $sender['smtp_pass'];                               //SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                    $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                    //Recipients
                    $mail->setFrom($sender['smtp_user'], 'Mailer');
                    $mail->addAddress('renefgj@gmail.com', 'Rene');     //Add a recipient
                    //$mail->addAddress('ellen@example.com');               //Name is optional
                    $mail->addReplyTo($line['m_email'], $line['m_descricao']);
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    //Attachments
                    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                    //Content
                    $mail->isHTML(true);  
                    $to      = $para[0];
                    $subject = $assunto;
                    $message = $email_header . $texto . $email_footer;                     
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->AltBody = $headers;

                    $sx = $mail->send();
                    echo 'Message has been sent';
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }          

            /************ OUtros protocolos */
            default:
                $CI -> email -> send();
                return(1);
        }

    if (count($line) == 0)
        {
        echo('<font color="red">Proprietário do e-mail (' . $de . ') não configurado (veja mensagem_own)</font>');
        exit ;
        }
}
?>