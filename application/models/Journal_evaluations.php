<?php
class journal_evaluations extends CI_Model
{
    var $base = 'brapci_evaluation.';
    function __construct()
    {
        $this->load->model('oai_pmh');
        $this->oai_pmh->base = 'brapci_evaluation.';
        $this->load->model('sources');
        $this->sources->table = 'brapci_evaluation.source_source';
    }
    function parameters()
    {
        $d = array();
        $d['table'] = $this->sources->table;
        $d['fields'] = array(0, 70, 10, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5);
        $d['page_view'] = base_url(PATH . 'evaluation/source');
        $d['harvesting'] = 'single';
        return ($d);
    }
    function index($d1, $d2, $d3, $d4)
    {
        $sx = '';
        switch ($d1) {
            case 'getrecord':
                $sx = $this->GetRecord($d2);
                $sx .= '<br>' . $this->back();
            break;
            case 'report':
                $sx = $this->report($d2);
            break;

            case 'set':
                $sx = $this->set();
            break;

            case 'identify':
                $sx .= $this->identify();
                $sx .= '<br>' . $this->back();
            break;

            case 'sets':
                $sx .= $this->sets();
                $sx .= '<br>' . $this->back();
            break;

            case 'sources':
                $sx .= $this->sources($d2, $d3, $d4);
                $sx .= '<br>' . $this->back();
                /* Atualiza data das publicações */
                $this->set_years();
            break;

            case 'source':
                $sx .= $this->source($d2, $d3);
                $sx .= '<br>' . $this->back();
            break;

            case 'politics':
                $sx .= $this->politics($d2, $d3);
                $sx .= '<br>' . $this->back();
            break;

            case 'listsets':
                $sx .= $this->listsets();
                $sx .= '<br>' . $this->back();
            break;
            case 'system':
                $id = $this->rset();
                $dt = le($this->sources->table, 'id_jnl=' . $id);

                $link = trim($dt['jnl_url']);
                if (strlen($link) > 0) {
                    $txt = read_link($link);
                    $sx .= $this->version_software($id, $txt);
                }
                $sx .= '<br>' . $this->back();
            break;

            default:
            $sx .= '<h1>' . msg('journal_evaluation') . '</h1>';
            $sx .= '<ul>';
            $jnl = $this->rset();
            $sx .= '<li>';
            $sx .= '<a href="' . base_url(PATH . 'evaluation/set') . '">' . msg('select_journal_url') . '</a>';
            $sx .= ' ' . msg('or') . ' ';
            $sx .= '<a href="' . base_url(PATH . 'evaluation/sources') . '">' . msg('source_journal') . '</a>';
            $sx .= '</li>';

            if ($jnl > 0) {
                $sx .= '<li>' . '<a href="' . base_url(PATH . 'evaluation/identify') . '">' . msg('ev_identify_journal') . '</a>' . '</li>';
                $sx .= '<li>' . '<a href="' . base_url(PATH . 'evaluation/sets') . '">' . msg('ev_sets_journal') . '</a>' . '</li>';
                $sx .= '<li>' . '<a href="' . base_url(PATH . 'evaluation/listsets') . '">' . msg('ev_listsets_journal') . '</a>' . '</li>';
                $sx .= '<li>' . '<a href="' . base_url(PATH . 'evaluation/getrecord') . '">' . msg('ev_getrecord') . '</a>' . '</li>';
                $sx .= '<li>' . '<a href="' . base_url(PATH . 'evaluation/politics') . '">' . msg('ev_politics') . '</a>' . '</li>';
                $sx .= '<li>' . '<a href="' . base_url(PATH . 'evaluation/system') . '">' . msg('ev_system') . '</a>' . '</li>';
                $sx .= '<li>' . '<a href="' . base_url(PATH . 'evaluation/report') . '">' . msg('report') . '</a>' . '</li>';
            }
            $sx .= '</ul>';
        }
        return ($sx);
    }
    function back()
    {
        $sx = '<a href="' . base_url(PATH . 'evaluation') . '" class="btn btn-outline-primary">' . msg('return') . '</a>';
        return ($sx);
    }

