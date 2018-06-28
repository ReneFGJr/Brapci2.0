<?php
class libraries extends CI_model
	{
	function show()
		{
			$data = array();
			$sx = $this->load->view('brapci/library',$data);
		}	
	}
?>	
