<?php
defined("BASEPATH") or exit("No direct script access allowed");

/**
 * CodeIgniter Form Helpers
 *
 * @package     CodeIgniter
 * @subpackage  IA
 * @category    IA-R
 * @author      Rene F. Gabriel Junior <renefgj@gmail.com>
 * @link        http://www.sisdoc.com.br/CodIgniter
 * @version     v0.21.04.10
 */

class ias_r extends CI_Model
{
    function index($d1, $d2, $d3)
    {
        switch ($d1) {
                case 'test':
                $this->load->helper('R');
                $R = new r;
                $sx = $R->test();
                break;

                default:
                $action = array(
                        'test');
                $sx = '';
                $sx .= '<h2>'.msg("Cited").'</h2>';
                $sx .= '<ul>';
                for ($r=0;$r < count($action);$r++)
                    {
                        $link = '<a href="'.base_url(PATH.'ia/R/'.$action[$r]).'">';
                        $linka = '</a>';
                        $sx .= '<li>'.$link.$action[$r].$linka.'</li>';
                    }
                $sx .= '</ul>';
                
        }
        return ($sx);
    }

    function test()
        {
            $sx = 'TEST';
            return($sx);
        }
}