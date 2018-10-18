<?php
$edit_link1 = '<img src="' . base_url('img/icone_edit.gif') . '" height="16" id="titles">';
$edit_link2 = '<img src="' . base_url('img/icone_edit.gif') . '" height="16" id="authors">';
$edit_link4 = '<img src="' . base_url('img/icone_edit.gif') . '" height="16" id="abstract1">';
$edit_link5 = '<img src="' . base_url('img/icone_edit.gif') . '" height="16" id="abstract2">';
$edit_link6 = '<img src="' . base_url('img/icone_edit.gif') . '" height="16" id="issue">';

/* Authors */
$authors = array('');
$authores_row = troca($authores_row, chr(13), ';');
$authors = splitx(';', $authores_row);
?>
	<div class="row">
		<div class="col-md-12">
			<table class="table" width="100%">
				<tr valign="top">
					<th width="40">field</th>
					<th width="20">##</th>
					<th>content</th>
				</tr>
				
				<!---- 022 --->
				<tr valign="top">
					<td align="center">
						<tt>022</tt>
					</td>
					<td>
						<tt>##</tt>
					</td>
					<td>
						<tt>
						<?php
						echo '|a ' . $jnl_issn_impresso;
						echo ' |l ' . $jnl_issn_impresso;
						?>							
						</tt>
					</td>
				</tr>	
				
				<!---- 022 --->
				<?php if (strlen($jnl_issn_eletronico) > 0) { ?>
				<tr valign="top">
					<td align="center">
						<tt>022</tt>
					</td>
					<td>
						<tt>##</tt>
					</td>
					<td>
						<tt>
						<?php
						echo '|a ' . $jnl_issn_eletronico;
						echo ' |l ' . $jnl_issn_impresso;
						?>							
						</tt>
					</td>
				</tr>							
				<?php } ?>
				<!---- 100 --->
				<?php
				if (count($authors) > 0)
				{ ?>
				<tr valign="top">
					<td align="center">
						<tt>100</tt>
					</td>
					<td>
						<tt>1_</tt>
					</td>
					<td>
						<tt><?php echo '|a ' . $authors[0]; ?></tt>
					</td>
				</tr>
				<?php }?>
				<!---- 245 --->
				<tr valign="top">
					<td align="center">
						<tt>245</tt>
					</td>
					<td>
						<tt>10</tt>
					</td>
					<td>
						<tt><?php echo '|a ' . $ar_titulo_1; ?> |6 <?php echo msg('idioma') . ' ' . msg($ar_idioma_1); ?></tt>
					</td>
				</tr>
				
				<!---- 246 --->
				<?php if (strlen($ar_titulo_2) > 0) { ?>
				<tr valign="top">
					<td align="center">
						<tt>246</tt>
					</td>
					<td>
						<tt>10</tt>
					</td>
					<td>
						<tt><?php echo $ar_titulo_2; ?> |6 <?php echo msg('idioma') . ' ' . msg($ar_idioma_2); ?></tt>
					</td>
				</tr>
				<?php } ?>
				
				<!---- 300 --->
				<tr valign="top">
					<td align="center">
						<tt>300</tt>
					</td>
					<td>
						<tt>10</tt>
					</td>
					<td>
						<tt><?php echo '|a v. ' . $ed_vol . ', n. ' . $ed_nr . ', ' . $ed_ano . $pages; ?></tt>
					</td>
				</tr>
				
				<!---- 520 --->
				<tr valign="top">
					<td align="center">
						<tt>520</tt>
					</td>
					<td>
						<tt>3#</tt>
					</td>
					<td>
						<tt>|a <?php echo $ar_resumo_1; ?> |6 <?php echo msg('idioma') . ' ' . msg($ar_idioma_1); ?></tt>
					</td>
				</tr>
				<?php if (strlen($ar_idioma_2) > 0) { ?>
				<tr valign="top">
					<td align="center">
						<tt>520</tt>
					</td>
					<td>
						<tt>3#</tt>
					</td>
					<td>
						<tt>|a <?php echo $ar_resumo_2; ?> |6 <?php echo msg('idioma') . ' ' . msg($ar_idioma_2); ?></tt>
					</td>
				</tr>
				<?php } ?>
				<!---- 650 --->
				<?php for ($r=1;$r < count($keywords);$r++) {
					$line = $keywords[$r];
					$kw_word = $line['kw_word'];
					$kw_keyword = $line['kw_keyword'];
					$kw_idioma = $line['kw_idioma'];
					
					$link = '[ <a href="'.base_url('index.php/v/t/'.$kw_keyword).'">link</a> ]';
					?>
				<tr valign="top">
					<td align="center">
						<tt>650</tt>
					</td>
					<td>
						<tt>1_</tt>
					</td>
					<td>
						<tt>|a <?php echo $kw_word; ?> |9 <?php echo $kw_idioma; ?> |6 <?php echo $link; ?></tt>
					</td>
				</tr>
				<?php } ?>
				
				<!---- 700 --->
				<?php for ($r=1;$r < count($authors);$r++) { ?>
				<tr valign="top">
					<td align="center">
						<tt>700</tt>
					</td>
					<td>
						<tt>1_</tt>
					</td>
					<td>
						<tt><?php echo '|a ' . $authors[$r]; ?></tt>
					</td>
				</tr>
				<?php } ?>
				
				<!---- 773 --->
				<tr valign="top">
					<td align="center">
						<tt>773</tt>
					</td>
					<td>
						<tt>0#</tt>
					</td>
					<td>
						<tt><?php echo '|a ' . $cidade_nome . ' |t ' . $jnl_nome . ' |x ' . $jnl_issn_impresso; ?></tt>
					</td>
				</tr>
				
				<!---- 856 --->
				<?php for ($r=0;$r < count($links);$r++) { ?>
				<tr valign="top">
					<td align="center">
						<tt>856</tt>
					</td>
					<td>
						<tt>4#</tt>
					</td>
					<td>
						<tt>
						<?php
						$line = $links[$r];
						$url = $links[$r]['bs_adress'];
						$tipo = $links[$r]['bs_type'];
						if (substr($url, 0, 4) == 'http') {
							$link = $url;
							$url = '|u ' . '<a href="' . $link . '" target="_black">' . $link . '</a>';
						} else {
							switch($tipo) {
								case 'DOI' :
									$link = 'http://dx.doi.org/' . $line['bs_adress'];
									$url = '|u ' . '<a href="' . $link . '" target="_black">' . $link . '</a>';
									break;
								case 'PDF' :
									$link = base_url('index.php/main/download/' . $line['id_bs'] . '/' . checkpost_link($line['id_bs']));
									$url = '|u ' . '<a href="' . $link . '" target="_black">' . $link . '</a>';
									$url = $url . ' |n Brapci, Brazil';
								default :
									$link = base_url('index.php/main/download/' . $line['id_bs'] . '/' . checkpost_link($line['id_bs']));
									$url = '|u ' . '<a href="' . $link . '" target="_black">' . $link . '</a>';
									//$url = $url . ' |n Brapci, Brazil';
									break;
							}
						}
						echo $url;
						?></tt>
					</td>
				</tr>
				<?php } ?>							
			</table>
		</div>
	</div>

