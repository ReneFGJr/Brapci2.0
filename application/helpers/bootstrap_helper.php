<?php
/**
* CodeIgniter Form Helpers
*
* @package     CodeIgniter
* @subpackage  BootStrap
* @category    Helpers
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.sisdoc.com.br/CodIgniter
* @version     v0.21.01.31
*/
function bscol($c)
    {
        switch($c)
            {
                case '12':
                    $sx = 'col-md-12';
                    $sx .= ' col-'.$c;
                    $sx .= ' col-sm-'.$c;
                    $sx .= ' col-lg-'.$c;
                    $sx .= ' col-xl-'.$c;
                break;

                case '2':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-4';
                    $sx .= ' col-lg-2';
                    $sx .= ' col-xl-2';
                break;   

                case '3':
                    $sx = 'col-md-6';
                    $sx .= ' col-6';
                    $sx .= ' col-sm-6';
                    $sx .= ' col-lg-2';
                    $sx .= ' col-xl-2';
                break;  

                case '5':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-5';
                    $sx .= ' col-lg-5';
                    $sx .= ' col-xl-5';
                break;

                case '7':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-7';
                    $sx .= ' col-lg-7';
                    $sx .= ' col-xl-7';
                break;

                case '6':
                    $sx = 'col-md-6';
                    $sx .= ' col-6';
                    $sx .= ' col-sm-6';
                    $sx .= ' col-lg-6';
                    $sx .= ' col-xl-6';
                break;                                           

                case '10':
                    $sx = 'col-md-12';
                    $sx .= ' col-12';
                    $sx .= ' col-sm-6';
                    $sx .= ' col-lg-10';
                    $sx .= ' col-xl-10';
                break;

                default:
                    $c = sonumero($c);
                    $sx = 'col-md-'.$c;
                    $sx .= ' col-'.$c;
                    $sx .= ' col-sm-'.$c;
                    $sx .= ' col-lg-'.$c;
                    $sx .= ' col-xl-'.$c;
                break;
            }
        return($sx);
    }
function bs_pages($ini,$stop,$link='')
    {
        $sx = '';
        $sx .= '<nav aria-label="Page navigation example">'.cr();
        $sx .= '<ul class="pagination">'.cr();
        for ($r=$ini;$r <= $stop;$r++)
            {
                $xlink = base_url($link.'/'.chr($r));
                $sx .= '<li class="page-item"><a class="page-link" href="'.$xlink.'">'.chr($r).'</a></li>'.cr();
            }
        $sx .= '</ul>';
        $sx .= '</nav>';
        return($sx);
    }
function bs_alert($type = '', $msg = '') {
    $ok = 0;
    switch($type) {
        case 'success' :
            $ok = 1;
            break;
        case 'secondary' :
            $ok = 1;
            break;
        case 'danger' :
            $ok = 1;
            break;
        case 'warning' :
            $ok = 1;
            break;
        case 'info' :
            $ok = 1;
            break;
        case 'light' :
            $ok = 1;
            break;
        case 'dark' :
            $ok = 1;
            break;
        default :
            $sx = 'TYPE: primary, secondary, success, danger, warning, info, light, dark';
    }
    if ($ok == 1) {
        $sx = '<br><div class="alert alert-' . $type . '" role="alert">
                ' . $msg . '
               </div>' . cr();
    }
    return($sx);
}
?>