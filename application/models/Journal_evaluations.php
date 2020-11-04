<?php
class journal_evaluations extends CI_Model 
{
    var $base = 'brapci_evaluation.';
    function __construct()
    {
        $this->load->model('oai_pmh');
        $this->oai_pmh->base = 'brapci_evaluation.';
        $this->load->model('sources');
        $this->sources->table = 'brapci_evaluation.source_source';        
    }
    function parameters()
    {
        $d = array();
        $d['table'] = $this->sources->table;
        $d['fields'] = array(0,70,10,10);
        $d['page_view'] = base_url(PATH.'evaluation/source');
        return($d);
    }
    function index($d1,$d2,$d3,$d4)
    {
        $sx = '';
        switch($d1)
        {
            case 'set':
                $sx = $this->set();
            break;
            
            case 'identify':
                $sx .= $this->identify();
                $sx .= '<br>'.$this->back();
            break;
            
            case 'sets':
                $sx .= $this->sets();
                $sx .= '<br>'.$this->back();
            break;
            
            case 'sources':
                $sx .= $this->sources($d2,$d3,$d4);                
                $sx .= '<br>'.$this->back();
                /* Atualiza data das publicações */
                $this->set_years();
            break;
            
            case 'source':
                $sx .= $this->source($d2,$d3);
                $sx .= '<br>'.$this->back();
            break;       
            
            case 'listsets':
                $sx .= $this->listsets();
                $sx .= '<br>'.$this->back();
            break;            
            
            default:
            $sx .= '<h1>'.msg('journal_evaluation').'</h1>';
            $sx .= '<ul>';
            $jnl = $this->rset();
            $sx .= '<li>';
            $sx .= '<a href="'.base_url(PATH.'evaluation/set').'">'.msg('select_journal_url').'</a>';
            $sx .= ' '.msg('or').' ';
            $sx .= '<a href="'.base_url(PATH.'evaluation/sources').'">'.msg('source_journal').'</a>';
            $sx .= '</li>';
            
            if ($jnl > 0)
            {
                $sx .= '<li>'.'<a href="'.base_url(PATH.'evaluation/identify').'">'.msg('ev_identify_journal').'</a>'.'</li>';
                $sx .= '<li>'.'<a href="'.base_url(PATH.'evaluation/sets').'">'.msg('ev_sets_journal').'</a>'.'</li>';
                $sx .= '<li>'.'<a href="'.base_url(PATH.'evaluation/listsets').'">'.msg('ev_listsets_journal').'</a>'.'</li>';
            }
            $sx .= '</ul>';
        }
        return($sx);
    }
    function back()
    {
        $sx = '<a href="'.base_url(PATH.'evaluation').'" class="btn btn-outline-primary">'.msg('return').'</a>';
        return($sx);
    }  
    
    function set_years()
    {
        $sql = "update ".$this->base."source_listidentifier 
        set li_year = year(li_datestamp)
        where li_year = 0 limit 1000";
        $rlt = $this->db->query($sql);
        return($sql);
    }
    
    function listsets()
    {
        set_time_limit(60000);
        $sx = '';
        $id = $this->rset();
        $dt = $this->oai_pmh->ListIdentifiers_harvesting($id);
        $sx .= '<div class="row">';
        $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">'.msg("Sections_journal").'</div>';
        
        for ($r=0;$r < count($dt);$r++)
        {
            $line = (array)$dt[$r];
            $sx .= '<div class="col-4 text-right small">'.(string)$line['setSpec'].'</div>';
            $sx .= '<div class="col-8"><b>'.(string)$line['setName'].'</b></div>';
        }        
        $sx .= '</div>';
        return($sx);
    }
    
    function sets()
    {
        $sx = '';
        $data = $this->oai_data();
        $dt = $this->oai_pmh->getListSets(0,$data);
        $sx .= '<div class="row">';
        $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">'.msg("Sections_journal").'</div>';
        
        for ($r=0;$r < count($dt);$r++)
        {
            $line = (array)$dt[$r];
            $setEsp = (string)$line['setSpec'];
            $setName = (string)$line['setName'];
            $sx .= '<div class="col-4 text-right small">'.$setEsp.'</div>';
            $sx .= '<div class="col-8"><b>'.$setName.'</b></div>';
            $this->update_sets($data['id_jnl'],$setEsp,$setName);
        }        
        $sx .= '</div>';
        return($sx);
    }
    
