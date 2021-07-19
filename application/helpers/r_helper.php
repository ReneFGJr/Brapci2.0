<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* CodeIgniter R Statistics
*
* @package     CodeIgniter
* @subpackage  Helpers
* @category    Helpers
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.brapci.inf.br/renefgj/
* @version     v0.21.04.21
*/

/*
ALTER TABLE `rdf_form_class` ADD `sc_global` INT NOT NULL DEFAULT '0' AFTER `sc_ord`;
ALTER TABLE `rdf_form_class` ADD `sc_gropup` VARCHAR(20) NOT NULL AFTER `sc_ativo`;
*/
class r
{
    var $dir = '/var/www/html/temp/';
    var $img = '';

    function fcn($ref)
        {
            $CI = &get_instance();
            $sql = "select * from brapci_cited.analysis_R where r_ref = '$ref' ";
            $rlt = $CI->db->query($sql);
            $rlt = $rlt->result_array();
            $line = $rlt[0];
            return($line['r_algoritmo'].cr().cr());
        }
    
    function library($l)
        {
            $sc = '#install.packages('.$l.')'.cr();
            $sc = 'library('.$l.')'.cr();
            return($sc);
        }

    function show_script($sc,$nrq)
        {
          $sx = '';
          if ($nrq == 0)
                {
                    $sx .= '
                    <script>
                    function myCopy(id) {
                        /* Get the text field */
                        var copyText = document.getElementById(id);

                        /* Select the text field */
                        copyText.select();
                        copyText.setSelectionRange(0, 99999); /* For mobile devices */

                        /* Copy the text inside the text field */
                        document.execCommand("copy");

                        /* Alert the copied text */
                        /* alert("Copied the text: " + copyText.value); */
                    }
                    </script>';
                }
            $qd = 'script_R'.$nrq;

            $sx .= '<a class="btn btn-outline-primary" onclick="$(\'#'.$qd.'\').toggle(\'slow\');">'.msg('Show_script_code').'</a>';
            $sx .= '<a href="#" onclick="myCopy(\''.$qd.'\');" class="btn btn-outline-primary">'.msg('Copy_code_to_clipboard').'</a>';
            $sx .= '<div>';
            $sx .= '<textarea id="'.$qd.'" style="height: 250px; display: none; font-size: 12px; color: blue;" class="form-control">';            
            $sx .= $sc;
            $sx .= '</textarea>';
            $sx .= '</div>';

  
            return($sx);
        }

    function exec($sc)
        {
            /*********** Arquivos de saida */
            $task = $_SESSION['__ci_last_regenerate'];
            $task = md5(date("YmdHis").$task);

            $script = $this->dir.'script_'.$task.'.R';
            $sc = ascii($sc);
            file_put_contents($script,$sc);
            exec("Rscript ".$script);
        }

    function image_name($n=0)
        {
            $n = round($n);
            /*********** Arquivos de saida */
            $task = $_SESSION['__ci_last_regenerate'];
            $task = md5(date("YmdHis").$task);
            $file = 'img_'.$task.'_'.$n.'.png';
            $this->img = $file;
            return($this->dir.$file);
        }

    function image($n,$dt=array())
        {
            $sx = $this->fcn('save_image');
            /* */
            $file = $this->image_name($n);
            $sx = troca($sx,'$img',$file);

            $sx .= cr();
            $sx .= cr();

            return($sx);
        }

    function datasets($df,$tp=1,$limit=10)
        {
            /***************************************** DATAFRAME */
            $v1 = '';
            $v2 = '';
            $l = 0;
            $vatual = 0;
            foreach($df as $name=>$value)
                {
                    if (($l <= $limit) or ($value >= $vatual))
                    {
                    if (strlen($v1) > 0)
                        {
                            $v1 .= ', ';
                            $v2 .= ', ';
                        }
                    switch($tp)
                        {
                            case '1':
                            $v1 .= '"'.nbr_author($name,5).'"';
                            break;

                            default:
                            $v1 .= '"'.trim($name).'"';
                            break;

                        }
                    $v2 .= $value;
                    $vatual = $value;
                    }
                    $l++;
                }
            $sc = 'df <- data.frame('.cr();
            $sc .= 'name = c('.$v1.'),'.cr();
            $sc .= 'producao = c('.$v2.')'.cr();
            $sc .= ')';
            $sc .= cr();
            $sc .= cr();

            return($sc);
        }
    function test()
        {   
            $sx = $this->delete_temp();  

            $file = '/var/www/html/brapci2.1/_repository/125240/2020/12/oai_ojs_pkp_sfu_ca_article_15770#00080.txt';

            /*********** Arquivos de saida */
            $task = $_SESSION['__ci_last_regenerate'];
            $task = md5(date("YmdHis").$task);

            $script = $this->dir.'script_'.$task.'.R';
            $pre_file = 'result_'.$task;

            /* Images */
            $ximg = 0;
            $img = array();
            $img[1] = $pre_file.'_'.chr(48+$ximg).'.png';

            /* Out */
            $out = $pre_file.'.txt';
            $sct = '
            t1 = c("Tudo Para Maiúsculas")
            t2 = c("'.utf8_decode('Tudo Para Maiúsculas').'")
            tolower(t1)
            tolower(t2)

            ############################ MANIPULANDO ARQUIVOS
            f <- \''.$file.'\'
            t <- scan(f, what="character", sep=\';\', encoding="UTF-8")
            
            write(t,"'.$out.'");

            ############################ Grafico
            x <- rnorm(6,0,1)
            png(filename="'.$this->dir.$img[1].'", width=1024, height=500)
            hist(x, col="red")
            dev.off()
            ';
            file_put_contents($script,$sct);

            exec("Rscript ".$script);
            $rst = 'IMAGE:<br>'.$this->html_image($img[1]);
            $rst .= '<hr>';
            $rst .= file_get_contents($out);
            $sx .= '<pre>'.$rst.'</pre>';
            return($sx);            
        }
    function html_image($img)
        {
            $sx = '<img src="'.base_url('image.php/?image='.$img).'" class="image-fluid img-fluid">';
            return($sx);
        }
    function delete_temp()
        {
            $dir = $this->dir;
            check_dir($dir);
            $exe = 'find '.$dir.' -maxdepth 1 -mmin +30 -name "*" -exec /bin/rm -f {} \;';
            exec($exe);
            return('Files exclued');
        }
}