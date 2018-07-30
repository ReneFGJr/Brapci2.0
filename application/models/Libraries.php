<?php
class libraries extends CI_model
	{
	function show()
		{
			$data = array();
			$this->load->view('brapci/library',$data);
		}	
	}
?>	
