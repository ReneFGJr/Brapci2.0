<?php
class Elasticsearch_patent extends CI_model
	{
	    function article_index($d)
            {
                return($d);
            }
		function query($q='')
			{
				$rlt = $this-> Elasticsearch -> query_all($q);
				print_r($rlt);
			}

		function journals_index()
			{
				$sql = "select * from patents order by id_pt";
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();
				echo '<pre>';
				for ($r=0;$r < count($rlt);$r++)
					{
						$data = $rlt[$r];
						$id = $data['id_pt'];
						print_r($this->Elasticsearch->add('patents',$id,$data));
					}
				echo '</pre>';
			}
	}
?>