    function update_sets($id,$cod,$setName)
    {
        if (strpos($setName,'Ã') > 0)
        {
            $setName = utf8_decode($setName);
        }            
        $sql = "select * from ".$this->base."source_sets
        where sets_journal = $id and
        sets_session = '$cod'";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) == 0)
        {
            $sql = "insert into ".$this->base."source_sets
            (sets_journal, sets_session, sets_name)
            values
            ('$id','$cod','$setName')";
            $rrr = $this->db->query($sql);
        }
    }
    
    function sources()
    {
        $sx = '';
        $par = $this->parameters();
        $sx = row3($par);
        return($sx);
    }
    
    function source($d1,$d2)
    {
        $sx = '';
        $p = $this->parameters();
        $_SESSION['jnl_id'] = $d1;
        $dt = le($p['table'],'id_jnl='.$d1);
        $sx .= show($dt);
        return($sx);
    }        
    
    function identify()
    {        
        //$this->load->model('sources');
        
        $id = $this->rset();
        $dt = $this->oai_pmh->identify($id);
        $sx = $this->journal_update($dt);
        return($sx);
    }
    
    function journal_update($dt)
    {
        $sx = '';
        if (isset($dt['repositoryName']))
        {
            $name = $dt['repositoryName'];
            $url_oai = $dt['baseURL'];
            $url = troca($url_oai,'/oai','');
            $sx .= '<div class="row">';
            
            $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">'.msg("About_journal").'</div>';
            $sx .= '<div class="col-2 text-right small">'.msg("journal_name").'</div>';
            $sx .= '<div class="col-10"><b>'.$name.'</b></div>';
            
            $sx .= '<div class="col-2 text-right small">'.msg("journal_administrator").'</div>';
            $sx .= '<div class="col-10"><b>'.$dt['adminEmail'].'</b></div>';
            
            $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">'.msg("OAI Protocol").'</div>';
            $var = array('protocolVersion','deletedRecord','granularity','baseURL');
            for ($r=0;$r < count($var);$r++)
            {
                $sx .= '<div class="col-4 text-right small">'.msg($var[$r]).'</div>';
                $sx .= '<div class="col-8"><b>'.msg($dt[$var[$r]]).'</b></div>';
            }
            
            $sx .= '</div>';
        }
        $id = $dt['id_jnl'];
        $sql = "update ".$this->sources->table."
        set jnl_oai_last_harvesting = '".date("Y/m/d-H:i:s")."', 
        jnl_active = 1,
        jnl_name = '$name'
        where id_jnl = ".$id;
        $rrr = $this->db->query($sql);
        return($sx);
    }
    
    function oai_data()
    {
        $data = array();
        if (isset($_SESSION['jnl_id']))
        {
            $id = round($_SESSION['jnl_id']);
            $data = $this->sources->le($id);
        } else {
            echo "OPS 554";
        }
        return($data);
    }
    function rset()
    {
        if (isset($_SESSION['jnl_id']))
        {
            $id = $_SESSION['jnl_id'];
            return($id);
        } else {
            return(0);
        }
    }
    function set()
    {
        $form = new form;
        $cp = array();
        if (get("dd1") == '')
        {
            //$_POST['dd1'] = $this->rset();                        
        }
        array_push($cp,array('$H8','','',false,false));
        array_push($cp,array('$S100','',msg('journal_url'),true,true));
        array_push($cp,array('$O 0:'.msg('no').'&1:'.msg("yes"),'',msg('scielo_index'),true,true));
        $sx = $form->editar($cp,'');
        if ($form->saved > 0)
        {
            $url = get("dd1");            
            while (substr($url,strlen($url)-1,1) == '/')
            {
                $url = substr($url,0,strlen($url)-1);
            }
            if (substr($url,strlen($url)-6,6) == '/index')
            {
                $url = substr($url,0,strlen($url)-6);
            } else {
                //echo substr($url,strlen($url)-6,5);
            }
            $url_oai = $url.'/oai';
            $sql = "select * from ".$this->sources->table." 
            where jnl_url = '$url'";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) == 0)
            {
                $xsql = "insert into ".$this->sources->table." 
                (jnl_name, jnl_url, jnl_url_oai)
                values
                ('no name yet','$url','$url_oai')";
                $rlt = $this->db->query($xsql);
                sleep(1);
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
            }
            $id = $rlt[0]['id_jnl'];
            $_SESSION['jnl_id'] = $id;
            redirect(base_url(PATH.'evaluation/identify/'));
        }
        return($sx);
    }
}
?>


