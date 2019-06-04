<?php
class mailer extends CI_model {
    var $table = 'mensagem_own';

    function row() {

    }

    function cp() {
        $cp = array($id = 0);
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$S100', 'msg_subject', msg('msg_subject'), true, true));
        array_push($cp, array('$T80:6', 'msg_text', msg('msg_text'), true, true));
        array_push($cp, array('$I1', 'msg_own', msg('msg_own'), true, true));
        return ($cp);
    }

    function mail_to() {
        $cp = array($id = 0);
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$S100', 'msg_subject', msg('msg_subject'), true, true));
        array_push($cp, array('$T80:6', 'msg_text', msg('msg_text'), true, true));
        $sql = "select * from mensagem_own";
        array_push($cp, array('$Q id_m:m_descricao:' . $sql, 'msg_own', msg('msg_own'), true, true));

        $link = '<a href="' . base_url(PATH . 'mail/message_templat') . '">' . msg('msg_templat') . '</a>';
        array_push($cp, array('$M', '', $link, false, true));

        $form = new form;
        $tela = $form -> editar($cp, '');
        return ($tela);
    }

    function message_templat($id = '', $pg = '') {
        $form = new form;

        $form -> fd = array('id_mt', 'mt_subject');
        $form -> lb = array('id', msg('us_name'));
        $form -> mk = array('', 'L', 'L', 'A');

        $form -> tabela = 'mensagem_templat';
        $form -> see = True;
        $form -> novo = perfil("#ADMIN");
        $form -> edit = perfil("#ADMIN");

        $form -> row_edit = base_url(PATH . 'mail/message_templat');
        $form -> row_view = base_url(PATH . 'mail/message_templat');
        $form -> row = base_url(PATH . 'mail/message_templat');
        
        $sx = row($form, $id);
        
        return ($sx);
    }

}
?>
