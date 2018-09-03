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
$lang['search_1'] = 'todos';
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
$lang['hasIssueOf'] = 'Edição';
$lang['hasSubject'] = 'Tema';
$lang['hasIssue'] = 'Publicação';
$lang['hasISSN'] = 'ISSN';
$lang['hasIdRegister'] = 'IDs';  
$lang['hasUrl'] = 'Link de acesso';
$lang['hasTitle'] = 'Título';
$lang['hasRegisterId'] = 'Identificador';
$lang['dateOfAvailability'] = 'Disponibilizado';
$lang['hasSectionOf'] = 'Sessão';
$lang['isPubishIn'] = 'Nome da Pulicação';
$lang['hasSource'] = 'Source';
$lang['altLabel'] = 'Nome alternativo';
$lang['dateOfPublication'] = 'Data da publicação';
$lang['hasVolumeNumber'] = 'Vol./Num.';
$lang['s_date_hour'] = 'Data/Hora';
$lang['s_query'] = 'Consulta';
$lang['s_type'] = 'Tipo';
$lang['s_result'] = 'Total';


$lang['cache_status_1'] = '<span style="color: #008F00"><b>Para coletar</b></span>';
$lang['cache_status_2'] = '<span style="color: #8080FF"><b>Em processamento</b></span>';
$lang['cache_status_3'] = '<span style="color: #000000">Processado</span>';
$lang['cache_status_8'] = '<span style="color: #C00000"><b>Erro no PDF</b></span>';
$lang['cache_status_9'] = '<span style="color: #808080">Excluído</span>';


$lang['Search'] = 'Pesquisar';
$lang['Subject'] = 'Índices das Palavras-chave';
$lang['index_sections'] = 'Seções';
$lang['Sections'] = 'Seções indexadas';
$lang['Corporate Body'] = 'Afiliação institucional';
$lang['Authors'] = 'Autores indexados';
$lang['Journal'] = 'Publicações indexadas';
$lang['Words'] = 'Termos indexados';

$lang['about_brapci'] = 'Sobra a Brapci';
$lang['collections'] = 'Coleções Indexadas';
$lang['help'] = 'Ajuda sobre a Brapci';

$lang['edit_source'] = 'Editar';
$lang['return_to_up'] = 'retorna ao topo';


$lang['admin_journals'] = 'Fontes indexadas';
$lang['admin_export'] = 'Exportar dados';

$lang['export_article'] = 'Exportar Artigos';
$lang['export_subject'] = 'Exportar Palavras';
$lang['export_subject_reverse'] = 'Exportar Índice Invertido';
$lang['export_issue'] = 'Exportar Nomes dos Fascículos';

$lang['tools_harvesting'] = 'Ferramenta de coleta';
$lang['admin_tools'] = 'Utilitários de coleta';
$lang['tools_pdf_import'] = 'Importar PDF dos artigos';
$lang['tools_oai_import'] = 'Importar Arquivos OAI';
$lang['tools_pdf_check'] = 'Checagem dos arquivos';
$lang['tools_oai_harvesting'] = 'Coletar de Metadados OAI';

$lang['perfil'] = 'Perfil';
$lang['logout'] = 'Sair';
$lang['SignIn'] = 'Entrar no Sistema';
$lang['password'] = 'senha';
$lang['return'] = 'voltar';
$lang['Not found'] = 'Não localizado';
$lang['how_cite'] = 'Como citar';
$lang['how_sharing'] = 'Compartilhe';
$lang['clean_selected'] = 'Limpar seleção';
$lang['xls_selected'] = 'Exportar CSV';
$lang['select_all'] = 'Selecionar Tudo';
$lang['not_match_to'] = 'Nada localizado para';
$lang['admin_vocabulary'] = 'Vocabulário controlado';
$lang['admin_config'] = 'Configurações';
$lang['config_forms'] = 'Campos do formulário';
$lang['config_class'] = 'Classes e Propriedades';
$lang['config'] = 'Configurações';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';
$lang[''] = '';

?>