    function set_years()
    {
        $sql = "update " . $this->base . "source_listidentifier
        set li_year = year(li_datestamp)
        where li_year = 0 limit 1000";
        $rlt = $this->db->query($sql);
        return ($sql);
    }

    function listsets()
    {
        //set_time_limit(60000);
        $sx = '';
        $id = $this->rset();
        $par = $this->parameters();
        $dt = $this->oai_pmh->ListIdentifiers_harvesting($id, $par);
        $sx .= '<div class="row">';
        $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">' . msg("Sections_journal") . '</div>';
        $sx .= $dt;

        $sx .= '</div>';
        return ($sx);
    }

    function sets()
    {
        $sx = '';
        $data = $this->oai_data();
        $dt = $this->oai_pmh->getListSets(0, $data);
        $sx .= '<div class="row">';
        $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">' . msg("Sections_journal") . '</div>';

        for ($r = 0; $r < count($dt); $r++) {
            $line = (array)$dt[$r];
            $setEsp = (string)$line['setSpec'];
            $setName = (string)$line['setName'];
            $sx .= '<div class="col-4 text-right small">' . $setEsp . '</div>';
            $sx .= '<div class="col-8"><b>' . $setName . '</b></div>';
            $this->update_sets($data['id_jnl'], $setEsp, $setName);
        }
        $sx .= '</div>';
        return ($sx);
    }

