
<?php
class highchart
{
    function show($data)
    {
        $sx = '<h1>GRAFICO</h1>';
        $sx .= $this->prepare();
        $sx .= $this->grapho($data);
        $sx .= $this->dashboard();
        return ($sx);
    }
    function prepare()
    {
        $sx = '';
        $sx .= '<script src="https://code.highcharts.com/highcharts.js"></script>';
        return ($sx);
    }
    function dashboard()
    {
        $sx = '
            <div class="container">
                <br/>
                <h2 class="text-center">Highcharts php mysql json example</h2>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-heading">Dashboard</div>
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
            echo '<pre>';
            print_r($data);
            for ($r=0;$r < count($data['x']);$r++)
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

    function legendas($data=array())
        {
            $sx = '';
            for ($r=0;$r < count($data['x']);$r++)
                {
                    $serie_name = $data['s'][$r];
                    if (strlen($sx) > 0) { $sx .= ', '; }
                    $sx .= "{name '$serie_name', data [";
                        for ($y=0;$y < count($data['x'][$r]);$y++)
                            {
                                if ($y > 0) { $sx .= ','; }
                                $sx .= $data['x'][$r][$y];
                            }
                    $sx .= "]}".cr();
                }
            return($sx);
        }
    function grapho($data=array(),$type='line')
    {
        $series = $this->series($data);
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
                    text: \'Yearly Website Ratio\'
                },
                xAxis: {
                    categories: [\'2013\',\'2014\',\'2015\', \'2016\']
                },
                yAxis: {
                    title: {
                        text: \'Rate\'
                    }
                },
                series: ['.$series.']
            });
        });
        </script>';
        return ($sx);
    }
}
