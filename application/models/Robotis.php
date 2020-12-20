<?php
/* Cron TAB
* apt install cron
* systemctl enable cron
* Add line in /etc/crontab
30 *    * * *   root    /var/www/html/Brapci2.0/script/cron

Arquivo 
/var/www/html/Brapci2.0/script/cron

Conteúdo:
curl "https://brapci.inf.br/index.php/roboti/cron/" --max-time 60

*/
class robotis extends CI_Model  
    {
        var $version = '0.20.12.18';
        var $base = 'brapci_bots.';

        function __construct()
            {
                    $this->load->model("frbr");
                    $this->load->model("frbr_core");
                    $this->load->model("oai_pmh");                 
                    $this->load->model("sources");                    
            }

        function cron($path,$id)
            {
                $data = $this->serviceCron(1);                
                $data['cron'] = $this->version;
                $data['command'] = 'nextOAI';
                $data['id'] = $id;
                $this->cron_execute($data);
            }
        function log($s)
            {
                $data = date("Y-m-d H:i:s");
                $sql = "insert into ".$this->base."cron_logs
                            (log_type,log_data)
                            value
                            ('$s','$data')";
                $rlt = $this->db->query($sql);
                return(1);
            }

        function cron_execute($data)
            {
                if (!isset($data['cron_exec']))
                    {                        
                        echo "ERRO";
                        exit;
                    }
                $type = $data['cron_exec'];
                $cmd = $data['cron_cmd'];
                switch($type)
                    {
                        case 'php':
                            $data = eval($cmd);
                            break;
                        default:
                            echo 'OPS, metodo não existe - '.$type;
                            
                            exit;
                    }
                //echo $this->json($data);
            }
        
        function serviceCron($id)
            {
                $sql = "select * from ".$this->base."cron ";
                if ($id > 0)
                    {
                        $sql .= " where id_cron = ".round($id);
                    }
                $sql .= " order by cron_prior";
                
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                if (count($rlt) > 0)
                    {
                        $line = $rlt[0];
                    } else {
                        $line = array();
                    }
                $this->log($line['cron_acron']);
                return($line);
            }
        function index($v,$c,$i)
            {
                switch($v)
                    {
                        case 'cron':
                            $this->cron($c,$i);
                            break;
                        case 'oai':
                            $this->oai($c,$i);
                            break;
                        case 'help':
                            $this->help_cmd();
                            break;                        
                        case 'status':
                            $this->status_json();
                            break;
                        default:
                            echo $this->robotis->verb_not_exists();
                    }
            }
        function log_last_update()
            {
                $sql = "select * from ".$this->base."cron_logs 
                        order by id_log desc
                        limit 1";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $sx = '<div class="col-md-12"><tt>';
                $sx .= 'Server now: '.date("d/m/Y H:m:s");
                $sx .= '<br/>';
                if (count($rlt) > 0)
                    {
                        $line = $rlt[0];
                        $sx .= 'Last update: '.stodbr($line['log_data']).' '.substr($line['log_data'],10,6);
                    } else {
                        $sx .= 'No logs';
                    }
                $sx .= '</tt></div>';
                return($sx);

            }

        function oai_next()
            {
                $dt = $this->oai_pmh->NextHarvesting();
                $data = $this->oai_ListIdentifiers($dt['id_jnl']);
                echo $this->json($data);                  
            }

        function oai_GetRecord()
            {
                $dt = array();
                $id = 0;
                $idc = $this -> oai_pmh -> getRecord(0);
                if ($idc > 0) 
                {
                    $dt = $this -> oai_pmh -> getRecord_oai_dc($idc, $dt);
                    $dt['idc'] = $idc;
                    $src = $this -> sources -> info($id);
                    $rcn = $this -> oai_pmh -> process($dt);
                }
                $data = array();
                $data['source'] = $id;
                $data['rcn'] = $rcn;
                echo $this->json($data); 
            }

        function oai_ListIdentifiers($id)
            {                
                $data = $this->oai_pmh->ListIdentifiers($id);
                return($data);
            }
        function oai($c,$i)
            {
                $this->load->model('oai_pmh');
                $this->load->model('sources');
                $this->load->model('frbr_core');
                
                switch($c)
                    {
                        case 'next':
                            $this->oai_next();
                        break; 

                        default:
                        
                        $data = array(
                            'oai-pmh'=>'OAI-PMH',
                            'vesrion'=>$this->oai_pmh->version(),
                        );
                        echo $this->json($data);                        
                    }
            }
        function navegador() {
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                if (strlen($user_agent) > 15)
                    {
                        return 1;
                    } else {
                        return 0;
                    }
                return 0;
            }            
        function help_cmd()
            {
                $web = $this->navegador();
                $cmd = array();
                $cmd['help'] = msg('This verb with Roboti verbs');
                $cmd['status'] = msg('Verify state of server');
                $cmd['cron'] = msg('Execute schedule');
                $cmd['oai'] = msg('OAI-PMH harvesting');

                if ($web) echo $this->logo();
                if ($web) echo '<pre>';
                echo '==Verb list=='.cr();
                foreach($cmd as $cmds => $desc)
                    {
                        if ($web) echo chr(9);
                        echo $cmds.chr(9).$desc.cr();
                    }
                if ($web) echo '</pre>';
            }

        function json($data=array())
            {
                header('Content-Type: application/json');
                return json_encode($data);
            }

        function status_json()
            {
                
                $data = array('status'=>'active');
                echo $this->json($data);
            }
        function verb_not_exists()
            {
                $data = array('verb'=>'not informed');
                echo $this->jason($data);
            }            
        function no_service()
            {
                $data = array('service'=>'500','error'=>'not informed');
                echo $this->json($data);	
            }            

        function version($container=0)
            {
                $sx = '';
                if ($container==1)
                    {
                        $sx = '<div class="col-md-12 text-right">';
                        $sx .= '<span class="red">';
                    }
                    $sx .= msg('version').': '.$this->version;
                if ($container == 1)
                    {
                        $sx .= '</span>';
                        $sx .= '</div>'.cr();
                    }
                return($sx);
            }
        function logo()
            {
                $sx = '<div class="col-md-12">';
                $sx .= '<a href="'.base_url(PATH).'">';
                $sx .= '<img src="'.base_url('img/logo/roboti.png').'">';
                $sx .= '</a>';
                $sx .= '</div>'.cr();    
                return($sx);
            }            
        function help()
            {
                $sx = '<div class="col-md-12">';
                $sx .= '<tt>No verb found, use <a href="'.base_url(PATH.'verb/help').'">help</a></tt>';
                $sx .= '</div>';
                return($sx);
            }
        function status_html()
            {
                
                $sx .= '<div class="container"><div class="row" style="margin-top:50px;">';
                $sx .= '<div class="col-md-12"><h4>Robots Status</h4></div>';
                $sx .=  $this->schedule->cron_status('html');
                $sx .= '</div>';
                $sx .= '</div>';
                return($sx);
            }
    }