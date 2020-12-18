<?php
class robotis extends CI_Model  
    {
        var $version = '0.20.12.18';
        function index($v,$c,$i)
            {
                switch($v)
                    {
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

        function oai_ListIdentifiers($id)
            {
                $data = $this->oai_pmh->ListIdentifiers($id);
            }
        function oai($c,$i)
            {
                $this->load->model('oai_pmh');
                $this->load->model('sources');
                $this->load->model('frbr_core');
                
                switch($c)
                    {
                        case 'next':
                            $dt = $this->oai_pmh->NextHarvesting();
                            $data = $this->oai_ListIdentifiers($dt['id_jnl']);
                            $this->json();    
                            echo json_encode($data);
                        break; 

                        default:
                        $this->json();
                        $data = array(
                            'oai-pmh'=>'OAI-PMH',
                            'vesrion'=>$this->oai_pmh->version(),
                        );
                        echo json_encode($data);        
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

        function json()
            {
                header('Content-Type: application/json');
            }

        function status_json()
            {
                $this->json();
                $data = array('status'=>'active');
                echo json_encode($data);
            }
        function verb_not_exists()
            {
                $data = array('verb'=>'not informed');
                header('Content-Type: application/json');
                echo json_encode($data);
            }            
        function no_service()
            {
                $data = array('service'=>'500','error'=>'not informed');
                header('Content-Type: application/json');
                echo json_encode($data);	
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