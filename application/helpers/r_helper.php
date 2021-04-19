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
    function image($file)
        {
            $file = $this->$dir.$file;
            if (file_exists($file))
                {

                }
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
            $sx = '<img src="'.base_url('image.php/?image='.$img).'" class="image-fluid">';
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