<!-- jQuery Timeline -->
<link rel="stylesheet" href="https://ka2.org/jqtl-v2/dist/jquery.timeline.min.css?v=1601370388">
<link rel="stylesheet" href="https://ka2.org/jqtl-v2/dist/jquery.timeline.demo.css?v=1557716496">
<style>
.thumbnail { height: 32px; width: 32px; margin: auto 1em auto 0; background-repeat: no-repeat; background-size: cover; background-position: 50% 50%; }
.thumbnail.circled { border-radius: 50%; }
#myModal .modal-body { opacity: 0; transition: all 0.5s ease; }
#myModal .jqtl-event-view { display: flex; }
#myModal .jqtl-event-view > * { width: 50%; }
#notices p { margin-bottom: 0; }
#notice-php label, #notice-js label { font-weight: 600; margin-right: 1em; }
/* .popover { box-shadow: 0 1px 0 4px rgba(51,51,51,0.05); } */
.popover-body { color: #444; }
.popover-body b { font-weight: 500; color: #212529; }
</style>
<div class="container">
<section class="row">
<div class="col-12">
<div id="my-timeline" class="test-timeline test-1">
<ul class="timeline-events">
<li data-timeline-node="{id:1,start:'2008',row:1,content:'2009-content',size:'30',extend:{toggle:'modal',target:'#myModal'}}">2008</li>
<li data-timeline-node="{id:2,start:'2009',row:1,content:'2009-content',size:'34',relation:{before:1},extend:{toggle:'popover',trigger:'hover'}}">2009</li>
<li data-timeline-node="{id:3,start:'2010',row:1,content:'2010-content',size:'22',relation:{before:2},extend:{toggle:'popover',trigger:'hover'}}">2010</li>
<li data-timeline-node="{id:4,start:'2011',row:4,content:'2011-content',size:'22',relation:{before:4},extend:{toggle:'popover',trigger:'hover'}}">2010</li>
<li data-timeline-node="{id:5,start:'2012',row:4,content:'2011-content',size:'27',relation:{before:4},extend:{toggle:'popover',trigger:'hover'}}">2010</li>
</ul>
</div><!-- /#my-timeline -->
</div><!-- /.col -->
</section><!-- /.row -->
</div><!-- /.container-fluid -->


<!-- jQuery (latest 3.3.1) -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script defer src="https://ka2.org/jqtl-v2/dist/jquery.timeline.min.js?v=1601370392"></script>
<!-- local scripts -->
<script>
window.addEventListener('load',function(){
    //$(function () {
        
        
        const dt = new Date()
        
        let defaults = 
        {
            "type":"point",
            "startDatetime":"2008","endDatetime":"2020",
            "scale":"year",
            "rows":"auto",
            "minGridSize":90,
            "headline":
            {
                "display":true,
                "title":"Produção",
                "range":true,
                "locale":"en-US"
            },
            
            "sidebar": 
            {
                "sticky":true,
                "list":[
                    "<a name=\"row-01\">Articles<\/a>",
                    "<a name=\"row-02\">Dossie<\/a>",
                    "<a name=\"row-03\">Outros<\/a>",
                    "<a name=\"row-04\">Editorial<\/a>",
                    ]
                }
            },
            overrides = 
            {
                startDatetime: '2008',
                endDatetime: '2020',
                scale: 'year',
                minGridSize: 80,
                headline: {
                    title: 'Publicação anual 2',
                    range: true,
                },
                footer: {
                    display: false,
                },
                ruler: {
                    top: {
                        lines: [ 'year' ],
                        format: { month: 'numeric' }
                    },
                },
                effects: {
                    hoverEvent: true,
                },
                reloadCacheKeep: true,
                zoom: false,
                debug: false
            },
            mcu_options = Object.assign( defaults, overrides )
            
            $('#my-timeline').Timeline( mcu_options )
            .Timeline('initialized', function(e,v){
                $('.jqtl-headline-wrapper').append('<div><a href="/" class="btn btn-secondary btn-sm">&laquo; Home</a></div>')
                //$('[data-toggle="popover"]').popover()
            })
            
            $('#myModal').on('shown.bs.modal', function(){
                $(this).find('.modal-title').empty().append( $(this).find('.jqtl-event-title').html() ).end()
                .find('.jqtl-event-title').remove().end()
                .find('.modal-body').css('opacity','1')
            })
            
            //})
        },false);
        </script>
        </body>
        </html>
