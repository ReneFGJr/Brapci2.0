<?php 
//<meta name="DC.Contributor.Sponsor" xml:lang="pt" content=""/>
//<meta name="DC.Contributor.Sponsor" xml:lang="en" content=""/>

/********************************************************* Autores ****************/
for ($r=0;$r < count($authores);$r++)
{
	echo '	<meta name="DC.Creator.PersonalName" content="'.$authores[$r].'"/>'.cr();
}
echo '	<meta name="DC.Date.created" scheme="ISO8601" content="'.$disponivel_em.'"/>'.cr();

//<meta name="DC.Date.dateSubmitted" scheme="ISO8601" content="2017-06-09"/>
//<meta name="DC.Date.issued" scheme="ISO8601" content="2018-04-19"/>
//<meta name="DC.Date.modified" scheme="ISO8601" content="2018-04-19"/>
/********************************************************** Resumos **************/
if (isset($abs))
{
	foreach ($abs as $keys => $value) 
	{
		echo '<meta name="DC.Description" xml:lang="'.$keys.'" content="'.$value.'"/>'.cr(); 
	}
}
echo '<meta name="DC.Format" scheme="IMT" content="application/pdf"/>'.cr();
echo '<meta name="DC.Identifier" content="'.$id.'"/>'.cr();
echo '	<meta name="DC.Identifier.pageNumber" content="'.$pg_first.'-'.$pg_last.'"/>'.cr();
echo '	<meta name="DC.Identifier.DOI" content="'.$doi.'"/>'.cr();
//<meta name="DC.Identifier.URI" content="http://seer.ufrgs.br/EmQuestao/article/view/74064"/>
echo '	<meta name="DC.Language" scheme="ISO639-1" content="pt"/>'.cr();
echo '	<meta name="DC.Rights" content="Direitos autorais reservados a '.$journal.'" />'.cr();
echo '	<meta name="DC.Rights" content=""/>'.cr();
echo '	<meta name="DC.Source" content="'.$journal.'"/>'.cr();
//<meta name="DC.Source.ISSN" content="1808-5245"/>
//<meta name="DC.Source.Issue" content="2"/>
//<meta name="DC.Source.URI" content="http://seer.ufrgs.br/EmQuestao"/>
//<meta name="DC.Source.Volume" content="24"/>

/******************************* KEYWORDS **********************************/
$keys = ($key);
foreach ($keys as $keyx => $value) {
	$vlrs = splitx('.',strip_tags($value).'.');
	for ($r=0;$r < count($vlrs);$r++)
	{
		echo '	<meta name="DC.Subject" xml:lang="'.$keyx.'" content="'.$vlrs[$r].'"/>'.cr();	
	}	
}

/******************************** TITULOS **********************************************/
echo '	<meta name="DC.Title" content="'.$title.'"/>'.cr();
for ($r = 0; $r < count($tit); $r++) {
	echo '	<meta name="DC.Title.Alternative" xml:lang="en" content="'.$tit[$r].'"/>' . cr();
}
?>
<meta name="DC.Type" content="Text.Serial.Journal"/>
<meta name="DC.Type.articleType" content="Artigos"/>
<meta name="gs_meta_revision" content="1.1" />
<meta name="citation_journal_title" content="<?php echo $source;?>"/>
<?php 
//<meta name="citation_issn" content="1808-5245"/>
for ($r=0;$r < count($authores);$r++)
{
	echo '	<meta name="citation_author" content="'.$authores[$r].'"/>'.cr();
}

echo '	<meta name="citation_title" content="'.$title.'"/>'.cr();
echo '<meta name="citation_date" content="2018/04/19"/>'.cr();
echo '<meta name="citation_volume" content="24"/>'.cr();
echo '<meta name="citation_issue" content="2"/>'.cr();
if (strlen($pg_first) > 0) 	{ 	echo '	<meta name="citation_firstpage" content="'.$pg_first.'"/>'.cr(); }
if (strlen($pg_last) > 0) 	{ 	echo '	<meta name="citation_lastpage" content="'.$pg_last.'"/>'.cr(); }
if (strlen($doi) > 0) 		{ 	echo '	<meta name="citation_doi" content="'.$doi.'"/>'.cr(); }
//echo '<meta name="citation_abstract_html_url" content="'..'">'.cr();
echo '<meta name="citation_language" content="pt"/>'.cr();
foreach ($keys as $keyx => $value) {
	$vlrs = splitx('.',strip_tags($value).'.');
	for ($r=0;$r < count($vlrs);$r++)
	{
		echo '	<meta name="citation_keywords" xml:lang="'.$keyx.'" content="'.$vlrs[$r].'"/>'.cr();	
	}	
}
//<meta name="citation_pdf_url" content="http://seer.ufrgs.br/EmQuestao/article/download/74064/45895"/>
?>