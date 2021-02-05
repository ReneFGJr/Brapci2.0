<?php
class highcharts
    {
    function head()
            {
            $sx = '
                <script src="https://code.highcharts.com/highcharts.js"></script>
                <script src="https://code.highcharts.com/highcharts-3d.js"></script>
                <script src="https://code.highcharts.com/modules/exporting.js"></script>
                <script src="https://code.highcharts.com/modules/export-data.js"></script>
                <script src="https://code.highcharts.com/modules/accessibility.js"></script>

                <figure class="highcharts-figure">
                <div id="container" style="height: 600px;"></div>
                </figure>            
            ';
            return($sx);
            }

      function grapho($data=array())
        {
            $sx = '';
            $type_bar = 'column'; /* bar */
            $subtitle = '';
            $title = 'Title';
            $LABEL_ROTATION = 0;
            $LEG_HOR = '$LEG_HOR';
            $CATS = "'Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas'";
            $DATA = '15654, 4064, 1987, 976, 846';
            if (isset($data['LEG_HOR'])) { $LEG_HOR = $data['LEG_HOR']; }
            if (isset($data['TITLE'])) { $title = $data['TITLE']; }
            if (isset($data['TYPE'])) { $type_bar = $data['TYPE']; }
            if (isset($data['DATA']))
                {
                    $DATA = '';
                    for($r=0;$r < count($data['DATA']);$r++)
                        {
                            if (strlen($DATA) > 0) { $DATA .= ', '; }
                            $DATA .= $data['DATA'][$r];
                        }
                }
            if (isset($data['CATS']))
                {
                    $CATS = '';
                    for($r=0;$r < count($data['CATS']);$r++)
                        {
                            if (strlen($CATS) > 0) { $CATS .= ', '; }
                            $CATS .= "'".$data['CATS'][$r]."'";
                        }
                }                

            $sx .= $this->head();

            $sx .= '
            <script>
            // Set up the chart
            const chart = new Highcharts.Chart({
            chart: {
                renderTo: \'container\',
                type: \''.$type_bar.'\',
                options3d: {
                enabled: true,
                alpha: 0,
                beta: 5,
                depth: 50,
                viewDistance: 45
                }
            },
                
            title: { text: \''.$title.'\' },
            subtitle: { text: \''.$subtitle.'\' },
            plotOptions: {
                column: {
                depth: 125
                }
            },           
            
            xAxis: {
                categories: ['.$CATS.'],
                labels: {
                rotation: '.$LABEL_ROTATION.',
                style: {
                    fontSize: \'14px\',
                    fontFamily: \'Tahoma, Verdana, sans-serif\'
                    }
                },
            },            
            series: [ { name: \''.$LEG_HOR.'\', data: [ '.$DATA. '] }]
            });
            </script>';
            return($sx);
        }  
    }