
<?php
class highchart
{
    function show($data)
    {
        $sx = '';
        $sx .= $this->prepare();
        $sx .= $this->grapho($data);
        $sx .= $this->dashboard($data);
        return ($sx);
    }
    function prepare()
    {
        $sx = '';
        $sx .= '<script src="https://code.highcharts.com/highcharts.js"></script>';
        return ($sx);
    }
    function dashboard($data)
    {
        $sx = '
            <div class="container">
                <br/>
                <h4 class="text-center">'.$data['title'].'</h4>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div id="container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        return ($sx);
    }

    function series($data=array())
        {
            $sx = '';
            for ($r=1;$r <= count($data['x']);$r++)
                {
                    $serie_name = $data['y'][$r];
                    if (strlen($sx) > 0) { $sx .= ', '; }
                    $sx .= "{name: '$serie_name', data: [";
                        for ($y=0;$y < count($data['x'][$r]);$y++)
                            {
                                if ($y > 0) { $sx .= ','; }
                                $sx .= $data['x'][$r][$y];
                            }
                    $sx .= "]}".cr();
                }
            return($sx);
        }    

    function legends($data=array())
        {
            $sx = '';
            for ($r=0;$r < count($data['s']);$r++)
                {
                    if ($r > 0) { $sx .= ', '; }
                    $sx .= "'".$data['s'][$r]."'";
                }
            return($sx);
        }
    function grapho($data=array(),$type='line')
    {
        $series = $this->series($data);
        $legend = $this->legends($data);
        /* Type = column, line */
        $sx = '
        <script type="text/javascript">
        $(function () 
            { 
                $(\'#container\').highcharts({
                chart: {
                    type: \''.$type.'\'
                },
                title: {
                    text: \'\'
                },
                xAxis: {
                    categories: ['.$legend.'],
                    title: {
                        text: \''.$data['eixo_x'].'\'
                    }
                },
                yAxis: {
                    title: {
                        text: \''.$data['eixo_y'].'\'
                    }
                },
                series: ['.$series.']
            });
        });
        </script>';
        return ($sx);
    }
}
