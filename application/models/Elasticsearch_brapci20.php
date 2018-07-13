<?php
class Elasticsearch_brapci20 extends CI_model
	{
		function query($q='')
			{
				$rlt = $this-> Elasticsearch -> query_all($q);
				print_r($rlt);
			}
		function journals_index()
			{
				$sql = "select * from source_source order by id_jnl";
				$rlt = $this->db->query($sql);
				$rlt = $rlt->result_array();
				echo '<pre>';
				for ($r=0;$r < count($rlt);$r++)
					{
						$data = $rlt[$r];
						$id = $data['id_jnl'];
						print_r($this->Elasticsearch->add('journals',$id,$data));
					}
				echo '</pre>';
			}
	}
?>