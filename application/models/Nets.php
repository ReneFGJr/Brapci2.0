<?php
class nets extends CI_model
    {
    function pinterest($data)
        {
            $d = $this->dados($data);
			$nm = $d['title'].'. '.$d['autor_resumido'].' '.$d['http'];
			$nm = urlencode($nm);
            $url = 'https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href='.$nm.'&picture=&title=Aspectos%20%C3%A9ticos%20da%20coautoria%20em%20publica%C3%A7%C3%B5es%20cient%C3%ADficas&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html';
            $link = '<span onclick="newwin2(\''.$url.'\',800,400);" class="btn-primary" id="tw'.date("Ymdhis").'" style="cursor: pointer; padding: 5px 10px; border-radius: 4px;">';
            $link = '<img src="'.base_url('img/nets/icone_pinterest.png').'" class="icone_nets">';
            $link .= '</span>'.cr();
            return($link);
        }
    function linked($data)
        {
            $d = $this->dados($data);
			$nm = $d['title'].'. '.$d['autor_resumido'].' '.$d['http'];
			$nm = urlencode($nm);
            $url = 'https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href='.$nm.'&picture=&title=Aspectos%20%C3%A9ticos%20da%20coautoria%20em%20publica%C3%A7%C3%B5es%20cient%C3%ADficas&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html';
            $link = '<span onclick="newwin2(\''.$url.'\',800,400);" class="btn-primary" id="tw'.date("Ymdhis").'" style="cursor: pointer; padding: 5px 10px; border-radius: 4px;">';
            $link = '<img src="'.base_url('img/nets/icone_linked.png').'" class="icone_nets">';
            $link .= '</span>'.cr();
            return($link);
        }
    function google($data)
        {
            $d = $this->dados($data);
			$nm = $d['title'].'. '.$d['autor_resumido'].' '.$d['http'];
			$nm = urlencode($nm);
            $url = 'https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href='.$nm.'&picture=&title=Aspectos%20%C3%A9ticos%20da%20coautoria%20em%20publica%C3%A7%C3%B5es%20cient%C3%ADficas&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html';
            $link = '<span onclick="newwin2(\''.$url.'\',800,400);" class="btn-primary" id="tw'.date("Ymdhis").'" style="cursor: pointer; padding: 5px 10px; border-radius: 4px;">';
            $link = '<img src="'.base_url('img/nets/icone_google.png').'" class="icone_nets">';
            $link .= '</span>'.cr();
            return($link);
        }
    function facebook($data)
        {
            $d = $this->dados($data);
			$nm = $d['title'].'. '.$d['autor_resumido'].' '.$d['http'];
			$nm = urlencode($nm);
			$url = 'https://www.facebook.com/dialog/share?app_id=140586622674265&display=popup&href='.$nm.'&picture=&title=Aspectos%20%C3%A9ticos%20da%20coautoria%20em%20publica%C3%A7%C3%B5es%20cient%C3%ADficas&description=&redirect_uri=http%3A%2F%2Fs7.addthis.com%2Fstatic%2Fthankyou.html';
            $link = '<span onclick="newwin2(\''.$url.'\',800,400);" class="btn-primary" id="tw'.date("Ymdhis").'" style="cursor: pointer; padding: 5px 10px; border-radius: 4px;">';
            $link = '<img src="'.base_url('img/nets/icone_facebook.png').'" class="icone_nets">';
            $link .= '</span>'.cr();
            return($link);
        }
    function twitter($data)
        {
            $d = $this->dados($data);
			$nm = $d['title'].'. '.$d['autor_resumido'].' '.$d['http'];
			$nm = urlencode($nm);
            $url = 'https://twitter.com/intent/tweet?text='.$nm.'&related=';
            $link = '<span onclick="newwin2(\''.$url.'\',800,400);" id="tw'.date("Ymdhis").'" style="cursor: pointer;">';
            $link .= '<img src="'.base_url('img/nets/icone_twitter.png').'" class="icone_nets">';
            $link .= '</span>'.cr();
            return($link);            
        }                
    function dados($data) 
        {
            $title = '';
            $autor = '';
			$autor_resumido = '';
            $doi = '';
            $source = '';
            $doi = '';
			$subject = '';
            
            for ($r=0;$r < count($data);$r++)
                {
                    $line = $data[$r];
					$id = $line['d_r1'];
                    $class = trim($line['c_class']);
                    $name = $line['n_name'];
                    //echo $class.'-'.$name.'<hr>';
                    switch($class)
                        {
                        case 'hasRegisterId':
                            if (substr($doi,0,3) == 'DOI:')
                                {
                                    $doi = troca($name,'DOI:','');
                                    $doi = '#'.$doi;
                                    //$doi = 'http://dx.doi.org/'.$doi;        
                                }
                            
                            
                            break;
						case 'hasSubject':
                            if (strlen($name) > 0)
                                {
                                	$name = ucwords($name);
                                    $subject .= '#'.troca(ucFirst($name),' ','').' ';      
									$subject = troca($subject,'&','');
									$subject = troca($subject,'-','');
                                }
                            break;
							
                        case 'hasSource':
                            if (strlen($source) == 0)
                                {
                                	$src = $name;
									$src1 = substr($src,0,strpos($src,';'));
									$src1 = troca($src1,' ','').' ';
									$src2 = substr($src,strpos($src,';')+1,strlen($src));
                                    $source = '#'.$src1.$src2;  
									$source = troca($source,'&','');      
                                }
                            
                            break;
                        case 'hasTitle':
                            if (strlen($title) == 0)
                                {
                                    $title = $name;
                                }
                            break;
                        case 'hasAuthor':
							$au = nbr_autor($name,7);
							$aun = nbr_autor($name,9);                           
							
                            if (strlen($au) > 0)
                                {
                                    if (strlen($autor) > 0) { $autor .= '; '; $autor_resumido .= '; ';}
									$au = troca($au,' ','');
                                    $autor .= trim($au); 
									$autor_resumido .= trim($aun);       
                                }
                            
                            break;
                        }
                }
			$d = array();
			$d['title'] = $title;
			$d['autor'] = $autor;
			$d['autor_resumido'] = $autor_resumido;
			$d['http'] = base_url(PATH.'v/'.$id);
			$d['doi'] = $doi;
			$d['subject'] = $subject;
            //$nm = urlencode($nm);			
            return($d);             
        }    		
    }
?>
