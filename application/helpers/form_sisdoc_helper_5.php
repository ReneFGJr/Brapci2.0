<?php
function wclose()
    {
        $sx = '<script>	window.opener.location.reload(); close();</script>';
        return($sx);
    }

function newxy($url, $xx, $yy)
{
    $sx = "
    NewWindow = window.open($url, 'newwin', 'scrollbars=yes,resizable=no,width=$xx,height=$yy,top=10,left=10');
    NewWindow.focus();
    void (0);
    ";
    return($sx);
}

function sround($v)
    {
        $v = sonumero($v);
        if ($v == '')
            {
                return 0;
            }
        return sround($v);

    }
function menus()
    {

    }