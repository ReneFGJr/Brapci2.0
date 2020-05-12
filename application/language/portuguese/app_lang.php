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
#if (!function_exists(('msg')))
	{
		function msg($t)
			{
				//return($t.'====');
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
$lang['search_1'] = 'título, palavra-chave e resumo';
$lang['search_2'] = 'autores';
$lang['search_3'] = 'título';
$lang['search_4'] = 'palavras-chave';
$lang['search_5'] = 'resumo';
if (date("Ym") < 202006)
{
	$lang['search_6'] = 'texto completo <span style="color: orange"><b><sup>novo</sup></b></span>';
} else {
	$lang['search_6'] = 'texto completo';
}
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
$lang['hasTitleAlternative'] = 'Título alternativo';
$lang['hasRegisterId'] = 'Identificador';
$lang['dateOfAvailability'] = 'Disponibilizado';
$lang['hasSectionOf'] = 'Sessão';
$lang['isPubishIn'] = 'Nome da Pulicação';
$lang['hasSource'] = 'Source';
$lang['altLabel'] = 'Nome alternativo';
$lang['dateOfPublication'] = 'Data da publicação';
$lang['hasPublicationVolume'] = 'Vol.';
$lang['hasPublicationNumber'] = 'Núm.';
$lang['s_date_hour'] = 'Data/Hora';
$lang['s_query'] = 'Consulta';
$lang['s_type'] = 'Tipo';
$lang['s_result'] = 'Total';
$lang['submit'] = 'Enviar/salvar';


$lang['cache_status_1'] = '<span style="color: #008F00"><b>Para coletar</b></span>';
$lang['cache_status_2'] = '<span style="color: #8080FF"><b>Em processamento</b></span>';
$lang['cache_status_3'] = '<span style="color: #000000">Processado</span>';
$lang['cache_status_8'] = '<span style="color: #C00000"><b>Erro no PDF</b></span>';
$lang['cache_status_9'] = '<span style="color: #808080">Excluído</span>';


$lang['Search'] = 'Pesquisar';
$lang['Subject'] = 'Índices das Palavras-chave';
$lang['index_sections'] = 'Seções';
$lang['Sections'] = 'Seções indexadas';
$lang['CorporateBody'] = 'Afiliação institucional';
$lang['Authors'] = 'Autores indexados';
$lang['Journal'] = 'Publicações indexadas';
$lang['Words'] = 'Termos indexados';

$lang['about_brapci'] = 'Sobra a Brapci';
$lang['collections'] = 'Coleções Indexadas';
$lang['help'] = 'Ajuda sobre a Brapci';
$lang['our_colletions'] = 'Nossa coleção';
$lang['journal_timeline'] = 'Criação das Publicações - Timeline';

$lang['edit_source'] = 'Editar Fonte';
$lang['edit'] = 'Editar';
$lang['Remissive'] = 'Remissivas';
$lang['return_to_up'] = 'retorna ao topo';

$lang['admin_journals'] = 'Fontes indexadas';
$lang['admin_export'] = 'Exportar dados';

$lang['export_article'] = 'Exportar Artigos';
$lang['export_subject'] = 'Exportar Palavras';
$lang['export_index_authors'] = 'Exportar Índice de Autores';
$lang['export_subject_reverse'] = 'Exportar Índice Invertido';
$lang['export_issue'] = 'Exportar Nomes dos Fascículos';
$lang['export_collections_form'] = 'Exportar Coleções (formulário)';

$lang['tools_harvesting'] = 'Ferramenta de coleta';
$lang['admin_tools'] = 'Utilitários de coleta';
$lang['tools_pdf_import'] = 'Importar PDF dos artigos';
$lang['tools_oai_import'] = 'Importar Arquivos OAI';
$lang['tools_pdf_check'] = 'Checagem dos arquivos';
$lang['tools_oai_harvesting'] = 'Coletar de Metadados OAI';
$lang['harvesting_all'] = 'Coletar todas as publicações';
$lang['button_harvesting_status'] = 'Situação das publicações';
$lang['new_source'] = 'Incluir nova publicação';

$lang['signup_user_already_exist'] = 'Usuário já existe';
$lang['Institution'] = 'Instituição';
$lang['fullName'] = 'Nome completo';
$lang['SignUp'] = 'Inscrever-se';
$lang['Sign Up Send'] = 'Enviar inscrição';
$lang['Forgot Password?'] = 'Esqueceu a senha?';
$lang['Don’t have an account?'] = 'Não tem uma conta?';
$lang['login'] = 'acessar';
$lang['Forgot'] = 'Esqueceu a senha?';
$lang['login_recover_password'] = 'recuperar a senha';

$lang['signup_success'] = 'Mensagem enviada';
$lang['signup_success_msg'] = 'verifique sua caixa de entrada de seu e-mail';

$lang['perfil'] = 'Perfil';
$lang['logout'] = 'Sair';
$lang['SignIn'] = 'Entrar no Sistema';
$lang['password'] = 'senha';
$lang['return'] = 'voltar';
$lang['Not found'] = 'Não localizado';
$lang['how_cite'] = 'Como citar';
$lang['how_sharing'] = 'Compartilhe';
$lang['clean_selected'] = 'Limpar seleção';
$lang['xls_selected'] = 'Exportar XLS';
$lang['csv_selected'] = 'Exportar CSV';
$lang['doc_selected'] = 'Exportar DOC';
$lang['select_page'] = 'Selecionar Página';
$lang['select_all'] = 'Selecionar Tudo';
$lang['select_all_page'] = 'Selecionar Tudo';
$lang['not_match_to'] = 'Nada localizado para';
$lang['admin_vocabulary'] = 'Vocabulário controlado';
$lang['admin_config'] = 'Configurações';
$lang['config_forms'] = 'Campos do formulário';
$lang['config_class'] = 'Classes e Propriedades';
$lang['config'] = 'Configurações';
$lang['alternativeNames'] = 'Nomes alternativos';
$lang['No_changes'] = 'Sem alterações';
$lang['hasSectionIndex'] = 'Sessão indexada';
$lang['basket_empty'] = 'Seleção vazia';
$lang['config_email'] = 'Configurações de e-mail';
$lang['Dear'] = 'Prezado(a),';
$lang['change_new_password'] = 'Enviamos um link para você poder alterar sua senha, caso não tenha solicitado, ignore esse e-mail.';
$lang['limits'] = 'Delimitação';
$lang['search_delimitation'] = 'Delimitação da busca';
$lang['Collection'] = 'Coleções';
$lang['total_subject'] = 'Total de';
$lang['registers'] = 'registros';
$lang['Authorities'] = 'Autoridades';
$lang['Journals'] = 'Publicações';
$lang['collection_all'] = 'Revistas Latino Americanas e Eventos';
$lang['select_collection'] = 'Selecionar';
$lang['save_selected'] = 'Salva seleção';
$lang['basket_saved'] = 'Seleção Salva';
$lang['basket_inport'] = 'Importar seleção';
$lang['IDs_info'] = 'Insira a lista de ID dos trabalhos, pode ser inserido um por linha ou separados por ponto e virgula.';
$lang['bb_public'] = 'Deixar lista para acesso público';
$lang['bb_success'] = 'Lista salva com sucesso';
$lang['basket_saved'] = 'Listas de bibliografias salvas';
$lang['saved_in'] = 'atualizado em';
$lang[''] = '';
$lang['References'] = 'Referências';

/* MESES */
$lang['mes_01'] = 'jan.';
$lang['mes_02'] = 'fev.';
$lang['mes_03'] = 'mar.';
$lang['mes_04'] = 'abr.';
$lang['mes_05'] = 'maio';
$lang['mes_06'] = 'jun.';
$lang['mes_07'] = 'jul.';
$lang['mes_08'] = 'ago.';
$lang['mes_09'] = 'set.';
$lang['mes_10'] = 'out.';
$lang['mes_11'] = 'nov.';
$lang['mes_12'] = 'dez.';

$lang['bibliometric_menu'] = 'Utilitários Bibliométricos e Tratamento de Texto';
$lang['semicolon_to_list'] = 'Troca ponto e virgula (;) por Enter (CR)';
$lang['csv_to_net'] = 'Converte .CSV para .NET (Pajek)';
$lang['csv_to_matrix'] = 'Converte .CSV para Matriz de Correlação';

$lang['bibliometric_tools'] = 'Ferramentas bibliometricas';
$lang['hasAuthoresInfo'] = 'Separe um autor por linha, ou utilize o "ponto e virgula"';
$lang['Next_events'] = 'Próximos eventos';
$lang['event_all'] = 'todos os eventos';
$lang['register_our_event'] = 'registrar seu evento';
$lang['event'] = 'Área de eventos';

$lang['cache_status'] = 'Situação dos trabalhos';
$lang['genere'] = 'Genêro';
$lang['Selected'] = 'Seleção';
$lang['change_to'] = 'Muda strings DE/PARA';
$lang['remove_tags'] = "Remove Tags/Html";
$lang['Genere'] = "Indicadores de Gênero do autores";

$lang['validity'] = 'Vigência';
$lang['not_informed'] = 'não informado';
$lang['event_site'] = 'Site do Evento';

$lang['Indiceadores'] = "Indicadores";

$lang['collection_EV'] = 'Eventos';
$lang['collection_JA'] = 'Revistas Brasileiras';
$lang['collection_JE'] = 'Revistas Internacionais';
$lang['config_event'] = 'Eventos Científicos';
$lang['csv_to_matrix_ocorrencia'] = "Converte .CSV para Matriz de Correlação única";		
$lang['change_password'] = 'Mudar senha';



?>