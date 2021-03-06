<?php
defined("BASEPATH") or exit("No direct script access allowed");

/**
 * CodeIgniter Form Helpers
 *
 * @package     CodeIgniter
 * @subpackage  OAI
 * @category    OAI-PHM
 * @author      Rene F. Gabriel Junior <renefgj@gmail.com>
 * @link        http://www.sisdoc.com.br/CodIgniter
 * @version     v0.21.02.16
 */

class oai_pmh extends CI_model
{
    var $issue;
    var $token = '';

    var $erro = 0;
    var $erro_msg = '';

    var $status = 0;
    var $reg = 0;
    var $new = 0;
    var $del = 0;
    var $ref = 'null';

    var $base = '';

    var $url = '';

    var $version = '0.20.12.18';

    function version()
    {
        return ($this->version);
    }


    function erros($erro)
    {
        switch ($erro) {
            case '404':
                return (msg('oai_page_not_found'));
                break;

            case '510':
                return (msg('oai_no_registers'));
                break;

            case '200':
                return (msg('oai_harvesting_ok'));
        }
        /*
                    404 - Page not found
                    510 - Documento em branco OAI
                    */
    }

    function check_oai_index($id_jnl)
    {
        $n = 'jnl:' . $id_jnl;
        $prop = 'hasIdRegister';
        $idj = $this->frbr_core->find($n, $prop, 1);
        return ($idj);
    }
    function menu($id_jnl = 0)
    {
        $n = 'jnl:' . $id_jnl;
        $idj = $this->check_oai_index($id_jnl);
        $data = $this->sources->le($id_jnl);

        $sx = '';
        $sx .= '<a href="' . base_url(PATH . 'oai/info/' . $id_jnl) . '">' . msg('status') . '</a>';
        $sx .= ' | ';

        if ($idj > 0) {
            $sx .= '';
            $sx .= '<a href="' . base_url(PATH . 'oai/ListIdentifiers/' . $id_jnl) . '">ListIdentifiers</a>';
            $sx .= ' | ';
            if ($data['jnl_scielo'] == '1') {
                $sx .= '<a href="' . base_url(PATH . 'oai/GetRecordScielo/' . $id_jnl) . '">GetRecordScielo</a>';
            } else {
                $sx .= '<a href="' . base_url(PATH . 'oai/GetRecord/' . $id_jnl) . '">GetRecord</a>';
            }
        } else {
            $sx .= '<a href="' . base_url(PATH . 'oai/Identify/' . $id_jnl) . '">Identify</a>';
        }

        return ($sx);
    }

