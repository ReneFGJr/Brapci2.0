<?php
class BI extends CI_model
{
	function action($ac,$id)
		{
			switch($ac)
				{
					case 'genere':
					$sx = $this->generes($id);
				}
			return($sx);
		}
	function generes()
		{
			$rdf = new rdf;
			$rlt = $rdf->index_count('','Gender');

	        $sx = '<div class="col"><div class="col-12">';
	        $sx .= '<table class="table" style="width: 500px;">';
	        $sx .= '<tr class="text-center"><th>'.msg("genere").'</th>
	        			<th>'.msg('subtotal').'</th>
	        			<th>'.msg('percentual').'</th>
	        			</tr>';
	        $tot = 0;
	        for ($r=0;$r < count($rlt);$r++)
	        {
	        	$line = $rlt[$r];
	        	$tot = $tot + $line['total'];
        	}

	        for ($r=0;$r < count($rlt);$r++)
	        {
	        	$line = $rlt[$r];
	        	$link = '<a href="'.base_url(PATH.'v/'.$line['id']).'">';
	    		$linka = '</a>';
	    		$sx .= '<tr>';
	    		$sx .= '<td class="text-right">';
	        	$sx .= $link.$line['n_name'].$linka;
	        	$sx .= '</td>';
	        	$sx .= '<td class="text-center">';
	        	$sx .= ''.number_format($line['total'],0,',','.');
	        	$sx .= '</td>';
	        	$sx .= '<td class="text-center">';
	        	$sx .= ''.number_format($line['total']/$tot*100,1,',','.').'%';
	        	$sx .= '</td>';
	        	$sx .= '</tr>';	        	
	        }
	        $sx .= '<tr><th class="text-right">'.msg("Total").'</th>
	        				<th class="text-center"><b>'.number_format($tot,0,',','.').'</b></th>
	        				<th class="text-center"><b>100,0%</b></th>
	        				</tr>';
	        $sx .= '</table>';
	        $sx .= '</div>';			
	        $sx .= '</div>';
			return($sx);
		}	
}
?>