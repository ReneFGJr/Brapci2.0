<div class="row">
<div class="col-md-12">
<h4>Timeline</h4>
<?php
$prod = array();
$prod[0] = 0;
for ($r = date("Y")+1;$r > 1960;$r--)
    {
        $prod[$r] = 0;
    }
for ($r=0;$r < count($meta);$r++)
    {
        if ($meta[$r]['c_class'] == 'hasAuthor')
            {
                $vlr = $meta[$r]['d_r1'];
                $fl = 'c/'.$vlr.'/name.ABNT';
                if (file_exists($fl))
                    {
                        $txt = file_get_contents($fl);
                        $ano = round(substr($txt,strpos($txt,'PY - ')+5,4));
                        if (isset($prod[$ano]))
                            {
                                $prod[$ano] = $prod[$ano] + 1;
                            } else {
                                $prod[$ano] = 1;
                            }
                    }
            }
    }
$max = 15;
$lg = 12;
$tb = '<table width="100%" style="margin: 0px; padding: 0px;">';
$sl = '';
$tx = '';
$obs = '';
foreach($prod as $ano => $tot)
    {
        if ($ano > 0)
        {
        $sx = '<td style="border-right: 1px solid #eee; border-top: 1px solid #888;"
                    >';
        $wh = (int)round(($tot / $max) * 150);
        if ($wh < 1) { $wh = 1; }
        $sx .= '<img src="'.base_url('img/point/point_blue.png').'" ';
        $sx .= ' style="height: '.$wh.'px; width: '.$lg.'px;"';
        $sx .= ' title="Total de '.$tot.' trabalhos em '.$ano.'." ';
        $sx .= '>';
        $sx .= '</td>';
        $sx .= cr();
        
        $ms_ano = '';
        if (substr($ano,3,1) == '0')
            {
                $ms_ano .= substr($ano,0,1).'<br>';
                $ms_ano .= substr($ano,1,1).'<br>';
                $ms_ano .= substr($ano,2,1).'<br>';
            } else {
                $ms_ano .= '&nbsp;<br>';
                $ms_ano .= '&nbsp;<br>';
                $ms_ano .= '&nbsp;<br>';
            }
        $ms_ano .= substr($ano,3,1);
        $sl = '<td aling="center" 
                style="font-size: 10px; margin: 0px; padding: 0px; 
                border-right: 1px solid #eee; text-align: center;"
                >'.$ms_ano.'</td>' . $sl;
        $tx = $sx . $tx;
        } else {
            $obs = 'Não identificado(s) '.$tot;
        }
    }    
$tb .= '<tr>'.$sl.'</tr>'.cr();
$tb .= '<tr>'.$tx.'</tr>'.cr();
if (strlen($obs) > 0)
    {
        $tb .= '<tr><td colspan=60>'.$obs.'</td></tr>';
    }
$tb .= '</table>';
echo $tb;
?>
</div></div>