    function le_cache($id)
    {
        $sql = "select * from " . $this->base . "source_listidentifier
                    WHERE id_li = $id";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
        } else {
            $line = array();
        }
        return ($line);
    }

    function indexa_secao($jnl, $sec)
    {
        $sql = "select * from source_setSpec
                                    where st_jnl = $jnl
                                    and st_setspec = '$sec'
                                    ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            return ($rlt[0]['st_index']);
        }
        return (1);
        echo $sql;
        echo '===>' . $jnl;
        echo '===>' . $sec;
        exit;
    }

    function process($dt)
    {
        $this->load->model('searchs');
        $this->load->model('indexer');
        $this->load->model('export');
        $this->load->model('elasticsearch');

        /*********************************** PROCESS **************************************/
        $dt2 = $this->le_cache($dt['idc']);
        $dt3 = $this->sources->le($dt2['li_jnl']);
        $dt = array_merge($dt, $dt2, $dt3);


        /*********************************** ISSUE ****************************************/
        if (isset($dt['issue'])) {
            $ida = $this->frbr->issue($dt, 'Issue');
            $dt['issue']['id'] = $ida;
        } else {
            $issue = sonumero($dt['source'][0]['name']);
            $ida = $this->frbr->issue($dt, 'Issue');
            $dt['issue']['id'] = $ida;
            //print_r($issue);
            echo '==>' . $issue;
            echo '<pre>';
            print_r($dt);
            return ('ERRO DE ISSUE');
        }
        $dt['issue_uri'] = $ida;

        /*********************************** SECTION **************************************/
        /*********************************** VE SE INDEXA SESSAO***************************/
        if (!$this->indexa_secao($dt2['li_jnl'], $dt['issue']['section'])) {
            $this->cache_alter_status($dt['idc'], 9);
            return ("Sessão não indexada");
        }

        /*********************************** ARTICLE **************************************/
        $article_id = $this->frbr->article_create($dt);
        if ($article_id == 0) {
            return ("Trabalho já indexado");
        }

        /*********************************** AUTHORS **************************************/
        if (isset($dt['authors'])) {
            $d = $dt['authors'];
            for ($r = 0; $r < count($d); $r++) {
                $type = $d[$r]['type'];
                if ($type == 'author') {
                    $author = $this->frbr->frad($d[$r], 'Person');
                    $this->frbr_core->set_propriety($article_id, 'hasAuthor', $author, 0);
                }
            }
        } else {
            echo '<pre>';
            print_r($dt);
            echo "OPS";
            exit;
        }
        $link = '<a href="' . base_url(PATH . 'v/' . $article_id) . '" target="_new' . $article_id . '">';
        $this->cache_alter_status($dt['idc'], 3);

        $dt['article_id'] = $article_id;
        $this->indexer->indexing($dt);
        $this->export->export_Article_Single($article_id);
        //$this->Elasticsearch->add('article',$dt['idc'],$dt);

        $this->export->export_Article_Single($article_id);
        $this->elasticsearch->update($article_id);

        $sx = '<div class="col-md-6">';
        $sx .= 'Section: ' . $dt['issue']['section'];
        $sx .= "<h1>Index Article: " . $link . $article_id . '</a></h1><br>' . $this->frbr->msg;
        $sx .= '</div>';
        return ($sx);
    }

    function author($dt)
    {
        $id = $this->frbr->frad($dt);
    }

    function cache_alter_status($id_jnl, $status)
    {
        $sql = "update " . $this->base . "source_listidentifier
                    set li_s = $status
                    WHERE id_li = $id_jnl ";
        $rlt = $this->db->query($sql);
        return (1);
    }

    function leftHarvesting()
    {
        $sql = "select count(*) as total 
                    from " . $this->base . "source_listidentifier 
                    where li_status = 'active' and li_s = 1
                    order by li_s, li_u, id_li
                    limit 1";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $line = $rlt[0]['total'];
        return ($line);
    }

    function getRecord($id_jnl = 0)
    {
        if ($id_jnl > 0) {
            $wh = "li_jnl = '$id_jnl' and ";
        } else {
            $wh = '';
        }

        $sql = "select * from " . $this->base . "source_listidentifier 
                    where $wh li_status = 'active' and li_s = 1
                    order by li_s, li_u, id_li
                    limit 1";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();

        if (count($rlt) == 0) {
            return (0);
        }
        $line = $rlt[0];

        $id = $line['id_li'];
        $this->cache_alter_status($id, 2);

        /* Atualiza total de registros */
        //$this->LisIdentifiesToHarvesting($id_jnl);
        $this->LisIdentifiesToHarvestingAll();
        return ($id);
    }

    function getRecordNlM($id = 0, $dt, $z, $a)
    {
        $this->load->model("sources");

        $sql = "select * from " . $this->base . "source_listidentifier where id_li = $id";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $jnl = $line['li_jnl'];

            $data = $this->sources->le($jnl);
            $url = $this->oai_url($data, 'GetRecordNlm') . $line['li_identifier'];
            $cnt = $this->readfile($url);

            $cnt = troca($cnt, 'abstract-', 'abstract_');
            $cnt = troca($cnt, 'article-', 'article_');
            $cnt = troca($cnt, 'contrib-', 'contrib_');
            $cnt = troca($cnt, 'given-', 'given_');
            $cnt = troca($cnt, 'title-', 'title_');
            $cnt = troca($cnt, 'trans-', 'trans_');
            $cnt = troca($cnt, 'issue-', 'issue_');
            $cnt = troca($cnt, 'self-', 'self_');
            $cnt = troca($cnt, 'subj-', 'subj_');
            $cnt = troca($cnt, 'pub-', 'pub_');
            $cnt = troca($cnt, 'xlink:', '');
            $cnt = troca($cnt, 'xml:', '');

            if (strlen($cnt) > 200) {
                $xml = simplexml_load_string($cnt);
                $erro = (string)$xml->error;
                if (strlen($erro) > 0) {
                    return ($dt);
                }
            } else {
                return ($dt);
            }

            /******************************************************* AUTHOR ********************/
            $is = $xml->GetRecord->record->metadata->article->front->article_meta->contrib_group->contrib;
            $authors = array();
            for ($r = 0; $r < count($is); $r++) {
                $at = $is[$r]->attributes();
                $n1 = UpperCase((string)$is[$r]->name->surname);
                $n2 = (string)$is[$r]->name->given_names;
                $aff = (string)$is[$r]->aff;
                $email = (string)$is[$r]->email;
                $func = (string)$at['contrib_type'];
                $nm = trim($n1) . ', ' . trim($n2);
                $tit = $nm;
                $autho = array('name' => $tit, 'email' => $email, 'aff' => $aff, 'type' => $func);
                array_push($authors, $autho);
            }
            $dt['authors'] = $authors;
            /******************************************************* TITLE **********************/
            $is = $xml->GetRecord->record->metadata->article->front->article_meta->title_group->trans_title;
            $title = array();
            for ($r = 0; $r < count($is); $r++) {
                $at = $is[$r]->attributes();
                $title = (string)$is[$r];
                $lang = $at['lang'];
                //array_push($title, $tit);
            }
            /******************************************************* ABSTRACT *******************/
            $is = $xml->GetRecord->record->metadata->article->front->article_meta->abstract_trans;
            $title = array();
            for ($r = 0; $r < count($is); $r++) {
                $at = $is[$r]->attributes();
                $title = (string)$is[$r]->p;
                $lang = $at['lang'];
                //array_push($title, $tit);
            }
            /******************************************************* DATA ***********************/
            $is = $xml->GetRecord->record->metadata->article->front->article_meta->pub_date;
            $day = (string)$is->day;
            $month = (string)$is->month;
            $year = (string)$is->year;

            $vol = (string)$xml->GetRecord->record->metadata->article->front->article_meta->volume;
            $issue = (string)$xml->GetRecord->record->metadata->article->front->article_meta->issue;

            $issue_id = (string)$xml->GetRecord->record->metadata->article->front->article_meta->issue_id;
            $section = (string)$xml->GetRecord->record->metadata->article->front->article_meta->article_categories->subj_group->subject;
            $iss = array();
            $iss['year'] = $year;
            $iss['month'] = $month;
            $iss['day'] = $day;
            $iss['section'] = $section;
            $iss['issue_id'] = $issue_id;
            $iss['vol'] = $vol;
            $iss['nr'] = $issue;
            $dt['issue'] = $iss;
            /******************************************************* URI ************************/
            $is = $xml->GetRecord->record->metadata->article->front->article_meta->self_uri;
            $uri = array();
            for ($r = 0; $r < count($is); $r++) {
                $at = $is[$r]->attributes();
                $title = (string)$at['href'];
                $lang = (string)$at['content-type'];
                array_push($uri, array('href' => $title, 'type' => $lang));
            }
            $dt['uri'] = $uri;
        }
        return ($dt);
    }

    function getListSets($id = 0, $data = array())
    {
        $tp = 0;
        if (isset($data['jnl_url_oai'])) {
            $tp = 1;
            $url = $data['jnl_url_oai'];
            $url = $this->oai_url($data, 'ListSets');
            echo '<h1>' . $url . '</h1>';
        } else {
            if ($id == 0) {
                return ("");
            }
            $this->load->model("sources");
            $data = $this->sources->le($id);
            $url = $this->oai_url($data, 'ListSets');
        }

        $cnt = $this->readfile($url);
        $cnt = troca($cnt, 'oai_dc:', 'oai_');
        $cnt = troca($cnt, 'dc:', '');
        $cnt = troca($cnt, 'xml:', '');
        $xml = simplexml_load_string($cnt);

        if (!isset($xml->ListSets->set)) {
            return (array());
        }

        if ($tp == 1) {
            return ($xml->ListSets->set);
        }

        $rcn = $xml->ListSets->set;
        for ($r = 0; $r < count($rcn); $r++) {
            $line = $rcn[$r];
            $setSpec = (string)$line->setSpec;
            $setName = (string)$line->setName;

            if (strpos($setName, 'Ã') > 0) {
                $setName = utf8_decode($setName);
            }
            $setName = LowerCase(($setName));
            $setName = convert($setName);
            $setName = ucase($setName);
            $class = 'ArticleSection';
            $id_section = $this->frbr_core->rdf_concept_create($class, $setName);
            $term = $this->frbr_core->frbr_name($setSpec);
            $prop = 'altLabel';
            $this->frbr_core->set_propriety($id_section, $prop, 0, $term);
        }
    }

    function getRecord_oai_dc($id = 0, $dt)
    {
        $this->load->model("sources");
        $sql = "select * from " . $this->base . "source_listidentifier 
                    where id_li = $id";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            $jnl = $line['li_jnl'];

            $data = $this->sources->le($jnl);
            $url = $this->oai_url($data, 'GetRecord') . $line['li_identifier'];

            $cnt = $this->readfile($url);
            $cnt = troca($cnt, 'oai_dc:', 'oai_');
            $cnt = troca($cnt, 'dc:', '');
            $cnt = troca($cnt, 'xml:', '');
            $xml = simplexml_load_string($cnt);

            $rcn = $xml->GetRecord->record->metadata->oai_dc;

            /******************************************************* TITLE **********************/
            $is = $this->xml_values($rcn->title);
            $title = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$rcn->title[$r];
                $lang = '';
                foreach ($rcn->title[$r]->attributes() as $atrib => $value) {
                    if ($atrib == 'lang') {
                        $lang = (string)$value;
                    }
                }
                array_push($title, array('title' => $tit, 'lang' => $lang));
            }
            $dt['title'] = $title;
            /******************************************************* CREATOR *******************/
            $is = $this->xml_values($rcn->creator);
            $author = array();
            $aff = '';
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$rcn->creator[$r];
                if (strpos($tit, ';') > 0) {
                    $aff = trim(substr($tit, strpos($tit, ';') + 1, strlen($tit)));
                    $tit = trim(substr($tit, 0, strpos($tit, ';')));
                }
                $tit = NBR_autor($tit, 1);
                array_push($author, array('name' => $tit, 'type' => 'author', 'aff' => $aff, 'email' => ''));
            }
            $dt['authors'] = $author;
            /******************************************************* SUBJECT *******************/
            $is = $this->xml_values($rcn->subject);
            $key = '';
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$rcn->subject[$r];
                /******************************************************* excpetions ************/
                for ($q = 0; $q <= 9; $q++) {
                    $tit = troca($tit, $q . '.', $q . '¢');
                }
                $tit = troca($tit, '.', ';');
                $tit = troca($tit, '/', ';');
                $tit = troca($tit, ' - ', ';');
                $tit = troca($tit, '.', ';');
                $tit = troca($tit, ',', ';');
                $tit = troca($tit, ':', ';');
                $tit = troca($tit, '–', ';');

                /*******************************/
                $tit = troca($tit, '¢', '.');

                $tit = splitx(';', $tit);
                $lang = '';
                foreach ($rcn->subject[$r]->attributes() as $atrib => $value) {
                    if ($atrib == 'lang') {
                        $lang = $value;
                    }
                }
                for ($z = 0; $z < count($tit); $z++) {
                    $key .= $tit[$z] . '@' . $lang . ';';
                }
            }
            $subject = splitx(';', $key);
            /*************************************************** description ********************/
            $is = $this->xml_values($rcn->description);
            $abstract = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = trim((string)$rcn->description[$r]);
                $lang = '';
                foreach ($rcn->description[$r]->attributes() as $atrib => $value) {
                    if ($atrib == 'lang') {
                        $lang = (string)$value;
                    }
                }
                array_push($abstract, array('descript' => $tit, 'lang' => $lang));
            }
            $dt['abstract'] = $abstract;

            if (!isset($iss['issue'])) {
                /*************************************************** date **************************/
                $data = $this->xml_value($rcn->date);
                $year = '';
                $month = '';
                $day = '';

                //$year = substr($data, 0, 4);
                //$month = substr($data, 4, 2);
                //$day = substr($data, 7, 2);

                /******************************************************* setSpec *******************/
                $rcc = $xml->GetRecord->record->header->setSpec;
                $section = (string)$rcc;

                if (isset($xml->GetRecord->record->metadata->oai_dc->source[0])) {
                    $issues = (string)$xml->GetRecord->record->metadata->oai_dc->source[0];
                    $isz = $this->issue_mount($issues, $xml);
                    $issue_id = 'jnl:' . strzero($line['li_jnl'], 5) . '-' . $isz['year'] . '-' . $isz['vol'] . '-' . $isz['nr'];
                } else {
                    echo "ERRO DE ISSUE";
                }

                $iss['year'] = $isz['year'];
                $iss['month'] = $month;
                $iss['day'] = $day;
                $iss['start_page'] = $isz['pag_start'];
                $iss['end_page'] = $isz['pag_end'];
                $iss['section'] = $section;
                $iss['issue_id'] = $issue_id;
                $iss['vol'] = $isz['vol'];
                $iss['nr'] = $isz['nr'];
                $iss['sourcer'] = $isz['source'];
                $dt['issue'] = $iss;
            }
            /*************************************************** source ************************/
            $is = $this->xml_values($rcn->source);
            $source = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$rcn->source[$r];
                $lang = '';
                foreach ($rcn->source[$r]->attributes() as $atrib => $value) {
                    if ($atrib == 'lang') {
                        $lang = '@' . $value;
                    }
                }
                array_push($source, array('name' => $tit, 'lang' => $lang));
            }
            /*************************************************** relation **********************/
            $is = $this->xml_values($rcn->relation);
            $relation = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$rcn->relation[$r];
                array_push($relation, $tit);
            }
            /*************************************************** relation **********************/
            $is = $this->xml_values($rcn->date);
            $date = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$rcn->date[$r];
                array_push($date, $tit);
            }
            /*************************************************** relation **********************/
            $is = $this->xml_values($rcn->identifier);
            $identifier = array();
            for ($r = 0; $r < count($is); $r++) {
                $tit = (string)$rcn->identifier[$r];
                array_push($identifier, $tit);
            }
            $dt['subject'] = $subject;
            $dt['identifier'] = $identifier;
            $dt['source'] = $source;
            $dt['relation'] = $relation;
            $dt['date'] = $date;
        }
        return ($dt);
    }

    function pagination($n)
    {
        $dt = array();
        while (strpos(' ' . $n, ';')) {
            $n = substr($n, strpos($n, ';') + 1, strlen($n));
        }
        $pagi = '';
        $pagf = '';
        if (strlen($n) > 0) {
            if (strpos($n, '-') > 0) {
                $pagi = round(substr($n, 0, strpos($n, '-')));
                $pagf = round(substr($n, strpos($n, '-') + 1, strlen($n)));
            } else {
                $pagi = $n;
            }
        }
        $dt['pag_start'] = $pagi;
        $dt['pag_end'] = $pagf;
        return ($dt);
    }
    public function issue_mount($n, $dt = array())
    {
        $nm = $n;
        $dz = $this->pagination($n, $dt);
        $vol = '';
        $nr = '';
        $ano = '[????]';
        /************************************************ ano *******************/
        for ($r = 1900; $r <= (date("Y") + 10); $r++) {
            if ((strpos($n, (string)$r) > 0) and ($ano == '[????]')) {
                $ano = $r;
            }
        }

        if ($ano == '[????]') {
            if (isset($dt['date'])) {
                $ano = substr($dt['date'], 0, 4);
            } else {
                $aa = (string)$dt->GetRecord->record->header->datestamp;
                $ano = substr($aa, 0, 4);
            }
        }

        /************************************************ volume *****************/
        $n = troca($n, 'V.', 'v.');
        $n = troca($n, 'Vol.', 'v.');
        $n = troca($n, 'Vol ', 'v.');
        $n = troca($n, 'vol ', 'v.');
        $n = troca($n, 'vol.', 'v.');
        $n = troca($n, 'VOL.', 'v.');
        $n = troca($n, 'volume', 'v.');
        $n = troca($n, 'Volume', 'v.');
        $n = strtolower($n);
        if (strpos(' ' . $n, 'v.') > 0) {
            $vol = trim(substr($n, strpos($n, 'v.') + 2, strlen($n)));
            if (strpos($vol, ',')) {
                $vol = substr($vol, 0, strpos($vol, ','));
            }
            if (strpos($vol, '(')) {
                $vol = substr($vol, 0, strpos($vol, '('));
            }
            if (strpos($vol, ';')) {
                $vol = substr($vol, 0, strpos($vol, ';'));
            }
            if (strpos($vol, 'n') > 0) {
                $vol = substr($vol, 0, strpos($vol, 'n'));
            }
        }
        /************************************************ numero *****************/
        $n = troca($n, 'N.', 'n.');
        $n = troca($n, 'Núm.', 'n.');
        $n = troca($n, 'Núm.:', 'n.');
        $n = troca($n, ':', '');
        $n = troca($n, 'No ', 'n.');
        $n = troca($n, 'No. ', 'n.');
        $n = troca($n, 'Nº ', 'n.');
        $n = troca($n, 'nº ', 'n.');
        $n = troca($n, 'Nº. ', 'n.');
        $n = troca($n, 'Num.', 'n.');
        $n = troca($n, 'NUM.', 'n.');
        $n = troca($n, 'núm.', 'n.');
        $n = troca($n, 'núms.', 'n.');
        $n = troca($n, 'Núms.', 'n.');
        $n = troca($n, 'número', 'n.');
        $n = troca($n, 'Número', 'n.');
        $n = troca($n, 'no ', 'n. ');
        $n = troca($n, 'online first', 'n. online');
        $n = troca($n, 'ahead of print', 'n. online');

        if (strpos($n, 'n.')) {
            $nr = trim(substr($n, strpos($n, 'n.') + 2, strlen($n)));
            if (strpos($nr, ',')) {
                $nr = substr($nr, 0, strpos($nr, ','));
                $nr = trim($nr);
            }
            if (strpos($nr, '(')) {
                $nr = substr($nr, 0, strpos($nr, '('));
                $nr = trim($nr);
            }
            if (strpos($nr, ';')) {
                $nr = substr($nr, 0, strpos($nr, ';'));
                $nr = trim($nr);
            }
            if (strpos($nr, ':')) {
                $nr = substr($nr, 0, strpos($nr, ':'));
                $nr = trim($nr);
            }

            if (strpos($nr, ' ')) {
                $nr = substr($nr, 0, strpos($nr, ' '));
                $nr = trim($nr);
            }
        }
        if ((strlen($nr) == 0) and ((strpos($n, '(') > 0))) {
            $nz = $n;
            $nz = substr($nz, strpos($nz, '(') + 1, strlen($nz));
            $nz = substr($nz, 0, strpos($nz, ')'));
            if ($nz != $ano) {
                $nr = $nz;
            }
        }
        $nr = trim($nr);
        $vol = trim($vol);
        $ano = trim($ano);

        if (strlen($ano) == 0) {
            $ano = (string)$dt->GetRecord->record->header->datestamp;
            $ano = substr($ano, 0, 4);
        }

        if ((strlen($vol . $nr) == 0) or (($ano == 'xxxx') and (strlen($nr) == 0))) {
            $v = array('diciembre', 'junio');
            $vr = array('dez.', 'jun.');
            for ($r = 0; $r < count($v); $r++) {
                if (strpos($n, $v[$r]) > 0) {
                    $nr = $vr[$r];
                }
            }

            if (strlen($nr) == 0) {
                if (strpos($nm, 'Especial')) {
                    $nr = 'esp.';
                }
                if ((strpos($nm, 'Primeiro') > 0) or (strpos($nm, 'primeiro') > 0)) {
                    $nr .= ' 1. sem.';
                }
                if ((strpos($nm, 'Segundo') > 0) or (strpos($nm, 'segundo') > 0)) {
                    $nr .= ' 2. sem.';
                }
            }

            if (strlen($nr) == 0) {
                $nx = array('janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro');
                for ($r = 0; $r < count($nx); $r++) {
                    if ((strpos($nm, $nx[$r]) > 0) and (strlen($nr) == 0)) {
                        if ($r < 6) {
                            $nr = 'esp. 1. sem.';
                        } else {
                            $nr = 'esp. 2. sem.';
                        }
                    }
                }
            }
            if (strlen($nr) == 0) {
                $sql = "select * from " . $this->base . "source_issue_convert where sc_text like '%$nm%'";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                if (count($rlt) == 1) {
                    $line = $rlt[0];
                    $vol = $line['sc_vol'];
                    $nr = $line['sc_nr'];
                    $ano = $line['sc_year'];
                }
            }
            if (strlen($nr) == 0) {

                echo $n . '<br>';
                echo '<meta http-equiv="refresh" content="30">';
                echo "FALHA v. $vol, n. $nr, $ano";
                echo $sql;
                echo '<pre>';
                print_r($dt);
                exit;
            }
        }
        $d['year'] = $ano;
        $d['vol'] = $vol;
        $d['nr'] = $nr;
        $d['source'] = $nm;
        $d = array_merge($d, $dz);
        return ($d);
    }

    public function identify($id, $data = array())
    {
        $tp = 0;
        if (isset($data['jnl_url_oai'])) {
            $url = $this->oai_url($data, 'identify');
            $tp = 1;
        } else {
            if (round($id) <= 0) {
                echo "ERRO 450 - Journal ID note found";
                exit;
            }
            $data = $this->sources->le($id);
            $url = $this->oai_url($data, 'identify');
        }
        $cnt = $this->readfile($url);
        $xml = simplexml_load_string($cnt);

        $dt = array();
        $dt['id'] = $id;
        $dt['repositoryName'] = $this->xml_value($xml->Identify->repositoryName);
        $dt['protocolVersion'] = $this->xml_value($xml->Identify->protocolVersion);
        $dt['adminEmail'] = $this->xml_value($xml->Identify->adminEmail);
        $dt['deletedRecord'] = $this->xml_value($xml->Identify->deletedRecord);
        $dt['granularity'] = $this->xml_value($xml->Identify->granularity);
        $dt['baseURL'] = $this->xml_value($xml->Identify->baseURL);
        $dt['responseDate'] = $this->xml_value($xml->responseDate);
        $dt = array_merge($data, $dt);
        if ($tp = 0) {
            $this->frbr->journal($dt);
            $this->getListSets($id);
        } else {
            return ($dt);
        }
    }

    public function cache_link($line = array())
    {
        $sx = '';
        $link = '<a href="' . base_url(PATH . 'oai/cache/' . $line['li_jnl'] . '/' . $line['li_s']) . '">';
        $sx .= $link . msg('cache_status_' . $line['li_s']) . '</a>';
        return ($sx);
    }

    public function cache_change_to($id, $id2, $id3 = '')
    {
        $sx = '';
        if (strlen($id3) > 0) {
            $sql = "update " . $this->base . "source_listidentifier
                        set li_s = $id3
                        where li_jnl = $id and li_s = $id2";
            $rlt = $this->db->query($sql);
            $sx .= '
                        <div class="alert alert-success" role="alert">
                        Success! Changed this status!
                        </div>                
                        ';
        }
        $sx .= msg('change_to') . ':<br>';
        $sx .= '<ul>';
        for ($r = 1; $r < 10; $r++) {
            $link = '<a href="' . base_url(PATH . 'oai/cache/' . $id . '/' . $id2 . '/' . $r) . '">';
            $sx .= '<li>';
            $sx .= $link . msg('cache_status_' . $r) . '</a>';
            $sx .= '</li>';
        }
        $sx .= '</ul>';
        return ($sx);
    }

    function cache_reprocess($id)
    {
        $sql = "update " . $this->base . "source_listidentifier
                    set li_s = 1
                    where id_li = $id";
        $rlt = $this->db->query($sql);
        return (1);
    }

    public function list_cache($id, $id2, $id3 = '')
    {
        if (strlen($id3) > 0) {
            $wh = " AND (id_li = $id3) ";
        } else {
            $wh = " AND (li_s = $id2)";
        }
        $sql = "select * from " . $this->base . "source_listidentifier 
                    where li_jnl = $id 
                    $wh
                    ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<ul>';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $idc = $line['id_li'];
            $link = ' <a href="' . base_url(PATH . 'oai/cache_status_to/' . $id . '/' . $id2 . '/' . $idc) . '">';
            $link .= '(' . msg('reprocess') . '</a>)';
            $sx .= '<li>' . $line['li_identifier'] . $link . '</li>' . cr();
        }
        $sx .= '</ul>';
        return ($sx);
    }

    public function cache_resume($id = '')
    {
        /* Alter status - Deleted registers */
        $sql = "update " . $this->base . "source_listidentifier set li_s = 9 where li_status = 'deleted' and li_s <> 9";
        $rlt = $this->db->query($sql);
        if (strlen($id) > 0) {
            $wh = "WHERE li_jnl = $id" . cr();
            $wh .= ' GROUP BY li_s, li_jnl ' . cr();

            /* Counter Registers */
            $sql = "select count(*) as total, li_s, li_jnl 
                        from " . $this->base . "source_listidentifier
                        $wh 
                        ORDER BY li_s ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '<h5>' . msg("cache_status") . '</h5>';
            $sx .= '<ul>';
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $sx .= '<li>' . $this->cache_link($line) . ': <span>' . $line['total'] . '</span>' . '</li>' . CR;
            }
            $sx .= '</ul>';
        } else {
            $wh = '';
            $wh .= ' GROUP BY li_s, li_jnl, jnl_name ' . cr();


            /* Counter Registers */
            $sql = "select count(*) as total, li_s, li_jnl, jnl_name 
                        from " . $this->base . "source_listidentifier
                        INNER join " . $this->base . "source_source ON li_jnl = id_jnl
                        $wh 
                        ORDER BY jnl_name, li_s ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx = '<h5>' . msg("cache_status") . '</h5>';
            $sx .= '<ul>';
            $xjnl = '';
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $jnl = $line['li_jnl'];
                if ($xjnl != $jnl) {
                    $xjnl = $jnl;
                    $link = '<a href="' . base_url(PATH . 'jnl/' . $jnl) . '" style="color: #000000;">';
                    $sx .= '<h3>' . $link . $line['jnl_name'] . '</a>' . '</h3>' . cr();
                }
                $sx .= '<li>' . $this->cache_link($line) . ': <span>' . $line['total'] . '</span>' . '</li>' . CR;
            }
            $sx .= '</ul>';
        }
        return ($sx);
    }

    public function NextHarvesting()
    {
        $sql = "select * from " . $this->base . "source_source
                    where jnl_url_oai <> '' and jnl_active = 1
                    and jnl_historic = 0
                    order by jnl_oai_token desc, jnl_oai_last_harvesting 
                    limit 1";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            $line = $rlt[0];
            return ($line);
        } else {
            return (array());
        }
    }

    public function ListIdentifiers($id)
    {
        $data = array();

        /* */
        if ($this->check_oai_index($id) == 0) {
            $this->identify($id);
            $data['status'] = 2;
            $this->ref = '';
        }

        /************************** Início a Coleta */
        $this->sources->source_status_update($id, '400');
        $data = $this->ListIdentifiers_harvesting($id);
        $this->sources->source_status_update($id, '100');
        if (count($data) > 0) {
            $data['new'] = $this->LisIdentifiesToHarvesting($id);
            $data['status'] = 2;
        } else {
            $data['new'] = 0;
            $data['status'] = 5;
        }
        return ($data);
    }

    public function LisIdentifiesToHarvestingAll()
    {
        /* Zera tudo */
        $sql = "update " . $this->base . "source_source 
                    set jnl_oai_to_harvesting = 0 
                    where 1=1";
        $rlt = $this->db->query($sql);

        $sql = "SELECT li_jnl, count(*) as total, li_s 
                    from " . $this->base . "source_listidentifier  
                    where li_s = 1
                    group by li_s, li_jnl ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        foreach ($rlt as $dt => $vl) {
            $tot = $vl['total'];
            $id = $vl['li_jnl'];

            $sql = "update " . $this->base . "source_source set
                        jnl_oai_to_harvesting = " . $tot . "
                        where id_jnl = $id";
            $rlt = $this->db->query($sql);
        }
        return ("");
    }

    public function LisIdentifiesToHarvesting($id)
    {
        $sql = "SELECT count(*) as total, li_s 
                    from " . $this->base . "source_listidentifier  
                    where li_jnl = $id and li_s = 1
                    group by li_s ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) == 0) {
            $tot = 0;
        } else {
            $tot = $rlt[0]['total'];
        }
        $this->new = $tot;

        $sql = "update " . $this->base . "source_source set
                    jnl_oai_to_harvesting = " . $tot . "
                    where id_jnl = $id";
        $rlt = $this->db->query($sql);

        return ($tot);
    }
    public function ListIdentifiers_harvesting($id, $data = array())
    {
        $tp = 0;

        if (isset($data['jnl_url_oai'])) {
            $tp = 1;
        } else {
            $data = $this->sources->le($id);
        }
        $scielo = $data['jnl_scielo'];
        $url = $this->oai_url($data, 'ListIdentifiers');
        $this->url = $url;

        $cnt = $this->readfile($url);
        if (strlen(trim($cnt)) == 0) {
            $this->erro = 510;
            return (array());
        }


        /* XML File */
        if (!strpos($cnt, 'xml version')) {
            $this->erro = 509;
            return (array());
            echo $url . '<hr>';
            echo $cnt;
            return (array());
        }

        if ($scielo != 1) {
            $xml = simplexml_load_string($cnt);
            $LI = (array)$this->xml_values_array($xml->ListIdentifiers->header);
            $token = (string)$this->xml_value($xml->ListIdentifiers->resumptionToken);
            $response = $this->xml_value($xml->responseDate);
        } else {
            $xml = simplexml_load_string($cnt);
            $LN = (array)($xml->ListIdentifiers);
            $LI = array();
            foreach ($LN as $idX => $LL) {
                $LL = (array)$LL;
                for ($xx = 0; $xx < count($LL); $xx++) {
                    $LL2 = (array)($LL[$xx]);
                    $LL2['status'] = 'active';
                    if (isset($LL2['identifier'])) {
                        array_push($LI, $LL2);
                    }
                }
            }

            $token = (string)$this->xml_value($xml->ListIdentifiers->resumptionToken);
            $response = $this->xml_value($xml->responseDate);
        }

        $this->update_token($id, $token);
        $dta = array();
        $dta['Journal'] = ascii($data['jnl_name']);
        $dta['Journal_id'] = $id;
        $dta['Status'] = 200;
        $dta['Token'] = $token;

        for ($r = 0; $r < count($LI); $r++) {
            $line = $LI[$r];
            $this->cache($data['id_jnl'], $line);
        }

        /* Atualiza registros para coletar */
        $this->LisIdentifiesToHarvestingAll();
        return ($dta);
    }

    private function update_token($id_jnl, $token)
    {
        $date = date("Y-m-d H:m:s");
        $sql = "update " . $this->base . "source_source 
                    set 
                    jnl_oai_token = '$token',
                    jnl_oai_last_harvesting = '$date'
                    where id_jnl = $id_jnl";
        $rlt = $this->db->query($sql);
    }

    private function cache($id_jnl, $data)
    {
        $identifier = $data['identifier'];
        $sql = "select * from " . $this->base . "source_listidentifier 
                    where li_identifier = '$identifier'
                    AND li_jnl = $id_jnl ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '';

        if (!isset($data['datestamp'])) {
            $data['datestamp'] = date("Y-m-d");
        }
        if (!isset($data['setSpec'])) {
            $data['setSpec'] = 'setSpec';
        }

        if (count($rlt) > 0) {
            $line = $rlt[0];
            $sqlu = "li_status = '" . $data['status'] . "'";
            $sqlu .= ", li_datestamp = '" . substr($data['datestamp'], 0, 10) . "'";
            $sqlu .= ", li_setSpec = '" . $data['setSpec'] . "'";
            $up = 0;
            if (($data['status'] != $line['li_status']) or ($data['setSpec'] != $line['li_setSpec'])) {
                $sqlu .= ", li_update " . $this->base . "= 1";
                $up = 1;
            }
            $sql = "update " . $this->base . "source_listidentifier set " . $sqlu . " where id_li = " . $line['id_li'];
            if ($up == 1) {
                $this->db->query($sql);
            }
            $sx .= $identifier . ' ' . '<span class="alert-warning">harvested</span>';
            $this->reg++;
        } else {
            $fld1 = 'li_jnl';
            $fld2 = $id_jnl;
            foreach ($data as $key => $value) {
                $fld1 .= ', li_' . $key;
                if ($key == 'datestamp') {
                    $value = troca($value, 'Z', '');
                    $value = troca($value, 'T', ' ');
                }
                $fld2 .= ", '" . $value . "'";
            }
            $sql = "insert into " . $this->base . "source_listidentifier
                        ($fld1)
                        values
                        ($fld2)";
            $this->db->query($sql);
            $sx .= $identifier . ' ' . '<span class="alert-success">inserted</span>';
            $this->new++;
        }
        return ($sx);
    }

    public function xml_values_array($x)
    {
        $v = array();

        if (strlen($x) == 0) {
            return (array());
        }

        for ($r = 0; $r < count($x); $r++) {
            $xx = $x[$r];
            $rg = array();
            $rg['status'] = 'active';
            /******************* atributes *************/
            foreach ($xx->attributes() as $a => $b) {
                $rg[$a] = (string)$b;
            }

            /******************* values ****************/
            foreach ($xx as $key => $value) {
                $rg[$key] = (string)$value;
            }
            if (count($rg) > 0) {
                array_push($v, $rg);
            }
        }
        return ($v);
    }

    public function xml_values($x)
    {
        if (strlen($x) == 0) {
            return (array());
        }
        $v = array();
        foreach ($x as $key => $value) {
            array_push($v, (string)$value);
        }
        return ($v);
    }

    public function xml_value($x)
    {
        if (strlen($x) == 0) {
            return ("");
        }
        foreach ($x as $key => $value) {
            return ((string)$value);
        }
    }

    public function readfile($url)
    {
        $url = trim($url);
        if (substr($url, 0, 4) == 'http') {
            $data = load_page($url);
            $data = $data['content'];
            return ($data);
        }
        try {
            if (file_exists($url)) {
                $cnt = file_get_contents($url);
            } else {
                $this->erro = '404';
                $cnt = '';
            }
        } catch (Exception $e) {
            $this->erro = -1;
            $this->erro_msg = $e->getMessage();
            $cnt = '';
        }
        return ($cnt);
    }

    public function oai_url($data, $verb)
    {
        $url = trim($data['jnl_url_oai']);
        $scielo = '';
        if ($data['jnl_scielo'] == '1') {
            $scielo = '&set=' . $data['jnl_oai_set'];
        }
        switch ($verb) {
            case 'ListSets':
                $url .= '?verb=ListSets';
                break;

            case 'GetRecord':
                $url .= '?verb=GetRecord&metadataPrefix=oai_dc&identifier=';
                break;

            case 'GetRecordNlm':
                $url .= '?verb=GetRecord&metadataPrefix=nlm&identifier=';
                break;
            case 'ListIdentifiers':
                if (strlen($data['jnl_oai_token']) > 5) {
                    $url .= '?verb=ListIdentifiers&resumptionToken=' . trim($data['jnl_oai_token']);
                    $url .= $scielo;
                } else {
                    $url .= '?verb=ListIdentifiers&metadataPrefix=oai_dc';
                    $url .= $scielo;
                }
                break;
            case 'identify':
                $url .= '?verb=Identify';
                break;
        }
        return ($url);
    }

    function rescan_xml($id, $art)
    {
        $art = strzero($art, 10);
        $idx = strzero($id, 7);
        $file = 'ma/oai/' . $idx . '.xml';
        $txt = load_file_local($file);
        $sx = '<dc:identifier>';
        $txt = substr($txt, strpos($txt, $sx) + strlen($sx), strlen($txt));
        $txt = trim(substr($txt, 0, strpos($txt, '<')));

        if (substr($txt, 0, 4) == 'http') {
            $data['content'] = '==>' . $txt . '<==';
            $sql = "select * from " . $this->base . "brapci_article_suporte where bs_adress = '$txt' and bs_article = '$idx' ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) == 0) {
                $this->load->view('content', $data);
                $sql = "insert into " . $this->base . "brapci_article_suporte 
                                                (
                                                    bs_status, bs_article, bs_type, 
                                                    bs_adress, bs_journal_id, bs_update
                                                    ) values (
                                                        '@','$art','URL',
                                                        '$txt',''," . date("Ymd") . ')';
                $this->db->query($sql);
            } else {
            }
        } else {
        }
        redirect(base_url('index.php/article/view/' . $art));
    }

    public function repository_list()
    {
        $sql = "select * from " . $this->base . "source_source 
                                                where jnl_active <> 'X'
                                                AND jnl_url_oai <> ''	
                                                order by jnl_name
                                                ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $last = $line['update_at'];
            $url = $line['jnl_url'];
            $link = '<A HREF="' . trim($line['jnl_url']) . '" target="_new">';
            $link_oai = base_url(PATH . 'oai/Identify/' . $line['id_jnl']);

            $sx .= '<div class="col-md-6">' . CR;
            $sx .= '<a href="' . $link_oai . '" class="link">';
            $sx .= $line['jnl_name'];
            $sx .= '</a>';
            $sx .= '</div>';

            $sx .= '<div class="col-md-4">' . CR;
            $sx .= $line['jnl_oai_token'];
            $sx .= '</div>' . CR;

            $sx .= '<div class="col-md-2">' . CR;
            $sx .= stodbr($line['update_at']);
            $sx .= '</div>' . CR;
        }
        return ($sx);
    }

    function oai_listset($ida, $setSepc, $date)
    {
        $ida = trim($ida);
        $jid = $this->jid;
        $njid = strzero($jid, 7);

        $sql = "select * from " . $this->base . "oai_cache where cache_oai_id = '$ida' ";
        $rlt = $this->db->query($sql);
        $line = $rlt->result_array();

        if (count($line) > 0) {
            return ('<span class="label label-warning">Already!</span>');
            /* já existe */
        } else {
            $data = date("Ymd");
            $sql = "update " . $this->base . "brapci_journal set jnl_last_harvesting = '$data', jnl_update = '$data' where id_jnl = $jid ";
            $rlt = $this->db->query($sql);

            /* Insere na agenda */
            $sql = "insert into " . $this->base . "oai_cache (
                                                        cache_oai_id, cache_status, cache_journal, 
                                                        cache_prioridade, cache_datastamp, cache_setSpec, 
                                                        cache_tentativas
                                                        ) values (
                                                            '$ida','@','$njid',
                                                            '1','$date','$setSepc',
                                                            0
                                                            )";
            $this->db->query($sql);
            return ('<span class="label label-success">Insert!</span>');
        }
    }

    function _deletar___ListIdentifiers_Method_1($url)
    {
        $rs = load_page($url);

        $xml_rs = $rs['content'];
        $xml = simplexml_load_string($xml_rs);

        $token = $xml->ListIdentifiers->resumptionToken;
        $this->token = $token;

        $xml = $xml->ListIdentifiers->header;
        $sx = '<ul>';
        $status = 'ok';
        $del = 0;
        $reg = 0;
        $new = 0;
        for ($r = 0; $r < count($xml); $r++) {
            foreach ($xml[$r]->attributes() as $a => $b) {
                if ($a == 'status') {
                    //$status = $b;
                }
            }
            $ida = $xml[$r]->identifier;
            $date = $xml[$r]->datestamp;
            $setSpec = $xml[$r]->setSpec;

            if ($status == 'deleted') {
                $rt = '<span class="label label-important">deleted</span>';
                $sx .= '<li>' . $ida . ' - ' . $status . '</li>';
                $del++;
            } else {
                $rt = $this->oai_listset($ida, $setSpec, $date);
                $sx .= '<li>' . $ida . ' - ' . $rt . '</li>';
                if (strpos($rt, 'harvested')) {
                    $reg++;
                } else {
                    $new++;
                }
            }
            $this->new = $new;
            $this->del = $del;
            $this->reg = $reg;
        }
        $sx .= '</ul>';

        return ($sx);
    }

    /** Altera Status **/
    function altera_status_chache($id, $sta)
    {
        $sql = "update " . $this->base . "oai_cache set cache_status = '$sta' where id_cache = $id ";
        $this->db->query($sql);
        return (1);
    }

    /* SetSepc */
    function save_setspec($set, $tema, $jid)
    {
        $jid = strzero($jid, 7);
        $sql = "select * from " . $this->base . "oai_listsets where ls_setspec = '$set' and ls_journal = '$jid' ";
        $rlt = db_query($sql);
        if ($line = db_read($rlt)) {
            $sql = "update " . $this->base . "oai_listsets set ls_equal = '$tema' where id_ls = " . round($line['id_ls']);
            $this->db->query($sql);
            return ('');
        } else {
            $data = date("Ymd");
            $sql = "insert into " . $this->base . "oai_listsets (
                                                                ls_setspec, ls_setname, ls_setdescription,
                                                                ls_journal, ls_status, ls_data,
                                                                ls_equal, ls_tipo, ls_equal_ed
                                                                ) values (
                                                                    '$set','$set','',
                                                                    '$jid','A','$data',
                                                                    '$tema','S','')";
            $rlt = $this->db->query($sql);
        }
        return ('');
    }

    /** PROCESS */

    function recupera_ano($s)
    {
        //$s = trim(sonumero($s));
        $ano = '';
        for ($r = (date("Y") + 1); $r > 1940; $r--) {
            if (strpos($s, trim($r)) > 0) {
                if (strlen($ano) == 0) {
                    return ($r);
                }
            }
        }
        return ($ano);
    }

    /******************************************************************************
     * RECUPERA NUMERO ************************************************************
     ******************************************************************************/
    function recupera_nr($s)
    {
        $nr = '';
        $s = troca($s, 'esp.', '');
        $s = troca($s, 'Esp.', '');
        $s = troca($s, 'esp', '');
        if (strpos($s, 'n.')) {
            $nr = substr($s, strpos($s, 'n.'), strlen($s));
        }
        if (strpos($s, 'No ')) {
            $nr = substr($s, strpos($s, 'No ') + 3, strlen($s));
        }
        if (strpos($s, 'No ')) {
            $nr = substr($s, strpos($s, 'No ') + 3, strlen($s));
        }
        if (strpos($s, 'Nº ')) {
            $nr = substr($s, strpos($s, 'N° ') + 2, strlen($s));
        }
        if (strpos($s, 'nº')) {
            $nr = substr($s, strpos($s, 'nº') + 2, strlen($s));
        }

        if (strpos($s, 'Núm. ')) {
            $nr = substr($s, strpos($s, 'Núm. ') + 4, strlen($s));
        }
        if (strpos($s, 'NÚM. ')) {
            $nr = substr($s, strpos($s, 'NÚM. ') + 4, strlen($s));
        }
        $nr = trim($nr);

        if (strlen($nr) > 0) {
            if (strpos($nr, ',') > 0) {
                $nr = substr($nr, 0, strpos($nr, ','));
            }
            if (strpos($nr, '-') > 0) {
                $nr = substr($nr, 0, strpos($nr, '-'));
            }
            if (strpos($nr, '(') > 0) {
                $nr = substr($nr, 0, strpos($nr, '('));
            }
            $nr = troca($nr, 'n. ', '');
            $nr = troca($nr, ' ', 'x');
            if (strpos($nr, 'x') > 0) {
                $nr = substr($nr, 0, strpos($nr, 'x'));
            }
            $nr = troca($nr, 'x', '');
            $nr = troca($nr, 'n.', '');
            $nr = trim($nr);
        }
        return ($nr);
    }

    /******************************************************************************
     * RECUPERA VOLUME ************************************************************
     ******************************************************************************/
    function recupera_vol($s)
    {
        $vl = '';
        $s = troca($s, 'V.', 'v.');
        $s = troca($s, 'Vol ', 'v.');
        $s = troca($s, 'Vol.', 'v.');

        /***********************************************************************/
        if (strpos($s, 'v.')) {
            $vl = substr($s, strpos($s, 'v.'), strlen($s));
        }

        if (strlen($vl) > 0) {
            if (strpos($vl, '(') > 0) {
                $vl = substr($vl, 0, strpos($vl, '('));
            }
            if (strpos($vl, ',') > 0) {
                $vl = substr($vl, 0, strpos($vl, ','));
            }
            $vl = troca($vl, 'v. ', '');
            if (strpos($vl, ' ') > 0) {
                $vl = substr($vl, 0, strpos($vl, ' '));
            }
            $vl = troca($vl, 'v.', '');
            $vl = trim($vl);
        }
        return ($vl);
    }

    function recupera_section($sec, $jid)
    {
        $sql = "select * from " . $this->base . "oai_listsets where ls_setspec = '$sec' and ls_journal = '$jid'";
        $rlt = db_query($sql);
        if ($line = db_read($rlt)) {
            $rsec = trim($line['ls_equal']);
        } else {
            $data = array();
            return ('');
            $rsec = '';
        }
        return ($rsec);
    }

    function recupera_issue($rcn, $jid)
    {
        $issue = $rcn['sources'];
        for ($r = 0; $r < count($issue); $r++) {
            $si = $issue[$r]['source'];
            $ano = round($this->recupera_ano($si));
            $nr = $this->recupera_nr($si);
            $vol = $this->recupera_vol($si);

            if ($ano < 1970) {
                echo "ANO INVÁLIDO";
                echo '<hr>';
                print_r($issue);
                exit;
            }

            for ($r = 0; $r < 50; $r++) {
                echo '<br>==>' . $ano . ' ' . $nr . ' ' . $vol;
            }

            /* Trata issue */
            $jid = strzero($jid, 7);

            $sql = "selectx * from " . $this->base . "brapci_edition where 
                                                                    ed_vol = '$vol'
                                                                    and ed_nr = '$nr'
                                                                    and ed_ano = '$ano' 
                                                                    and ed_journal_id = '$jid' ";
            $rlt = db_query($sql);
            $sx = "v. $vol, n. $nr, $ano";
            $this->issue = $sx;

            if ($line = db_read($rlt)) {
                $eds = $line['ed_status'];
                if ($eds == 'A') {
                    return ($line['id_ed']);
                } else {
                    return ('9999999');
                }
            } else {
                return (0);
            }
        }
    }

    function process_le_xml($xml_rs, $file)
    {
        $dom = new DOMDocument;
        $dom = new DOMDocument;

        /* Arquivo vazio */
        $fr = fopen($file, 'r');
        $st = fread($fr, 512);
        fclose($fr);

        if (strlen($st) == 0) {
            $doc['status'] = 'reload';
            echo '<meta http-equiv="refresh" content="1">';
            return ($doc);
        }
        $dom->load($file);

        /* Array */
        $doc = array();

        /* Header */
        $headers = $dom->getElementsByTagName('header');
        $status = '';
        foreach ($headers as $header) {
            //$setSpec = $header -> nodeValue;
            if (isset($header->attributes->getNamedItem('status')->value)) {
                $status = $header->attributes->getNamedItem('status')->value;
            }
        }

        /* Registro deletado, nao processar */
        if ($status == 'deleted') {
            $doc['status'] = 'deleted';
            return ($doc);
        } else {
            $doc['status'] = 'active';
        }

        /* setSpec */
        $headers = $dom->getElementsByTagName('setSpec');
        $size = ($headers->length);
        /* Header inválido */
        if ($size < 1) {
            $doc['status'] = 'deleted';
            return ($doc);
            exit;
        }

        foreach ($headers as $header) {
            $setSpec = $header->nodeValue;
        }
        $setSpec = troca($setSpec, ':', '_');
        $setSpec = troca($setSpec, ' ', '_');
        $setSpec = troca($setSpec, '+', '_');
        $doc['setSpec'] = $setSpec;

        /* setSpec */
        $idf = '';
        $headers = $dom->getElementsByTagName('identifier');
        foreach ($headers as $header) {
            if (strlen($idf) == 0) {
                $idf = $header->nodeValue;
            }
        }
        $doc['idf'] = $idf;

        $nodes = $dom->getElementsByTagName('metadata');

        /* Recupeda dados */
        foreach ($nodes as $node) {

            /* Recupera titulos */
            $titles = $node->getElementsByTagName("title");
            $id = 0;
            foreach ($titles as $title) {
                $value = $title->nodeValue;
                $value = troca($value, "'", "´");
                $lang = $title->attributes->getNamedItem('lang')->value;
                if ($lang == 'pt-BR') {
                    $lang = 'pt-BR';
                }
                if ($lang == 'en-US') {
                    $lang = 'en';
                }

                $dt = array();
                $dt['title'] = $value;
                $dt['idioma'] = $lang;
                $doc['titles'][$id] = $dt;
                $id++;
            }
            /* Recupera autores */
            $titles = $node->getElementsByTagName("creator");
            $id = 0;
            foreach ($titles as $title) {
                $value = troca($title->nodeValue, "'", '´');
                $dt = array();
                $dt['name'] = $value;
                $doc['authors'][$id] = $dt;
                $id++;
            }
            /* Recupera KeyWorkds */
            $titles = $node->getElementsByTagName("subject");
            $id = 0;
            foreach ($titles as $title) {
                $value = $title->nodeValue;
                $lang = $title->attributes->getNamedItem('lang')->value;
                if ($lang == 'pt-BR') {
                    $lang = 'pt-BR';
                }
                if ($lang == 'en-US') {
                    $lang = 'en';
                }
                $dt = array();
                $dt['term'] = $value;
                $dt['idioma'] = $lang;
                $doc['keywords'][$id] = $dt;
                $id++;
            }
            /* Recupera Resumos */
            $titles = $node->getElementsByTagName("description");
            $id = 0;
            foreach ($titles as $title) {
                $value = $title->nodeValue;
                $lang = $title->attributes->getNamedItem('lang')->value;
                $lang = $this->frbr_core->language($lang);
                $dt = array();

                $value = troca($value, '  ', ' ');
                $dt['content'] = $value;
                $dt['idioma'] = $lang;
                $doc['abstract'][$id] = $dt;
                $id++;
            }

            /* link */

            $titles = $node->getElementsByTagName("identifier");
            $id = 0;
            foreach ($titles as $title) {
                $value = $title->nodeValue;
                $dt = array();
                $dt['link'] = $value;
                $doc['links'][$id] = $dt;
                $id++;
            }

            /* Source */
            $titles = $node->getElementsByTagName("source");
            $id = 0;
            foreach ($titles as $title) {
                $value = $title->nodeValue;
                $dt = array();
                $dt['source'] = $value;
                $doc['sources'][$id] = $dt;
                $id++;
            }
            return ($doc);
        }
        return (array());
    }

    function _deletar_coleta_oai_cache_next($id)
    {
        $jid = strzero($id, 7);
        $sql = "select * from " . $this->base . "oai_cache
                                                                inner join " . $this->base . "brapci_journal on jnl_codigo = cache_journal
                                                                where cache_journal = '$jid'
                                                                and cache_status = '@'
                                                                ";
        $rlt = db_query($sql);

        $sr = 'nothing to harvesting';

        if ($line = db_read($rlt)) {
            $url = trim($line['jnl_url_oai']);
            $ido = trim($line['cache_oai_id']);
            $idr = $line['id_cache'];

            /* Atualiza registro de coleta */
            $sql = "update " . $this->base . "oai_cache set cache_tentativas = cache_tentativas + 1 where id_cache = " . $id;
            $this->db->query($sql);

            /* Method 1 */
            $link = $url . '?verb=GetRecord';
            $link .= '&metadataPrefix=oai_dc';
            $link .= '&identifier=' . $ido;
            $xml_rt = load_page($link);
            $xml = $xml_rt['content'];

            $sr = '<BR><font color="grey">Cache:</font> ' . $ido . ' <font color="green">coletado</font>';

            $file = 'ma/oai/' . strzero($idr, 7) . '.xml';
            $f = fopen($file, 'w+');
            fwrite($f, $xml);
            fclose($f);

            $sql = "update " . $this->base . "oai_cache set cache_status='A' where id_cache = " . $idr;
            $this->db->query($sql);

            /* Meta refresh */
            $sr .= '<meta http-equiv="refresh" content="3">';
        }
        return ($sr);
    }

    function oai_resumo_to_harvesing()
    {
        $sql = "select count(*) as total, cache_journal, jnl_nome from " . $this->base . "oai_cache 
                                                                inner join " . $this->base . "brapci_journal on jnl_codigo = cache_journal
                                                                where cache_status = '@'
                                                                group by cache_journal, jnl_nome
                                                                order by jnl_nome ";
        $rlt = db_query($sql);
        $t = array(0, 0, 0, 0);
        $sx = '<h1>Record to harvesting</h1>';
        while ($line = db_read($rlt)) {
            $link = '<A HREF="' . base_url('index.php/oai/Harvesting/' . $line['cache_journal']) . '">';
            $sx .= '' . $link . $line['jnl_nome'] . '</A>';
            $sx .= ' (' . $line['total'] . ')<BR>';
        }
        return ($sx);
    }

    function oai_resumo_to_progress()
    {
        $sql = "select count(*) as total, cache_journal, jnl_nome from " . $this->base . "oai_cache 
                                                                inner join " . $this->base . "brapci_journal on jnl_codigo = cache_journal
                                                                where cache_status = 'A'
                                                                group by cache_journal, jnl_nome
                                                                order by jnl_nome ";
        $rlt = db_query($sql);
        $t = array(0, 0, 0, 0);
        $sx = '<br><br><h1>Record to process</h1>';
        while ($line = db_read($rlt)) {
            $link = '<A HREF="' . base_url('index.php/oai/Harvesting/' . $line['cache_journal']) . '">';
            $sx .= '' . $link . $line['jnl_nome'] . '</A>';
            $sx .= ' (' . $line['total'] . ')<BR>';
        }
        return ($sx);
    }

    function oai_resset_cache($id)
    {
        $sql = "update " . $this->base . "oai_cache set cache_status = '@' where cache_journal = '" . strzero($id, 7) . "'";
        $rlt = $this->db->query($sql);
        return (1);
    }

    function oai_resumo($jid = 0)
    {
        $wh = ' 1 = 1 ';
        if ($jid > 0) {
            $wh = " cache_journal = '" . strzero($jid, 7) . "' ";
        }

        $sql = "select count(*) as total, cache_status from " . $this->base . "oai_cache 
                                                                where $wh 
                                                                group by cache_status ";
        $rlt = db_query($sql);
        $t = array(0, 0, 0, 0);
        while ($line = db_read($rlt)) {
            $sta = $line['cache_status'];
            $tot = $line['total'];
            switch ($sta) {
                case '@':
                    $t[0] = $t[0] + $line['total'];
                    break;
                case 'B':
                    $t[1] = $t[1] + $line['total'];
                    break;
                case 'A':
                    $t[2] = $t[2] + $line['total'];
                    break;
                default:
                    $t[3] = $t[3] + $line['total'];
                    break;
            }
        }

        $sx = '';
        $sx .= 'OAI-PMH Status';
        $sx .= '<ul class="nav nav-tabs nav-justified">';
        $sx .= '<li><a href="#">para coletar <span class="badge">' . number_format($t[0], 0, ',', '.') . '</span></a></li>';
        $sx .= '<li><a href="#">coletado <span class="badge">' . number_format($t[2], 0, ',', '.') . '</span></a></li>';
        $sx .= '<li><a href="#">processado <span class="badge">' . number_format(($t[1] + $t[3]), 0, ',', '.') . '</span></a></li>';
        $sx .= '<li><a href="#">total <span class="badge">' . number_format(($t[0] + $t[1] + $t[2] + $t[3]), 0, ',', '.') . '</span></a></li>';
        $sx .= '</ul>';
        return ($sx);
    }

    function doublePDFlink()
    {
        $sql = "select * from " . $this->base . "(
                                                                                SELECT bs_adress, count(*) as total, max(id_bs) as id 
                                                                                from " . $this->base . "`brapci_article_suporte` 
                                                                                WHERE bs_type = 'URL' 
                                                                                and bs_adress like 'http%'
                                                                                and (bs_status ='A' or bs_status = '@')
                                                                                and bs_adress <> ''
                                                                                group by bs_adress
                                                                                ) as tabela
                                                                                where total > 1
                                                                                ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $adress = $line['bs_adress'];
                $id = $line['id'];
                $sql = "update " . $this->base . "brapci_article_suporte 
                                                                                        set bs_status = 'D' 
                                                                                        WHERE bs_adress = '$adress' 
                                                                                        and id_bs <> $id ";
                $xrlt = $this->db->query($sql);
            }
        } else {
            return (0);
        }
    }

    function artcle_wifout_file($pag = 0)
    {
        $off = $pag * 350;
        $sql = "select count(*) as total from " . $this->base . "brapci_article
                                                                                LEFT join " . $this->base . "(
                                                                                    select count(*) as total, bs_article from " . $this->base . "brapci_article_suporte 
                                                                                    where bs_status <> 'X' and bs_type = 'PDF' 
                                                                                    group by bs_article
                                                                                    ) as tabela ON bs_article = ar_codigo
                                                                                    WHERE TOTAL is null AND ar_status <> 'X' 
                                                                                    limit 50 offset $off";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<h4>' . $rlt[0]['total'] . '</h4>';

        $sql = "select ar_codigo, ar_titulo_1, jnl_nome from " . $this->base . "brapci_article
                                                                                    LEFT join " . $this->base . "(
                                                                                        select count(*) as total, bs_article from " . $this->base . "brapci_article_suporte 
                                                                                        where bs_status <> 'X' and bs_type = 'PDF' 
                                                                                        group by bs_article
                                                                                        ) as tabela
                                                                                        ON bs_article = ar_codigo
                                                                                        INNER join " . $this->base . "brapci_journal ON jnl_codigo = ar_journal_id
                                                                                        
                                                                                        WHERE TOTAL is null AND ar_status <> 'X'					
                                                                                        ORDER BY jnl_nome, ar_codigo";
        /* removido em 27/07/2017 - limit 350 offset $off"; */

        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();

        $sx .= '<ul>';
        $jnl = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $xjnl = $line['jnl_nome'];
            if ($jnl != $xjnl) {
                $sx .= '<h4>' . $xjnl . '</h4>';
                $jnl = $xjnl;
            }
            $link = '<a href="' . base_url('index.php/admin/article_view/' . $line['ar_codigo'] . '/' . checkpost_link($line['ar_codigo'])) . '">';
            $sx .= '<li>' . $link . $line['ar_titulo_1'] . '</a></li>';
        }
        $sx .= '</ul>';
        return ($sx);
    }

    function fileExistPDFlink($pag = 0)
    {
        $sz = 30;
        $OFFSET = ($pag * 100);
        $data = date("Ymd");
        $sql = "select * from " . $this->base . "brapci_article_suporte 
                                                                                        WHERE bs_update <> '$data' 
                                                                                        and bs_status <> 'X'
                                                                                        and bs_type = 'PDF'
                                                                                        order by id_bs 
                                                                                        LIMIT 100 OFFSET $OFFSET
                                                                                        
                                                                                        ";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '';
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $sx .= '<br>';
            $sx .= ($r + $pag * 100) . '. ';
            $file = $line['bs_adress'];
            $sx .= $file;
            if (file_exists($file)) {
                $sx .= ' <b><font color="green">OK</font></b>' . cr();
            } else {
                $sx .= ' <b><font color="red">file not found</font></b>' . cr();
                $sql = "update " . $this->base . "brapci_article_suporte set bs_status = 'X', bs_update = '" . date("Ymd") . "' where id_bs = " . $line['id_bs'];
                $rla = $this->db->query($sql);
            }
        }
        if (count($rlt) > 0) {
            $sx .= '<META http-equiv="refresh" content="5;URL=' . base_url('index.php/admin/fileexist_pdf/' . ($pag + 1)) . '">';
        }
        return ($sx);
    }

    function totalPDFharvesting()
    {
        $sql = "select count(*) as total (
                                                                                            SELECT `bs_article` as art, count(*) as total FROM " . $this->base . "brapci_article_suporte WHERE bs_type = 'URL' group by bs_article
                                                                                            )
                                                                                            as tebela
                                                                                            inner join " . $this->base . "brapci_article_suporte on art = bs_article
                                                                                            where total = 1 and bs_adress like 'http%'
                                                                                            and bs_status ='A' or bs_status = '@'
                                                                                            and art <> '' 
                                                                                            limit 1";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            return ($rlt[0]['total']);
        } else {
            return (0);
        }
    }

    function nextPDFharvesting()
    {
        $sql = "select * from " . $this->base . "(
                                                                                                SELECT `bs_article` as art, count(*) as total 
                                                                                                FROM " . $this->base . "brapci_article_suporte` 
                                                                                                WHERE bs_type = 'URL' group by bs_article
                                                                                                )
                                                                                                as tebela
                                                                                                inner join " . $this->base . "brapci_article_suporte on art = bs_article
                                                                                                where total = 1 and bs_adress like 'http%'
                                                                                                and bs_status ='A' or bs_status = '@'
                                                                                                and art <> '' 
                                                                                                order by art desc
                                                                                                limit 1";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            $id = $rlt[0]['id_bs'];
            $sql = "update " . $this->base . "brapci_article_suporte set bs_status = 'T' where id_bs = " . $id;
            $this->db->query($sql);
            return ($rlt[0]);
        } else {
            return (0);
        }
    }

    function nextPDFconvert()
    {
        $data = date("Ymd");
        $sql = "select * from " . $this->base . "brapci_article_suporte where bs_status = 'T'
                                                                                                and bs_update <> $data
                                                                                                limit 1";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) > 0) {
            $id = $rlt[0]['id_bs'];
            $sql = "update " . $this->base . "brapci_article_suporte set bs_status = 'U', bs_update = $data 
                                                                                                    where id_bs = " . $id;
            $this->db->query($sql);
            return ($rlt[0]);
        } else {
            return (0);
        }
    }
    function valida_metadata($id = 0)
    {
        $jnl = 16;
        $sx = bs_alert('info', msg("OK"));
        $wh = 'li_jnl = ' . $jnl . ' and ';
        $sql = "select * from " . $this->base . "source_listidentifier 
                                                                                                where $wh li_status = 'active' and li_s = 3
                                                                                                order by li_s, li_u, id_li
                                                                                                limit 1 offset " . $id;
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();

        /* dados da publicação */
        $data = $this->sources->le($jnl);

        if (count($rlt) > 0) {
            $line = $rlt[0];
            $url = $this->oai_url($data, 'GetRecord') . $line['li_identifier'];
            $cnt = $this->readfile($url);
            $cnt = troca($cnt, 'oai_dc:', 'oai_');
            $cnt = troca($cnt, 'dc:', '');
            $cnt = troca($cnt, 'xml:', '');
            $xml = simplexml_load_string($cnt);

            $idi = $line['li_identifier'];
            $sx .= '<h2>' . $url . '<br>' . $idi . '</h2>';
            $dt = $xml->GetRecord->record->header;
            $mt = $xml->GetRecord->record->metadata->oai_dc;

            $dtst = $dt->datestamp;
            $setSpec = $dt->setSpec;
            /**********************/
            $date = $mt->date;
            $title = $this->xml_values($mt->title);
            $creator = $this->xml_values($mt->creator);

            echo '<br>=date stamp=>' . $dtst;
            echo '<br>=setSpec=>' . $setSpec;
            echo '<br>=Date=>' . $date;
            echo '<hr>';
            print_r($title);
            echo '<hr>';
            print_r($creator);
            echo '<hr>';

            //echo '<pre>';
            //print_r($xml);
            //echo '</pre>';        
        }
        return ($sx);
    }
}
