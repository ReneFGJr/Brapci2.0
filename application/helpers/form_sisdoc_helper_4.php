<?php
/**
* CodeIgniter Form Helpers
*
* @package     CodeIgniter
* @subpackage  Helpers
* @category    Helpers
* @author      Rene F. Gabriel Junior <renefgj@gmail.com>
* @link        http://www.sisdoc.com.br/CodIgniter
* @version     v0.21.04.12
*/

function cep($cep)
    {
        $cep = sonumero($cep);
        while (strlen($cep) < 8) { $cep = '0'.$cep; }

        //https://viacep.com.br/ws/90660900/json/
        $http = 'https://viacep.com.br/ws/'.$cep.'/json/';
        $rlt = load_page($http);
        return($rlt['content']);
    }

function btn_reload()
    {
        $sx = '<button class="btn btn-outline-primary" onClick="window.location.reload();">'.msg('Refresh Page').'</button>';
        return($sx);
    }
function row3($p=array())
{
    $CI = &get_instance();
    $table = $p['table'];
    $show = $p['fields'];
    
    $sql = "select * from ".$table;
    $rst =$CI->db->query($sql);
    $rlt = $rst->result_array();
        
    $sx = '';
    $hr = '';
    $hrs = '';
    $link = '';
    $linka = '';
    /*************** ID field */
    $idf = key($rlt[0]);
    /*************** Header ***/
    
    for ($r=0;$r < count($rlt);$r++)
    {
        $line = $rlt[$r];
        $sx .= '<tr>';
        $mst = 0;
        if (isset($p['page_view']))
            {
                $link = '<a href="'.trim($p['page_view']).'/'.$line[$idf].'/'.checkpost_link($line[$idf]).'">';
                $linka = '</a>';
            }
        foreach($line as $field=>$vlr)
        {   
            if ((isset($show[$mst])) and ($show[$mst] != '') and ($show[$mst] != '0'))
            {
                $sx .= '<td>';
                $sx .= $link.$vlr.$linka;
                $sx .= '</td>';
                
                if ($r==0)
                {
                    $hr .= '<th width="'.sonumero($show[$mst]).'%">';
                    $hr .= msg($field);
                    $hr .= '</th>';
                    
                    $hrs .= '<th>';
                    $hrs .= '<input type="text" id="txtColuna'.$mst.'"/>';
                    $hrs .= '</th>';
                }
            }
            $mst++;
        }
        $sx .= '</tr>';
    }
    /****************************************** header */
    $hr = '<thead class="fixedHeader">
    <tr>'.$hr.'</tr>
    <tr>'.$hrs.'</tr>
    </thead>';
    $sx = '<div id="divConteudo"><table id="tabela">'.$hr.$sx.'</table></div>';
    /********************************************* JS */
    $js = '
    <script>
    $(function(){
        $("#tabela input").keyup(function(){       
            var index = $(this).parent().index();
            var nth = "#tabela td:nth-child("+(index+1).toString()+")";
            var valor = $(this).val().toUpperCase();
            $("#tabela tbody tr").show();
            $(nth).each(function(){
                if($(this).text().toUpperCase().indexOf(valor) < 0){
                    $(this).parent().hide();
                }
            });
        });
        
        $("#tabela input").blur(function(){
            $(this).val("");
        });
    });
    
    function AdicionarFiltro(tabela, coluna) {
        var cols = $("#" + tabela + " thead tr:first-child th").length;
        if ($("#" + tabela + " thead tr").length == 1) {
            var linhaFiltro = "<tr>";
            for (var i = 0; i < cols; i++) {
                linhaFiltro += "<th></th>";
            }
            linhaFiltro += "</tr>";
            
            $("#" + tabela + " thead").append(linhaFiltro);
        }
        
        var colFiltrar = $("#" + tabela + " thead tr:nth-child(2) th:nth-child(" + coluna + ")");
        
        $(colFiltrar).html("<select id=\'filtroColuna_" + coluna.toString() + "\'  class=\'filtroColuna\'> </select>");
        
        var valores = new Array();
        
        $("#" + tabela + " tbody tr").each(function () {
            var txt = $(this).children("td:nth-child(" + coluna + ")").text();
            if (valores.indexOf(txt) < 0) {
                valores.push(txt);
            }
        });
        $("#filtroColuna_" + coluna.toString()).append("<option>'.msg('all').'</option>")
        for (elemento in valores) {
            $("#filtroColuna_" + coluna.toString()).append("<option>" + valores[elemento] + "</option>");
        }
        
        $("#filtroColuna_" + coluna.toString()).change(function () {
            var filtro = $(this).val();
            $("#" + tabela + " tbody tr").show();
            if (filtro != "TODOS") {
                $("#" + tabela + " tbody tr").each(function () {
                    var txt = $(this).children("td:nth-child(" + coluna + ")").text();
                    if (txt != filtro) {
                        $(this).hide();
                    }
                });
            }
        });
        
    };
    AdicionarFiltro("divConteudo", 2);
    </script>    
    ';
    return($sx.$js);    
}

