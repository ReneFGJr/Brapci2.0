<?php 
class clicks extends CI_Model  
    {
        var $table = 'brapci_cited.click';
        var $table_ip = 'brapci_cited.ip';

        function ip_locate($ip)
            {
                $url = 'https://tools.keycdn.com/geo?host='.$ip;
                $url = 'https://tools.keycdn.com/geo.json?host=94.130.9.183';
                $rsp = file_open($url);

            }

        function ip()
            {
                $ip = ip();
                $sql = "select * from ".$this->table_ip." where ip_ip = '$ip' ";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                if (count($rlt) == 0)
                    {
                        $xsql = "insert into ".$this->table_ip." (ip_ip) values ('$ip') ";
                        $rlt = $this->db->query($xsql);
                        sleep(1);
                        $rlt = $this->db->query($sql);
                        $rlt = $rlt->result_array();
                    }
                if (count($rlt) > 0)
                    {
                        return($rlt[0]['i_ip']);
                    } else {
                        return(0);
                    }
                
            }
        function click($id)
            {
                $ip = $this->ip();
                if ($ip > 0)
                {
                $sql = "insert into ".$this->table."
                        (click_id, click_ip)
                        values
                        ($id,'$ip')";
                $rlt = $this->db->query($sql);
                }
            }
    }