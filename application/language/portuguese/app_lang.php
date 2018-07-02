<?php
// This file is part of the Brapci Software. 
// 
// Copyright 2015, UFPR. All rights reserved. You can redistribute it and/or modify
// Brapci under the terms of the Brapci License as published by UFPR, which
// restricts commercial use of the Software. 
// 
// Brapci is distributed in the hope that it will be useful, but WITHOUT ANY
// WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
// PARTICULAR PURPOSE. See the ProEthos License for more details. 
// 
// You should have received a copy of the Brapci License along with the Brapci
// Software. If not, see
// https://github.com/ReneFGJ/Brapci/tree/master//LICENSE.txt 
/* @author: Rene Faustino Gabriel Junior <renefgj@gmail.com>
 * @date: 2015-12-01
 */
if (!function_exists(('msg')))
	{
		function msg($t)
			{
				$CI = &get_instance();
				if (strlen($CI->lang->line($t)) > 0)
					{
						return($CI->lang->line($t));
					} else {
						return($t);
					}
			}
	}



/* Cited */
$lang['current'] = 'atual';
$lang['Validity'] = 'Vigência';

$lang['about'] = 'sobre';
$lang['home'] = 'home';
$lang['tools'] = 'ferramentas';
$lang['signin'] = 'login';
$lang['search_term'] = 'informe o(s) termo(s) de busca';
$lang['search_1'] = 'assuntos';
$lang['search_2'] = 'autores';
$lang['search_3'] = 'título';
$lang['search_4'] = 'palavras-chave';
$lang['search_5'] = 'resumo';
$lang['search_6'] = 'referências';
$lang['indexs'] = 'índices';

$lang['propriety'] = 'propriedade';
$lang['value'] = 'valor';
$lang['affiliatedWith'] = 'Afiliação';
$lang['prefLabel'] = 'Descritor';
$lang['hasAuthor'] = 'Autor';
$lang['hasEmail'] = 'e-mail';
//$lang['hasIssueOf'] = 'Artigo';
$lang['hasSubject'] = 'Tema';  

$lang['cache_status_1'] = 'Para coletar';
$lang['cache_status_2'] = 'Em processamento';
$lang['cache_status_3'] = 'Processado';
$lang['cache_status_9'] = 'Excluído';


$lang['Search'] = 'Pesquisar';


$lang['admin_journals'] = 'Fontes indexadas';
$lang['admin_export'] = 'Exportar dados';
$lang['export_article'] = 'Exportar Artigos';
$lang['export_subject'] = 'Exportar Palavras';
$lang['export_subject_reverse'] = 'Exportar Índice Invertido';
?>