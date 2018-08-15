<?php
class nets extends CI_model {
    function pinterest($data) {
        return('');
        $d = $this -> dados($data);
        $nm = $d['title'] . '. ' . $d['autor_resumido'] . ' ' . $d['http'];
        $nm = urlencode($nm);
        $url = 'https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href=' . $nm . '&picture=&title=Aspectos%20%C3%A9ticos%20da%20coautoria%20em%20publica%C3%A7%C3%B5es%20cient%C3%ADficas&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html';
        $link = '<span onclick="newwin2(\'' . $url . '\',800,400);" class="btn-primary" id="tw' . date("Ymdhis") . '" style="cursor: pointer; padding: 5px 10px; border-radius: 4px;">';
        $link = '<img src="' . base_url('img/nets/icone_pinterest.png') . '" class="icone_nets">';
        $link .= '</span>' . cr();
        return ($link);
    }

    function linked($data) {
        $d = $this -> dados($data);
        $url = $d['doi'];
        if (strlen($url) == 0) {
            $url = $d['http'];
        }
        $nm = $d['title'] . '. ' . $d['autor'] . ' ' . $url;
        $nm = urlencode($nm);
        $url = 'https://www.linkedin.com/shareArticle?mini=true&url='.$url.'&title='.$nm.'&ro=false&summary=&source=';
        $link = '<span onclick="newxy(\'' . $url . '\',600,600);" id="lk' . date("Ymdhis") . '" style="cursor: pointer;">';
        $link .= '<img src="' . base_url('img/nets/icone_linked.png') . '" class="icone_nets">';
        $link .= '</span>' . cr();
        return ($link);
    }

    function google($data) {
        $d = $this -> dados($data);
        $url = $d['doi'];
        if (strlen($url) == 0) {
            $url = $d['http'];
        }
        $nm = $d['title'] . '. ' . $d['autor'] . ' ' . $url;
        $nm = urlencode($nm);

        $url = 'https://plus.google.com/share?url=' . $url . '&t=' . $nm;
        $link = '<span onclick="newxy(\'' . $url . '\',400,600);" id="tw' . date("Ymdhis") . '" style="cursor: pointer;">';
        $link .= '<img src="' . base_url('img/nets/icone_google.png') . '" class="icone_nets">';
        $link .= '</span>' . cr();
        return ($link);
    }

    function facebook($data) {
        $d = $this -> dados($data);
        $url = $d['doi'];
        if (strlen($url) == 0) {
            $url = $d['http'];
        }
        $nm = $d['title'] . '. ' . $d['autor_resumido'] . ' ' . $url;
        $nm = urlencode($nm);
        //https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href=http%3A%2F%2Fseer.ufrgs.br%2Findex.php%2FEmQuestao%2Farticle%2Fview%2F56837%23.W3NwkU7trRU.facebook&picture=&title=Empoderamento%20das%20mulheres%20quilombolas%3A%20contribui%C3%A7%C3%B5es%20das%20pr%C3%A1ticas%20mediacionais%20desenvolvidas%20na%20Ci%C3%AAncia%20da%20Informa%C3%A7%C3%A3o&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html
        $url = 'https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href=' . $nm . '&picture=&title=Aspectos%20%C3%A9ticos%20da%20coautoria%20em%20publica%C3%A7%C3%B5es%20cient%C3%ADficas&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html';
        $link = '<span onclick="newwin2(\'' . $url . '\',800,400);" id="tw' . date("Ymdhis") . '" style="cursor: pointer;">';
        $link .= '<img src="' . base_url('img/nets/icone_facebook.png') . '" class="icone_nets">';
        $link .= '</span>' . cr();
        return ($link);
    }

    function twitter($data) {
        $d = $this -> dados($data);
        //echo '<pre>';
        //print_r($d);
        //echo '</pre>';
        $url = $d['doi'];
        if (strlen($url) == 0) {
            $url = $d['http'];
        }

        $nm = $d['title'] . '. ' . $d['autor_resumido'] . '. ' . $url . ' #BRAPCI';
        $nm = urlencode($nm);
        $url = 'https://twitter.com/intent/tweet?text=' . $nm . '&related=';
        $link = '<span onclick="newwin2(\'' . $url . '\',800,400);" id="tw' . date("Ymdhis") . '" style="cursor: pointer;">';
        $link .= '<img src="' . base_url('img/nets/icone_twitter.png') . '" class="icone_nets">';
        $link .= '</span>' . cr();
        return ($link);
    }

    function dados($data) {
        $title = '';
        $autor = '';
        $autor_resumido = '';
        $doi = '';
        $source = '';
        $doi = '';
        $subject = '';

        for ($r = 0; $r < count($data); $r++) {
            $line = $data[$r];
            $id = $line['d_r1'];
            $class = trim($line['c_class']);
            $name = trim($line['n_name']);
            //echo $class.'-'.$name.'<hr>';
            switch($class) {
                case 'hasRegisterId' :
                    if (substr($name, 0, 3) == '10.') {
                        $doi = troca($name, 'DOI:', '');
                        $doi = 'http://dx.doi.org/' . $doi;
                    }

                    break;
                case 'hasSubject' :
                    if (strlen($name) > 0) {
                        $name = ucwords($name);
                        $subject .= '#' . troca(ucFirst($name), ' ', '') . ' ';
                        $subject = troca($subject, '&', '');
                        $subject = troca($subject, '-', '');
                    }
                    break;

                case 'hasSource' :
                    if (strlen($source) == 0) {
                        $src = $name;
                        $src1 = substr($src, 0, strpos($src, ';'));
                        $src1 = troca($src1, ' ', '') . ' ';
                        $src2 = substr($src, strpos($src, ';') + 1, strlen($src));
                        $source = '#' . $src1 . $src2;
                        $source = troca($source, '&', '');
                    }

                    break;
                case 'hasTitle' :
                    if (strlen($title) == 0) {
                        $title = $name;
                    }
                    break;
                case 'hasAuthor' :
                    $au = nbr_autor($name, 7);
                    $aun = nbr_autor($name, 9);

                    if (strlen($au) > 0) {
                        if (strlen($autor) > 0) { $autor .= '; ';
                            $autor_resumido .= '; ';
                        }
                        $au = troca($au, ' ', '');
                        $autor .= trim($au);
                        $autor_resumido .= trim($aun);
                    }

                    break;
            }
        }
        $d = array();
        $d['title'] = $title;
        $d['autor'] = $autor;
        $d['autor_resumido'] = $autor_resumido;
        $d['http'] = base_url(PATH . 'v/' . $id);
        $d['doi'] = $doi;
        $d['subject'] = $subject;
        //$nm = urlencode($nm);
        return ($d);
    }

}
?>