function row($obj, $pag = 1) {
    $page = page();
    $npag = $pag;
    $field = 1;
    
    if ($obj->tabela == '')
    {
        $sx = message('ERRO - Propriety "tabela" not informed',3);
        return($sx);
    }
    $acao = trim(get('acao'));
    /* Zera paginacao em nova consulta */
    if (get('acao') == msg('bt_search')) {
        $pag = 1;
        $npag = 1;
    }
    $start = round($pag);
    $offset = (integer)$obj -> offset;
    $pag .= get("pag");
    $pag = round($pag);
    $start = $pag * $offset;
    $CI = &get_instance();
    
    /* Dados do objeto */
    $fd = $obj -> fd;
    $mk = $obj -> mk;
    $lb = $obj -> lb;
    
    /*********************** Link para editar */
    if (($obj->edit == true) and (strlen($obj->row_edit)==0))
    {
        $lk = base_url(PATH.'admin/languages/ed/');
        $obj->row_edit = $lk;
    }        
    
    /************************* Registros da tabela não mostrados */
    if (!isset($fd[0]))
    {
        $sql = "DESC ".$obj->tabela." ";
        $rrr = $CI->db->query($sql);
        $rrr = $rrr->result_array();
        for ($r=0;$r < count($rrr);$r++)
        {
            $fld = $rrr[$r]['Field'];
            $fd[$r] = $fld;
            $lb[$r] = $fld;
            $mk[$r] = 'L';
            
        }
        $obj -> fd = $fd;
    }        
    
    /* BOTA NOVO */
    if ($acao == mst('bt_new')) {
        redirect($obj -> row_edit . '/0/0');
        exit ;
    }
    
    /* FILTRO */
    if ($acao == msg('bt_clear')) {
        $CI -> session -> userdata['rt_' . $page] = '';
        $CI -> session -> userdata['rf_' . $page] = '';
        $CI -> session -> userdata['rp_' . $page] = '';
    }
    $term = '';
    
    /* se postado recupera termos */
    if (strlen(get('dd1'))) 
    {
        if (strlen(get('dd2')) > 0)
        { 
            $acao = get('dd2');
        }
        if (strlen(get('dd1')) > 0) 
        { 
            $term = get('dd1');
        }
        $term = troca($term, "'", "´");
    }
    /********** Campo de busca  */
    if (strlen(get('dd5'))) {
        $field = round(get('dd5'));
    }
    /********** Se não setado, prioridade no primeiros */
    if ($field < 1) 
    { 
        $field = 1;
    }
    /***************** Coloca no último de indicador for maior que total de campos */
    if ($field >= count($fd)) 
    { 
        $field = count($fd) - 1;
    }
    
    /* parametros */
    $edit = $obj -> edit;
    $see = $obj -> see;
    $novo = $obj -> novo;
    
    /* campo ID */
    $fld = $fd[0];
    
    /* Cabecalho da Tabela */
    $sh = '<thead><tr>';
    for ($r = 1; $r < count($fd); $r++) {
        $label = $lb[$r];
        $sh .= '<th>' . $label . '</th>';
        /* campos da consulta */
        $fld .= ', ' . $fd[$r];
    }
    if ($obj -> edit == True) {
        $sh .= '<th>' . msg('action') . '</th>';
    }
    $sh .= '</tr></thead>';
    
    /* Recupera dados */
    $tabela = $obj -> tabela;
    $CI = &get_instance();
    
    /* SEM ACAO REGISTRADA */
    if (strlen($acao) == 0) {
        /* recupera dados da memoria */
        if (isset($_SESSION['rt_' . $page])) {
            $term = $_SESSION['rt_' . $page];
            $npage = round($_SESSION['rp_' . $page]);
            $field = round($_SESSION['rf_' . $page]);
        } else {
            $term = '';
        }
    }
    /************ Botao limpar buscas */
    if (strlen(get('acao')) > 0) {
        if (get('acao') == msg('bt_clear')) {
            $term = '';
            $CI -> session -> userdata['rt_' . $page] = '';
            $CI -> session -> userdata['rp_' . $page] = '';
            $CI -> session -> userdata['rf_' . $page] = '';
            redirect($obj -> row);
        }
    }
    
    /* Memoria */
    $termo = $term;
    /* Where */
    if (strlen($term) > 0) 
    {
        if (strlen(get('dd5')) > 0) 
        {
            $field = get('dd5');
        } else {
            if (!isset($_SESSION['rf_' . $page]))
            {
                $_SESSION['rf_' . $page] = '0';
            }
            $field = round($_SESSION['rf_' . $page]);
            if ($field <= 1) { $field = 1;
            }
        }
        
        /* Dados para consulta */
        $newdata = array('rt_' . $page => $termo, 'rf_' . $page => $field, 'rp_' . $page => $npag);
        $CI -> session -> set_userdata($newdata);
        
        $term = troca($term, ' ', ';');
        $term = splitx(';', $term);
        
        $wh = '';
        for ($rt = 0; $rt < count($term); $rt++) 
        {
            if (strlen($wh) > 0) 
            { 
                $wh .= ' and ';
            }
            $wh .= ' (' . $fd[$field] . " like '%" . $term[$rt] . "%') ";
        }
        $wh = ' where ' . $wh;
    } else {
        $wh = '';
    }
    
    /* PRE WHERE */
    if ((isset($obj -> pre_where)) and (strlen($obj -> pre_where) > 0)) {
        if (strlen($wh) == 0) {
            $wh .= ' where ' . $wh;
        } else {
            $wh .= ' AND ';
        }
        $wh .= ' (' . $obj -> pre_where . ')';
    }
    
    if (strlen($acao) > 0) {
        $pag = 1;
    }
    
    /* total de registros */
    $sql = "select count(*) as total from " . $tabela . " $wh ";
    $query = $CI -> db -> query($sql);
    $row = $query -> row();
    $total = $row -> total;
    
    /* mostra */
    $start_c = ($start - $offset);
    if ($start_c < 1) { $start_c = 0;
    }
    
    $sql = "select $fld from " . $tabela . ' ' . $wh;
    
    /* PRE WHERE */
    if ((isset($obj -> pre_where)) and (strlen($obj -> pre_where) > 0)) {
        $wh .= ' AND (' . $obj -> pre_where . ')';
    }
    if (strlen($obj -> order) > 0) {
        $sql .= " order by " . $obj -> order;
    } else {
        $sql .= " order by " . $fd[1];
    }
    
    $sql .= " limit " . $start_c . " , " . $offset;
    $query = $CI -> db -> query($sql);
    $data = '';
    
    /* Metodo de chamada */
    $url_pre = uri_string();
    $url_pre = substr($url_pre, 0, strpos($url_pre, '/')) . '/view';
    
    $url_pre = $obj -> row_view;
    
    /* PRE */
    $active = 0;
    for ($r = 0; $r < count($mk); $r++) 
    {
        if ($mk[$r] == 'A') 
        {
            $active = $r;
        }
    }
    
    foreach ($query->result_array() as $row) {
        /* recupera ID */
        $flds = trim($fd[0]);
        $id = $row[$flds];
        
        /* mostra resultado da query */
        $style = '';
        if ($active > 0) {
            $flds = trim($fd[$active]);
            if ($row[$flds] == 0) {
                $style = ' style="color: #ff0000;" ';
            }
        }
        $data .= '<tr>';
        for ($r = 1; $r < count($fd); $r++) {
            /* mascara */
            $flds = trim($fd[$r]);
            if (isset($mk[$r])) {
                $msk = trim($mk[$r]);
            } else {
                $msk = 'L';
            }
            $mskm = '';
            switch($msk) {
                case 'C' :
                    $mskm = ' align="center" ';
                break;
                case 'L' :
                    $mskm = ' align="left" ';
                break;
                case 'R' :
                    $mskm = ' align="right" ';
                break;
                case 'A' :
                    $mskm = ' align="center" ';
                    if ($row[$flds] == '0') {
                        $row[$flds] = '<font color="red">Inativo</font>';
                    } else {
                        $row[$flds] = '<font color="green">Ativo</font>';
                    }
                    
                break;
            }
            
            /* see */
            if ($see == TRUE) {
                $link = '<A HREF="' . $obj -> row_view . '/' . $id . '/' . checkpost_link($id) . '">';
                $linkf = '</A>';
            } else {
                $link = '';
                $linkf = '';
            }
            $data .= '<td ' . $mskm . '>' . $link . '<font ' . $style . '>' . trim($row[$flds]) . '</font>' . $linkf . '</td>';
        }
        if ($obj -> edit == True) {
            $idr = trim($row[$fd[0]]);
            $data .= '<td width="1%" align="center"><A HREF="' . $obj -> row_edit . '/' . $idr . '/' . checkpost_link($idr) . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true">[ed]</span></td>';
        }
        $data .= '</tr>' . chr(13) . chr(10);
    }
    
    /* Tela completa */
    $tela = '<table width="100%" class="table" id="row">';
    $tela .= $sh;
    $tela .= $data;
    $tela .= '<tr><th colspan=10 align="left">Total ' . $total . ' de registros' . '</th></tr>';
    $tela .= '</table>';
    
    $total_page = (int)($total / $offset) + 1;
    $obj -> term = $termo;
    $obj -> npag = $npag;
    $obj -> field = $field;
    
    $pags = npag($obj, $pag, $total_page, $offset);
    
    return ($pags . $tela);
}