    function update_sets($id, $cod, $setName)
    {
        if (strpos($setName, 'Ã') > 0) {
            //$setName = utf8_decode($setName);
        }
        $sql = "select * from " . $this->base . "source_sets
        where sets_journal = $id and
        sets_session = '$cod'";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        if (count($rlt) == 0) {
            $sql = "insert into " . $this->base . "source_sets
            (sets_journal, sets_session, sets_name)
            values
            ('$id','$cod','$setName')";
            $rrr = $this->db->query($sql);
        }
    }

    function sources()
    {
        $sx = '';
        $par = $this->parameters();
        $sx = row3($par);
        return ($sx);
    }

    function source($d1, $d2)
    {
        $sx = '';
        $p = $this->parameters();
        $_SESSION['jnl_id'] = $d1;
        $dt = le($p['table'], 'id_jnl=' . $d1);
        $sx .= show($dt);
        return ($sx);
    }

    function identify()
    {
        //$this->load->model('sources');

        $id = $this->rset();
        $dt = $this->oai_pmh->identify($id);
        $sx = $this->journal_update($dt);
        return ($sx);
    }

    function journal_update($dt)
    {
        $sx = '';
        if (isset($dt['repositoryName'])) {
            $name = $dt['repositoryName'];
            $url_oai = $dt['baseURL'];
            $url = troca($url_oai, '/oai', '');
            $sx .= '<div class="row">';

            $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">' . msg("About_journal") . '</div>';
            $sx .= '<div class="col-2 text-right small">' . msg("journal_name") . '</div>';
            $sx .= '<div class="col-10"><b>' . $name . '</b></div>';

            $sx .= '<div class="col-2 text-right small">' . msg("journal_administrator") . '</div>';
            $sx .= '<div class="col-10"><b>' . $dt['adminEmail'] . '</b></div>';

            $sx .= '<div class="col-12 text-center" style="border-top: 1px solid #000; border-bottom: 1px solid #000;">' . msg("OAI Protocol") . '</div>';
            $var = array('protocolVersion', 'deletedRecord', 'granularity', 'baseURL');
            for ($r = 0; $r < count($var); $r++) {
                $sx .= '<div class="col-4 text-right small">' . msg($var[$r]) . '</div>';
                $sx .= '<div class="col-8"><b>' . msg($dt[$var[$r]]) . '</b></div>';
            }

            $sx .= '</div>';
        }
        $id = $dt['id_jnl'];
        $sql = "update " . $this->sources->table . "
        set jnl_oai_last_harvesting = '" . date("Y/m/d-H:i:s") . "',
        jnl_active = 1,
        jnl_name = '$name'
        where id_jnl = " . $id;
        $rrr = $this->db->query($sql);
        return ($sx);
    }

    function oai_data()
    {
        $data = array();
        if (isset($_SESSION['jnl_id'])) {
            $id = sround($_SESSION['jnl_id']);
            $data = $this->sources->le($id);
        } else {
            echo "OPS 554";
        }
        return ($data);
    }
    function rset($redirect = 0)
    {
        if (isset($_SESSION['jnl_id'])) {
            $id = $_SESSION['jnl_id'];
            return ($id);
        } else {
            if ($redirect == 1) {
                redirect(base_url(PATH . 'evaluation'));
            } else {
                return (0);
            }
        }
    }
    function set()
    {
        $form = new form;
        $cp = array();
        if (get("dd1") == '') {
            //$_POST['dd1'] = $this->rset();
        }
        array_push($cp, array('$H8', '', '', false, false));
        array_push($cp, array('$S100', '', msg('journal_url'), true, true));
        array_push($cp, array('$O 0:' . msg('no') . '&1:' . msg("yes"), '', msg('scielo_index'), true, true));
        $sx = $form->editar($cp, '');
        if ($form->saved > 0) {
            $url = get("dd1");
            while (substr($url, strlen($url) - 1, 1) == '/') {
                $url = substr($url, 0, strlen($url) - 1);
            }
            if (substr($url, strlen($url) - 6, 6) == '/index') {
                $url = substr($url, 0, strlen($url) - 6);
            } else {
                //echo substr($url,strlen($url)-6,5);
            }
            $url_oai = $url . '/oai';
            $sql = "select * from " . $this->sources->table . "
            where jnl_url = '$url'";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            if (count($rlt) == 0) {
                $xsql = "insert into " . $this->sources->table . "
                (jnl_name, jnl_url, jnl_url_oai)
                values
                ('no name yet','$url','$url_oai')";
                $rlt = $this->db->query($xsql);
                sleep(1);
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
            }
            $id = $rlt[0]['id_jnl'];
            $_SESSION['jnl_id'] = $id;
            redirect(base_url(PATH . 'evaluation/identify/'));
        }
        return ($sx);
    }

    function politics($id)
    {
        $sx = '';
        $id = $this->rset(1);
        $tp = array(
            'about/contact', 'about/editorialTeam', 'about/editorialPolicies',
            'about/submissions'
        );
        $dt = le($this->sources->table, 'id_jnl=' . $id);
        #authorGuidelines
        for ($r = 0; $r < count($tp); $r++) {
            $sql = "select * from " . $this->base . "source_politics
            where sp_journal = $id and sp_class = '" . $tp[$r] . "'";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $sx .= '<br>Verificando ' . $tp[$r];

            if (count($rlt) == 0) {
                $url = $dt['jnl_url'] . '/' . $tp[$r];
                $txt = file_get_contents($url);
                $txt = troca($txt, "'", "\'");
                $sql = "insert into " . $this->base . "source_politics
                (sp_journal, sp_class, sp_text)
                values
                ($id,'$tp[$r]','$txt')";
                $rrr = $this->db->query($sql);
                $sx .= '<span style="color: green;">INSERTERD</span>';
            } else {
                $sx .= '<span style="color: orange;">PASS</span>';
            }
        }
        $sx .= $this->politics_analyse($id);
        return ($sx);
    }

    function politics_analyse($id)
    {
        $sx = '';
        $sql = "select * from " . $this->base . "source_politics
        where sp_journal = $id";
        $rlt = $this->db->query($sql);
        $rlt = $rlt->result_array();
        for ($r = 0; $r < count($rlt); $r++) {
            $line = $rlt[$r];
            $tp = $line['sp_class'];
            $txt = $line['sp_text'];
            switch ($tp) {
                case 'about/contact':
                    $sx .= $this->politics_recover($txt, 'principalContact');
                    $sx .= $this->politics_recover($txt, 'supportContact');
                break;
                case 'about/editorialTeam':
                    $sx .= $this->politics_recover($txt, 'group');
                    #member - ANALISAR MELHOR
                    #editorialTeam h4
                    #editorialTeam
                    #
                break;
                case 'about/submissions':
                    $sx .= $this->politics_recover($txt, 'onlineSubmissions');
                    $sx .= $this->politics_recover($txt, 'authorGuidelines');
                    $sx .= $this->politics_recover($txt, 'submissionPreparationChecklist');
                    $sx .= $this->politics_recover($txt, 'copyrightNotice');
                    $sx .= $this->politics_recover($txt, 'privacyStatement');
                    $sx .= $this->politics_recover($txt, 'copyrightNotice');
                    $sx .= $this->politics_recover($txt, 'copyrightNotice');

                break;
                case 'about/editorialPolicies':
                    $sx .= $this->politics_recover($txt, 'focusAndScope');
                    $sx .= $this->politics_recover($txt, 'sectionPolicies');
                    $sx .= $this->politics_recover($txt, 'peerReviewProcess');
                    $sx .= $this->politics_recover($txt, 'publicationFrequency');
                    $sx .= $this->politics_recover($txt, 'openAccessPolicy');
                    $sx .= $this->politics_recover($txt, 'custom-0');
                    $sx .= $this->politics_recover($txt, 'custom-1');
                    $sx .= $this->politics_recover($txt, 'custom-2');
                    $sx .= $this->politics_recover($txt, 'custom-3');
                    $sx .= $this->politics_recover($txt, 'custom-4');
                    $sx .= $this->politics_recover($txt, 'custom-5');
                break;
            }
        }
        return ($sx);
    }

    function politics_recover($txt, $type)
    {
        $id = '<div id="' . $type . '">';
        $pos = strpos($txt, $id) + strlen($id);
        $txt = substr($txt, $pos, strlen($txt));
        $txt = substr($txt, 0, strpos($txt, '</div>'));
        $txt = html_entity_decode($txt);
        if (strpos($txt, '<script') > 0) {
            $txt = substr($txt, 0, strpos($txt, '<script')) . substr($txt, strpos($txt, '</scrip'), strlen($txt));
        }
        $txt = strip_tags($txt);
        return ($txt);
    }
    #supportContact
    #pageFooter

    function politics_authorGuidelines($txt)
    {
    }

    function version_software($jnl, $txt)
    {
        $version = $this->recover_metadata($txt, "generator", "content");
        $this->report_update($jnl, 'software', $version);
        return ($version);
    }

    function recover_metadata($txt, $name, $meta)
    {
        $str = 'name="' . $name . '" ' . $meta . '="';
        $pos = strpos($txt, $str);
        if ($pos > 0) {
            $t = substr($txt, $pos + strlen($str), strlen($txt));
            $t = substr($t, 0, strpos($t, '"'));
            return ($t);
        }
        return ('undefined');
    }

    function report_coauthor($id)
    {
        $dt_fim = date("Y");
        $dt_ini = $dt_fim - 20;

        $t = array();
        /**************************************************** Legendas */
        for($r=0;$r < ($dt_fim - $dt_ini);$r++)
        {
            $t['s'][$r] = $r+$dt_ini;
        }
        /**************************************************** Matriz */
        $t['eixo_y'] = 'Percentual (%)';
        $t['eixo_x'] = 'Ano';

        /* Series */
        for ($y = 1; $y <= 5; $y++) {
            /* Anos */
            for ($r = 0; $r <= ($dt_fim - $dt_ini); $r++)
            {
                if ($y==1)
                {
                    $label = $y .' '.msg('author_unique');
                } else {
                    if ($y >= 5)
                    {
                        $label = '+5 '.msg('authors');
                    } else {
                        $label = $y.' '.msg('authors');
                    }
                }
                $t['y'][$y] = $label;
                $t['x'][$y][$r] = 0;
            }
        }
        $sql = "select count(*) as total, authors, year
        from (
            select count(*) as authors, a_article, a_year as year
            from " . $this->base . "source_autores
            where a_jnl = $id
            and (a_year >= $dt_ini and a_year <= $dt_fim)
            group by a_article, year
            ) as tabela
            group by authors, year
            order by year, authors
            ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $year = $line['year'] - $dt_ini;
                $auths = $line['authors'];
                $total = $line['total'];

                if ($auths > 5) {
                    $auths = 5;
                }
                $t['x'][$auths][$year] = $t['x'][$auths][$year] + $total;
            }

            $hc = new highchart;
            $t['title'] = 'Tipos de autorias dos trabalhos (normalizado 100%) ('.$dt_ini.'-'.$dt_fim.')';

            /* Normalizar */
            for($y=0;$y < count($t['x'][1]);$y++)
            {
                $tt = 0;
                for ($r=1;$r < count($t['x']); $r++)
                {
                    $v = $t['x'][$r][$y];
                    $tt = $tt + $v;
                }
                if ($tt > 0)
                {
                    for ($r=1;$r <= count($t['x']); $r++)
                    {
                        $v = $t['x'][$r][$y];
                        $t['x'][$r][$y] = sround(($v/$tt)*100);
                    }
                }
            }
            //echo '<pre>'; print_r($t); echo '</pre>';
            $sx = $hc->show($t);
            return ($sx);
        }

        function report_update($jnl, $field, $text)
        {
            $sql = "select * from " . $this->base . "source_report
            where rp_jnl = $jnl and rp_field = '$field' ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            if (count($rlt) == 0) {
                $sql = "insert into " . $this->base . "source_report
                (rp_jnl, rp_field, rp_text)
                value
                ($jnl,'$field','$text')";
            } else {
                $sql = "update " . $this->base . "source_report
                set rp_text = '$text'
                where id_rp = " . $rlt[0]['id_rp'];
            }
            $rlt = $this->db->query($sql);
        }

        function report($id = 0)
        {
            $this->load->helper("highchart");
            if ($id == 0) {
                $id = $this->rset(1);
            }
            $sx = '';
            $sx .= $this->report_journal($id);
            $sx .= $this->report_publications($id);
            $sx .= $this->report_production($id);
            $sx .= $this->report_author($id);
            $sx .= $this->report_coauthor($id);
            $sx .= $this->report_keywords($id);
            return ($sx);
        }

        function report_journal($id)
        {
            $dt = le($this->sources->table, 'id_jnl=' . $id);
            $sx = '';
            $sx .= '<table class="table">';
            $sx .= '<tr><td width="10%">Publicação</td><td style="font-size: 200%;">' . $dt['jnl_name'] . '</td></tr>';
            $sx .= '<tr><td width="10%">ISSN:</td><td>' . $dt['jnl_issn'] . '</td></tr>';
            $sx .= '<tr><td width="10%">URL:</td><td ><a href="' . $dt['jnl_url'] . '" target="_new">' . $dt['jnl_url'] . '</a></td></tr>';
            $sx .= '</table>';
            return ($sx);
        }

        function report_author($id)
        {
            $dt_fim = date("Y") + 1;
            $dt_ini = $dt_fim - 10;
            $limit = 50;
            $sql = "select a_name, count(*) as total from " . $this->base . "source_autores
            where a_jnl = $id
            and (a_year >= $dt_ini and a_year <= $dt_fim)
            group by a_name
            order by total desc
            limit $limit
            ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            $sx = '<br><br>';
            $sx .= '<h2>' . msg('report_authors') . ' - TOP ' . $limit . ' (' . $dt_ini . '-' . $dt_fim . ')</h2>';
            $sx .= '<div class="row">';
            $sx .= '<div class="col-md-12" style="column-count: 2;">';
            $sx .= '<ol>';
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $sx .= '<li>' . $line['a_name'] . ' <sup>' . $line['total'] . ' publicações</sup></li>';
            }
            $sx .= '</ol>';
            $sx .= '</div></div>';
            return ($sx);
        }

        function report_keywords($id)
        {
            $dt_fim = date("Y") + 1;
            $dt_ini = $dt_fim - 10;
            $limit = 50;
            $sql = "select a_name, count(*) as total from " . $this->base . "source_subject
            where a_jnl = $id
            and (a_year >= $dt_ini and a_year <= $dt_fim)
            group by a_name
            order by total desc
            limit $limit
            ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            $sx = '<br><br>';
            $sx .= '<h2>' . msg('report_keywords') . ' - TOP ' . $limit . ' (' . $dt_ini . '-' . $dt_fim . ')</h2>';
            $sx .= '<div class="row">';
            $sx .= '<div class="col-md-12" style="column-count: 2;">';
            $sx .= '<ol>';
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $sx .= '<li>' . $line['a_name'] . ' <sup>' . $line['total'] . ' publicações</sup></li>';
            }
            $sx .= '</ol>';
            $sx .= '</div></div>';

            $sql = "select a_name, count(*) as total from " . $this->base . "source_subject
            where a_jnl = $id
            and (a_year >= $dt_ini and a_year <= $dt_fim)
            group by a_name
            order by total desc
            ";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();
            $data = array();
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $name = $line['a_name'];
                $name = substr($name, 0, strpos($name, '@'));
                $data[$name] = $line['total'];
            }
            $data['subject'] = $data;
            $sx .= '<br><br>';
            $sx .= '<h2>' . msg('report_keywords') . ' - WordCloud ' . $limit . ' (' . $dt_ini . '-' . $dt_fim . ')</h2>';
            $sx .= $this->load->view("brapci/cloud_tags_3", $data, true);
            return ($sx);
        }

        function report_publications($id)
        {
            $dt_fim = date("Y") + 1;
            $dt_ini = $dt_fim - 15;

            $sql = "select ar_year, ar_lang, count(*) as total
            from " . $this->base . "source_article
            where ar_jnl = $id and (ar_year >= $dt_ini and ar_year <= $dt_fim)
            group by ar_year, ar_lang
            order by ar_lang";
            $rlt = $this->db->query($sql);
            $rlt = $rlt->result_array();

            /* Zera os anos */
            $t = array();
            $sh = '<th>ano</th>';
            for ($r = $dt_ini; $r <= $dt_fim; $r++) {
                $t[$r] = 0;
                $sh .= '<th class="text-center">' . $r . '</th>';
            }
            $lang = array();
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                if (!isset($lang[$line['ar_lang']])) {
                    $lang[$line['ar_lang']] = $t;
                }
                $lang[$line['ar_lang']][$line['ar_year']] = $lang[$line['ar_lang']][$line['ar_year']] + $line['total'];
            }
            $sx = '';
            $sx .= '<br><br>';
            $sx .= '<h2>' . msg('report_title_language') . ' (' . $dt_ini . '-' . $dt_fim . ')</h2>';
            $sx .= '<table width="100%" border=1>';
            $sx .= '<tr>' . $sh . '</tr>';
            foreach ($lang as $l => $totais) {
                $sx .= '<tr>';
                $sx .= '<td>' . $l . '</td>';
                foreach ($totais as $year => $tot) {
                    $sx .= '<td align="center">' . $tot . '</td>';
                }
            }
            $sx .= '</table>';


            $sql = "select count(*) as total, ar_year
            from (
                select ar_article, ar_year
                from " . $this->base . "source_article
                where ar_jnl = $id and
                (ar_year >= $dt_ini and ar_year <= $dt_fim)
                group by ar_article, ar_year
                ) as table1
                group by ar_year
                order by ar_year";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $sx .= '<br><br>';
                $sx .= '<h2>' . msg('report_publications') . ' (' . $dt_ini . '-' . $dt_fim . ')</h2>';
                $sx .= '<table>';
                $sx1 = '';
                $sx2 = '';
                $sx3 = '';

                $max = 10;
                for ($r = 0; $r < count($rlt); $r++) {
                    if ($rlt[$r]['total'] > $max) {
                        $max = $rlt[$r]['total'] * 1.1;
                    }
                }
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $sz = sround(200 * ($line['total'] / $max));
                    $bar = '<img src="' . base_url('img/point/point_blue.png') . '" style="height: ' . $sz . 'px; width: 100%;">';
                    $sx1 .= '<td width="5%" valign="bottom">' . $bar . '</td>';
                    $sx2 .= '<td width="5%" valign="bottom">' . $line['total'] . '</td>';
                    $sx3 .= '<td width="5%">' . $line['ar_year'] . '</td>';
                }
                $sx .= '<tr>' . $sx1 . '</tr>';
                $sx .= '<tr align="center">' . $sx2 . '</tr>';
                $sx .= '<tr align="center">' . $sx3 . '</tr>';
                $sx .= '</table>';
                return ($sx);
            }

            function report_production($id)
            {
                $dt_fim = date("Y") + 1;
                $dt_ini = $dt_fim - 10;
                $sql = "select sets_name, year(li_datestamp) as year,
                month(li_datestamp) as month,
                count(*) as total, li_setSpec
                from " . $this->base . "source_listidentifier
                left join " . $this->base . "source_sets ON li_setSpec = sets_session and sets_journal = $id
                where li_jnl = $id and li_status = 'active'
                group by sets_name, year, month, li_setSpec
                order by li_setSpec, year";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();

                $t = array();
                $sec = array();
                for ($r = $dt_ini; $r <= $dt_fim; $r++) {
                    for ($y = 0; $y <= 12; $y++) {
                        $t[$r][$y] = 0;
                    }
                }

                /*************************************** DATA DE PUBLICAÇÃO NO SISTEMA ***/
                $max = 10;
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $year = $line['year'];
                    $month = $line['month'];
                    $section = trim($line['sets_name']) . ' [' . $line['li_setSpec'] . ']';
                    if (($year >= $dt_ini) and ($year < $dt_fim)) {
                        $t[$year][$month] = $t[$year][$month] + $line['total'];
                        if ($t[$year][$month] > $max) {
                            $max = ($t[$year][$month] * 1.1);
                        }
                    }

                    if (isset($sec[$section])) {
                        $sec[$section] = $sec[$section] + $line['total'];
                    } else {
                        $sec[$section] = $line['total'];
                    }
                }

                $sx = '<br><br>';
                $sx .= '<h2>' . msg('report_distribution') . '</h2>';
                $sx .= '<div class="row">';
                $sx .= '<div class="col-md-12">';
                $sx .= '<table width="100%" border=1>';
                $sx .= '<tr ><th width="6%"class="text-center">Total</th><th class="text-left">Section</th>';
                asort($sec);
                $st = '';
                $tot = 0;
                foreach ($sec as $section => $total) {
                    $tot = $tot + $total;
                    $st = '<tr><td class="text-center">' . $total . '</td><td>&nbsp;' . $section . '</td></tr>' . cr() . $st;
                }
                $st .= '<tr><th class="text-center">' . $tot . '</th><th>&nbsp;Total</th></tr>' . cr();
                $st = troca($st, '[', '<sup>');
                $st = troca($st, ']', '</sup>');
                $sx .= $st;
                $sx .= '</table>';
                $sx .= '</div></div>';

                /***********************************************/
                $sx .= '<br><br>';
                $sx .= '<h2>' . msg('report_distribution_month') . '</h2>';
                $sx .= '<table border=1 width="100%">';
                $sx .= '<tr><th>' . msg('year') . '</th>';
                for ($r = 1; $r <= 12; $r++) {
                    $sx .= '<th class="text-center small">' . meses($r) . '</th>';
                }

                foreach ($t as $year => $totais) {
                    $sx .= '<tr>';
                    $sx .= '<td width="4%">' . $year . '</td>';
                    for ($r = 1; $r <= 12; $r++) {
                        $bg = '';
                        if ($totais[$r] > 0) {
                            $bg = 'style="background: #ddffdd;"';
                        }
                        $sx .= '<td width="8%" align="center" ' . $bg . '>' . $totais[$r] . '</td>';
                    }
                    $sx .= '</tr>';
                }
                $sx .= '</table>';

                return ($sx);
            }
            function getRecord($id = 0)
            {
                $sx = '';
                $ds = array();
                if ($id == 0) {
                    $id = $this->rset();
                }

                $data = le($this->sources->table, 'id_jnl=' . $id);
                $sql = "select * from " . $this->base . "source_listidentifier
                where li_jnl = $id and li_s = 1 and li_status = 'active'
                order by li_update desc
                limit 20";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                $sx .= '<ol>';
                for ($r = 0; $r < count($rlt); $r++) {
                    $line = $rlt[$r];
                    $idh = $line['id_li'];
                    $ds = $this->oai_pmh->getRecord_oai_dc($idh, $data);
                    $sx .= '<li>Process ' . $line['li_identifier'] . '</li>';
                    $year = $ds['issue']['year'];
                    if ($year == '[????]') {
                        $year = substr($ds['date'][0], 0, 4);
                    }

                    /* title */
                    if (isset($ds['title'])) {
                        for ($y = 0; $y < count($ds['title']); $y++) {
                            $name = $ds['title'][$y]['title'];
                            $lang = $ds['title'][$y]['lang'];
                            $name = troca($name, "'", '´');
                            $this->title_update($idh, $name, $lang, $year, $id);
                        }
                    }

                    /* Autores */
                    if (isset($ds['authors'])) {
                        for ($y = 0; $y < count($ds['authors']); $y++) {
                            $name = nbr_author(strtolower($ds['authors'][$y]['name']), 7);
                            $name = troca($name, "'", '´');
                            $this->authors_update($idh, $name, $year, $id);
                        }
                    }

                    /* Subject */
                    if (isset($ds['subject'])) {
                        for ($y = 0; $y < count($ds['subject']); $y++) {
                            $name = nbr_author(strtolower($ds['subject'][$y]), 7);
                            $name = troca($name, "'", '´');
                            $this->subject_update($idh, $name, $year, $id);
                        }
                    }
                    $sql = "update " . $this->base . "source_listidentifier
                    set li_s = 2
                    where id_li = $idh";
                    $rrr = $this->db->query($sql);
                }
                $sx .= '</ol>';
                if (strlen($sx) > 10) {
                    $url = base_url(PATH . 'evaluation/getrecord');
                    $sx .= '<meta http-equiv="refresh" content="1; URL=' . $url . '"/>';
                }
                return ($sx);
            }

            function authors_update($article, $author, $year, $journal)
            {
                $sql = "select * from " . $this->base . "source_autores
                where a_article = $article
                and a_name = '$author'";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                if (count($rlt) == 0) {
                    $sql = "insert into " . $this->base . "source_autores
                    (a_name, a_jnl, a_article, a_year)
                    values
                    ('$author',$journal,$article,$year);";
                    $rlt = $this->db->query($sql);
                }
            }
            function subject_update($article, $subject, $year, $journal)
            {
                $sql = "select * from " . $this->base . "source_subject
                where a_article = $article
                and a_name = '$subject'";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                if (count($rlt) == 0) {
                    $sql = "insert into " . $this->base . "source_subject
                    (a_name, a_jnl, a_article, a_year)
                    values
                    ('$subject',$journal,$article,$year);";
                    $rlt = $this->db->query($sql);
                }
            }
            function title_update($article, $title, $lang, $year, $journal)
            {
                $sql = "select * from " . $this->base . "source_article
                where ar_article = $article
                and ar_title = '$title'
                and ar_lang = '$lang'";
                $rlt = $this->db->query($sql);
                $rlt = $rlt->result_array();
                if (count($rlt) == 0) {
                    $sql = "insert into " . $this->base . "source_article
                    (ar_title, ar_jnl, ar_article, ar_year, ar_lang)
                    values
                    ('$title',$journal,$article,$year,'$lang');";
                    $rlt = $this->db->query($sql);
                }
            }
        }
