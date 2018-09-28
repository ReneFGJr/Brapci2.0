<?php
class B extends CI_model
	{
	function mark($key,$vlr='0')
		{
			$_SESSION['m'.$key] = $vlr;
			return($vlr);
		}
	function checkbox($key)
		{
			global $jss;
			$sx = '';
			
			if (!isset($jss))
				{
					$sx .= '
					<script>
						function mark(ms,ta)
							{
								alert("Consulta");
								var ok = ta.checked;
								$.ajax({
		  							type: "POST",
		  							url: "'.base_url(PATH.'mark/').'"+ms,
		  							data: { dd1: ms, dd2: ok }
								}).done(function( data ) {
									$("#basket").html(data);
								});						
							}					
					</script>
					';
				}
			if (strlen($key) > 0)
				{
					$id = 'm'.$key;
					if (isset($_SESSION[$id]))
						{
							if ($_SESSION[$id] == '1')
								{
									$chk = 'checked';		
								} else {
									$chk = '';
								}
							
						} else {
							$chk = '';
						}		
					$sx .= '<input type="checkbox" id="chk'.$key.'" onchange="mark('.$key.',this);"> '.$key.' - ';
				}
			return($sx);
		}	
	}
?>