function row2($par=array())
{
    $CI = &get_instance();
    if ((count($par) == 0) or (!is_array($par)))
    {
        $sx = '<pre>'.cr();;
        $sx .= 'table = &lt;nome da tabela&gt;'.cr();
        $sx .= 'type = &lt;tipo de visualização&gt; ex: 0,1,2,...'.cr();
        $sx .= 'cp = Array com o campo das tabelas';
        $sx .= '</pre>'.cr();
        return($sx);
    }
    
    /************************************************** View *********/
    if (isset($par['id']))
    {
        if (!isset($par['order']))
        {
            $par['order'] = $par['cp'][0][1];
        }
        $sql = "select * from ".$par['table'].' where '.$par['cp'][0][1].' = '.$par['id'];
        $sql .= ' order by '.$par['order'];
        
        $rlt = $CI->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<!-- Classe de produtos -->'.cr();
        $sx .= '<div class="col-md-12">';
        $sx .= '<h1>'.msg('Table').': '.$par['table'].'</h1>'.cr();
        $sx .= '<table id="fields" class="table">'.cr();
        $sx .= '<tr><th>Field</th></th>Value</th></tr>'.cr();
        $i = 0;
        foreach ($rlt[0] as $key => $value) {
            $i++;
            $sx .= '<tr>';
            
            $sx .= '<td align="right">';
            $sx .= (string)$key;
            $sx .= '</td>';
            $sx .= '<td><b>';
            $sx .= (string)$value;
            $sx .= '</b></td>';
            $sx .= '</tr>'.cr();
        }
        $sx .= '</table>'.cr();
        $sx .= '</div>';
        return($sx);
    }        
    
    
    /************************************************** Row **********/
    if (isset($par['cp']))        
    {
        $pag = round(get("pag"));
        $order = get("order");
        $filt = get("filter");
        $filter = '';
        if ((strlen($order) == 0) and (count($par['cp']) > 0)) 
        { 
            $order = 'order by '.(string)$par['cp'][1][1]; 
        }
        $cp = $par['cp'];
        $limit = 'limit 50';
        $cps = '';
        foreach ($cp as $key => $value) {
            if ((strlen($value[5]) > 0) or ($cps == ''))
            {
                if (strlen($cps) > 0) 
                { 
                    $cps .= ', '; 
                    if (strlen($filt) > 0) 
                    { 
                        $filter .= ' OR '; 
                    }
                }
                $cps .= $value[1];
                if (strlen($filt) > 0)
                {
                    $filter .= '('.$value[1] .' like \'%'.$filt.'%\')';
                }
            }
        }
        /* Caso a tabela esteja vazia */
        if (count($cp) == 0)
        {
            $cps = '*';
        }
        /*************************** QUERY *****************/
        if (strlen($filter) > 0)
        { 
            $where = ' where ('.$filter.')'; 
        } else {
            $where = '';
        }
        
        if (isset($par['where']))
        {
            if (strlen($where) > 0) 
            {
                $where .= ' AND ';
            } else {
                $where = ' where ';
            }
            $where .= ' ('.$par['where'].')';
        }
        
        $sql = "select $cps 
        from ".$par['table']."
        $where 
        $order
        $limit ";
        
        
        /************************* EXECUTA QUERY *************/
        $rlt = $CI->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<table class="table">';
        /* Filter */
        $sx .= '<tr>';
        $sx .= '<td colspan=10><form method="get">
        <div class="input-group mb-3">
        <input type="text" name="filter" class="form-control" placeholder="'.msg("name_to_filter").'" aria-label="'.msg("name_to_filter").'" aria-describedby="basic-addon2">
        <div class="input-group-append">
        <input type="submit" value="'.msg("bt_filter").'" class="input-group-text" id="basic-addon2">
        </div>
        
        <div class="input-group-append">
        <a href="'.$par['path'].'edit/0'.'"class="input-group-text" id="basic-addon3">Novo Registro</a>
        </div>
        
        </div>
        </form></td>';
        $sx .= '</tr>';
        /* header */
        $sx .= '<tr>';
        foreach ($cp as $key => $value) {   
            if ($value[5] == true)                 
            {
                $cps = trim((string)$value[1]);
                $sx .= '<th>'.msg($cps).'</th>';
            }
        }
        $sx .= '</tr>';
        /* Datas */            
        for ($r=0;$r < count($rlt);$r++)
        {
            $line = $rlt[$r];
            $link = '<a href="'.$par['path'].'view/'.$line[$cp[0][1]].'">';
            $linka = '</a>';
            $sx .= '<tr>';
            
            foreach ($cp as $key => $value) {   
                if ($value[5] == true)                 
                {                    
                    $value = $line[$value[1]];
                    $sx .= '<td>'.$link.$value.$linka.'</td>';                   
                }
            }
            $sx .= '</tr>';
            
        }
        $sx .= '</table>';
        return($sx);
    }
    
    /************* Sem os campos *******************************************/
    if (!isset($par['cp']))
    {
        $sql = "select * from ".$par['table'].' limit 1';
        $rlt = $CI->db->query($sql);
        $rlt = $rlt->result_array();
        $sx = '<!-- Classe de produtos -->'.cr();
        $sx .= '<div class="col-md-12">';
        $sx .= '<h1>'.msg('Table').': '.$par['table'].'</h1>'.cr();
        $sx .= '<table id="fields" class="table">'.cr();
        $sx .= '<tr><th>Field</th></th>Value</th></tr>'.cr();
        $i = 0;
        foreach ($rlt[0] as $key => $value) {
            $i++;
            $sx .= '<tr>';
            
            $sx .= '<td>'.$i.'</td>';
            $sx .= '<td>';
            $sx .= (string)$key;
            $sx .= '</td>';
            $sx .= '<td>';
            $sx .= (string)$value;
            $sx .= '</td>';
            
            $sx .= '<td>';
            
            if ($i == 1) {
                $sx .= 'array_push($cp,array(\'$H8\',"'.$key.'","'.msg($key).'",True,True,True)); ';
            } else {
                $sx .= 'array_push($cp,array(\'$S100\',"'.$key.'","'.msg($key).'",True,True,True)); ';
            }
            $sx .= '</td>';
            
            $sx .= '</tr>'.cr();
        }
        $sx .= '</table>'.cr();
        $sx .= '</div>';
        return($sx);
    }
}


function array2csv($rlt,$sep=',',$uft8=true) {
    $header = '';
    $csv = '';
    for ($r=0;$r < count($rlt);$r++)
    {
        $line = $rlt[$r];
        $ln = '';
        foreach($line as $key => $value) {
            $sepa = '';
            if (strlen($ln) > 0)
                {
                    $sepa .= $sep;
                }
            if ($r==0) { $header .= $sepa.$key; }
            $ln .= $sepa.'"'.$value.'"';
        }        
        $csv .= $ln.cr();
    }
    $csv =  $header.cr().$csv;
    if ($uft8==false)
        {
            $csv = utf8_decode($csv);
        }
    return($csv);
}