<?php
class Bs extends CI_model
	{
	function mark($key)
		{
			$dd2 = get("dd2");
			$key = trim($key);
			if ($dd2 == 'true')
				{
					$vlr = 1;
				} else {
					$vlr = 0;
				}
			$_SESSION['m'.$key] = $vlr;
			
			$s = $_SESSION;
			$tot = 0;
			foreach ($s as $key => $value) {
				if (substr($key,0,1) == 'm')
					{
						if ($value == '1')
							{
								$tot++;
							}	
					}
				return($tot);
			}
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
								/* alert("Consulta"); */
								var ok = ta.checked;
								$.ajax({
		  							type: "POST",
		  							url: "'.base_url(PATH.'mark/').'"+ms,
		  							data: { dd1: ms, dd2: ok }
								}).done(function( data ) {
									$("#basket").html(data);
									/* alert("===>"+data); */
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
					$sx .= '<input type="checkbox" id="chk'.$key.'" onchange="mark('.$key.',this);" '.$chk.'> '.$key.' - ';
				}
			return($sx);
		}	
	}
?>
