<?php
if ($ar_doi == '<font color="red">empty</font>') { $ar_doi = ''; }
?>
<meta name="description" content="<?php echo $jnl_nome;?>" />
<meta name="keywords"  xml:lang="<?php echo $ar_idioma_1;?>" content="<?php echo $ar_keyw_1;?>" />
<meta name="keywords"  xml:lang="<?php echo $ar_idioma_2;?>" content="<?php echo $ar_keyw_2;?>" />

<link rel="icon" href="http://www.brapci.inf.br/Favicon_pt_BR.ico" type="image/x-icon" />

<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<meta name="DC.Contributor.Sponsor" xml:lang="pt" content=""/>
<meta name="DC.Coverage" xml:lang="pt" content=""/>
<meta name="DC.Coverage.spatial" xml:lang="pt" content=""/>
<meta name="DC.Coverage.temporal" xml:lang="pt" content=""/>
<?php for ($r=0;$r < count($authors);$r++) { ?>
<meta name="DC.Creator.PersonalName" content="<?php echo nbr_autor($authors[$r]['autor_nome'],7);?>"/>
<?php } ?>
<meta name="DC.Date.created" scheme="ISO8601" content="<?php echo substr($ed_data_cadastro,0,4).'-'.substr($ed_data_cadastro,4,2).'-'.substr($ed_data_cadastro,6,2);?>"/>
<meta name="DC.Date.dateSubmitted" scheme="ISO8601" content="<?php echo substr($ed_data_cadastro,0,4).'-'.substr($ed_data_cadastro,4,2).'-'.substr($ed_data_cadastro,6,2);?>"/>
<meta name="DC.Date.issued" scheme="ISO8601" content="<?php echo substr($ed_data_cadastro,0,4).'-'.substr($ed_data_cadastro,4,2).'-'.substr($ed_data_cadastro,6,2);?>"/>
<meta name="DC.Date.modified" scheme="ISO8601" content="<?php echo substr($ed_data_cadastro,0,4).'-'.substr($ed_data_cadastro,4,2).'-'.substr($ed_data_cadastro,6,2);?>"/>
<meta name="DC.Description"  xml:lang="<?php echo $ar_idioma_1;?>" content="<?php echo $ar_resumo_1;?>" />
<meta name="DC.Description"  xml:lang="<?php echo $ar_idioma_2;?>" content="<?php echo $ar_resumo_2;?>" />
<meta name="DC.Format" scheme="IMT" content="application/pdf"/>
<meta name="DC.Identifier" content="<?php echo $id_ar;?>"/>
<meta name="DC.Identifier.DOI" content="<?php echo $ar_doi;?>"/>
<meta name="DC.Identifier.URI" content="<?php echo base_url('index.php/v/a/'.$id_ar);?>"/>
<meta name="DC.Language" scheme="ISO639-1" content="<?php echo $ar_idioma_1;?>"/>
<meta name="DC.Rights" content="Direitos autorais <?php echo date("Y");?> Reserved to Authors" />
<meta name="DC.Rights" content="http://creativecommons.org/licenses/by-nc-sa/4.0"/>
<meta name="DC.Source" content="<?php echo $jnl_nome;?>"/>
<meta name="DC.Source.ISSN" content="<?php echo $jnl_issn_impresso;?>"/>
<meta name="DC.Source.Issue" content="<?php echo $ed_nr;?>"/>
<meta name="DC.Source.URI" content="<?php echo base_url('index.php/v/a/'.$id_ar);?>"/>
<meta name="DC.Source.Volume" content="<?php echo $ed_vol;?>"/>
<?php for($r=0;$r < count($keywords);$r++) { ?>
<meta name="DC.Subject" xml:lang="<?php echo $keywords[$r]['kw_idioma'];?>" content="<?php echo $keywords[$r]['kw_word'];?>"/>
<?php } ?>
<meta name="DC.Title" content="<?php echo $jnl_nome;?>"/>
<meta name="DC.Type" content="Text.Serial.Journal"/>
<meta name="DC.Type.articleType" content="Artigos"/>
<meta name="gs_meta_revision" content="1.1" />

<meta name="prism.volume" content="<?php echo $ed_vol;?>">
<meta name="prism.number" content="<?php echo $ed_nr;?>">
<meta name="prism.startingPage" content="<?php echo $ar_pg_inicial;?>">
<meta name="prism.endingPage" content="<?php echo $ar_pg_inicial;?>">
<meta name="prism.publicationName" content="<?php echo $jnl_nome;?>">
<meta name="prism.issn" content="<?php echo $jnl_issn_impresso;?>">
<meta name="prism.publicationDate" content="<?php echo $ed_ano;?>">
<meta name="prism.doi" content="<?php echo $ar_doi;?>">


<meta name="citation_journal_title" content="<?php echo $jnl_nome_abrev;?>"/>
<meta name="citation_issn" content="<?php echo $jnl_issn_impresso;?>"/>
<?php for ($r=0;$r < count($authors);$r++) { ?>
<meta name="citation_author" content="<?php echo nbr_autor($authors[$r]['autor_nome'],7);?>"/>
<meta name="citation_author_institution" content="<?php echo $authors[$r]['autor_instituicao'];?>"/>
<?php } ?>
<?php
if ($ar_pg_inicial > 0) { echo '<meta name="citation_firstpage" content="'.$ar_pg_inicial.'">'.cr(); }
if ($ar_pg_final > 0) { echo '<meta name="citation_firstpage" content="'.$ar_pg_final.'">'.cr(); }
?>
<meta name="citation_lastpage" content="1001">
<meta name="citation_title" content="<?php echo $ar_titulo_1;?>"/>
<meta name="citation_date" content="<?php echo substr($ed_data_cadastro,0,4).'/'.substr($ed_data_cadastro,4,2).'/'.substr($ed_data_cadastro,6,2);?>"/>
<meta name="citation_volume" content="<?php echo $ed_vol;?>"/>
<meta name="citation_issue" content="<?php echo $ed_nr;?>"/>
<meta name="citation_doi" content="<?php echo $ar_doi;?>"/>
<meta name="citation_public_url" content="<?php echo base_url('index.php/v/a/'.$id_ar);?>" />
<meta name="citation_abstract_pdf_url" content="<?php echo base_url('index.php/v/a/'.$id_ar);?>" />
<meta name="citation_abstract_html_url" content="<?php echo base_url('index.php/v/a/'.$id_ar);?>"/>
<meta name="citation_language" content="<?php echo $ar_idioma_1;?>"/>
<?php for($r=0;$r < count($keywords);$r++) { ?>
<meta name="citation_keywords" xml:lang="<?php echo $ar_idioma_1;?>" content="<?php echo $keywords[$r]['kw_word'];?>"/>
<?php } ?>
<meta name="citation_pdf_url" content="<?php echo base_url('index.php/article/download/'.$id_ar);?>"/>
