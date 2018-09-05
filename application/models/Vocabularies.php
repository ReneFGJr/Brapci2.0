<?php
class vocabularies extends CI_model {
    function modal_th($id = '') {
        $sx = '';
        if (perfil("#ADM")) {
            $dta = $this -> frbr_core -> le_class($id);
            $sx .= '
                    <a href="' . base_url(PATH . 'vocabulary_ed/' . $dta['id_c']) . '" class="btn btn-secondary">Editar</a>
                    ';
        }
        if (strlen($id) > 0) {
            $sx .= '
                    <!-- Button trigger modal -->
                    <a href="' . base_url(PATH . 'vocabulary') . '" class="btn btn-secondary">Voltar</a>
                    ';
            if (isset($dta['c_url']) and (strlen($dta['c_url']) > 10)) {
                $sx .= '
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
                      ' . msg('update_vocabulary') . '
                    </button>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Importação de Vocabulários</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" id="cnt">
                                <span style="font-size:75%">Aguardando comando!</span>                                                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <input type="submit" id="dd50" class="btn btn-primary" value="Atualizar">
                          </div>
                        </div>
                      </div>
                    </div>';
                $sx .= '
                    <script>
                        jQuery("#dd50").click(function() {
                            jQuery("#cnt").html("Buscando...");
                            $.ajax({
                                method: "POST",
                                url: "' . base_url(PATH . 'ajax/inport/' . $id) . '",
                                data: { name: "John", location: "Boston" }
                                })
                                .done(function( msg ) {
                                    jQuery("#cnt").html(msg);
                                });
                            });
                    </script>
                    ';
            }
        }
        return ($sx);
    }

    function modal_vc($id = '') {
        $sx = '';
        if (strlen($id) > 0) {
            $sx .= '
                    <!-- Button trigger modal -->
                    <form method="post" action="' . base_url(PATH . 'vocabulary/' . $id) . '">
                    <a href="' . base_url(PATH . 'vocabulary') . '" class="btn btn-secondary">Voltar</a>' . cr();
            if (perfil("#ADM")) {
                $dta = $this -> frbr -> le_class($id);
                $sx .= '
                    <a href="' . base_url(PATH . 'vocabulary_ed/' . $dta['id_c']) . '" class="btn btn-secondary">Editar</a>
                    ';
            }
            $sx .= '
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
                      Inserir novo termo
                    </button>
                    
                    
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Vocabulário controlado</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                                <span style="font-size:75%">Termo</span>
                                <input type="text" name="dd1" value="" class="form-control">                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <input type="submit" class="btn btn-primary" value="Gravar >>>">
                          </div>
                        </div>
                      </div>
                    </div>
                    </form>';
        }
        return ($sx);
    }

    function list_vc_attr($id = '') {
        $sql = "
            SELECT d_r1 as id_n, n_name as n_name FROM `rdf_data`
                left JOIN rdf_concept ON d_r1 = id_cc 
                INNER JOIN rdf_name ON cc_pref_term = id_n
                where d_r2 = $id";
        $rlt = $this -> db -> query($sql);
        $rlt = $rlt -> result_array();
        return ($rlt);
    }

    function list_vc_type($id = '') {
        $ln = $this -> frbr -> data_classes($id);
        return ($ln);
    }

    function list_vc($id = '') {
        $sx = '';
        /********************************************/
        if (strlen($id) == 0) {
            $sql = "select * from rdf_class 
                            WHERE c_type = 'C' and (c_vc = 1 or c_vc <> 1) 
                            ORDER BY c_class ";
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            $sx = '<ul>';
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $link = '<a href="' . base_url(PATH . 'thesa/' . $line['c_class']) . '">';
                $linka = '</a>';
                $sx .= '<li>' . $link . msg($line['c_class']) . $linka . '</li>';
            }
            $sx .= '</ul>';
        } else {
            $ln = $this -> frbr_core -> data_classes($id);
            $sx = '<ul>';
            for ($r = 0; $r < count($ln); $r++) {
                $l = $ln[$r];
                $link = '<a href="' . base_url(PATH . 'v/' . $l['id_cc']) . '">';
                $linka = '</a>';
                $sx .= '<li>' . $link . $l['n_name'] . $linka . '</li>';
            }
            $sx .= '</ul>';
            return ($sx);
        }
        return ($sx);
    }

    function list_thesa($id = '') {
        $sx = '';
        /********************************************/
        if (strlen($id) == 0) {
            $sql = "select * from rdf_class 
                            WHERE c_type = 'C' 
                                    and c_vc = 0 and c_url <> '' 
                            ORDER BY c_class ";
            $rlt = $this -> db -> query($sql);
            $rlt = $rlt -> result_array();
            $sx = '<ul>';
            for ($r = 0; $r < count($rlt); $r++) {
                $line = $rlt[$r];
                $up = stodbr($line['c_url_update']);
                $link = '<a href="' . base_url(PATH . 'thesa/' . $line['c_class']) . '">';
                $linka = '</a>';
                $sx .= '<li>' . $link . msg($line['c_class']) . $linka . ' ' . $up . '</li>';
            }
            $sx .= '</ul>';
        } else {
            $ln = $this -> frbr -> data_classes($id);
            $sx = '<ul>';
            for ($r = 0; $r < count($ln); $r++) {
                $l = $ln[$r];
                $link = '<a href="' . base_url(PATH . 'v/' . $l['id_cc']) . '">';
                $linka = '</a>';
                $sx .= '<li>' . $link . $l['n_name'] . $linka . '</li>';
            }
            $sx .= '</ul>';
            return ($sx);
        }
        return ($sx);
    }

}
?>
