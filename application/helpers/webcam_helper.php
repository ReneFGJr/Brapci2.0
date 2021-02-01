<?php

class webcam 
{
    function photo($name,$path='_repositorio/users')
        {
            $CI = &get_instance();
            $CI->lang->language['take_picture'] = 'Tirar foto';
            $CI->lang->language['no_image'] = 'Sem imagem';

            $p = explode('/',$path);
            $tmp = '';
            for ($r=0;$r < count($p); $r++)
                {
                    $tmp .= $p[$r].'/';
                    check_dir($tmp);
                }
            $tmp .= $name;      
            if (strpos($tmp,'.jpg') > 0)
            {
                /* imagem jpg */
            } else {
                $tmp .= '.jpg';
            }

            if (isset($_POST['base_img']))
                {
                    $img = $_POST['base_img'];
                    $img = str_replace(" ","+",$img);
                    $img = explode(',', $img);
                    $img = base64_decode(trim($img[1]));
                    /* Renomear foto */
                    file_put_contents($tmp,$img);
                }

            $img_mst = message(msg('no_image'),3);
            if (file_exists($tmp))
                {
                    $img_mst = '
                            <img id="imagemConvertida" src="'.base_url($tmp.'?v='.time()).'" class="img-fluid"/>            
                            <p>'.$tmp.'</p>
                        ';
                }

            
            $sx = '            
            <div class="col-md-6 col-sm-12 col-12">
			<video autoplay="true" id="webCamera" class="webcam_video"></video>
                <form method="post">
    			    <textarea id="base_img" name="base_img" style="display: none;"/></textarea>
			        <button class="btn btn-outline-primary" style="width: 100%;" type="button" onclick="takeSnapShot(); submit();">'.msg('take_picture').'</button>	
                </form>
            </div>
            <div class="col-md-6 col-sm-12 col-12">'.$img_mst.'</div>
            </div>';

        $css = '
        <style>
        .webcam_video{
            width: 100%;
            height: auto;
            background-color: whitesmoke;
        }
        </style>
        ';

        $js = '
        <script>
            function loadCamera(){
                //Captura elemento de vídeo
                var video = document.querySelector("#webCamera");
                    //As opções abaixo são necessárias para o funcionamento correto no iOS
                    video.setAttribute(\'autoplay\', \'\');
                    video.setAttribute(\'muted\', \'\');
                    video.setAttribute(\'playsinline\', \'\');
                    //--
                
                //Verifica se o navegador pode capturar mídia
                if (navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({audio: false, video: {facingMode: \'user\'}})
                    .then( function(stream) {
                        //Definir o elemento vídeo a carregar o capturado pela webcam
                        video.srcObject = stream;
                    })
                    .catch(function(error) {
                        alert("Oooopps... Falhou!");
                    });
                }
            }  

            function takeSnapShot(){
                //Captura elemento de vídeo
                var video = document.querySelector("#webCamera");
                
                //Criando um canvas que vai guardar a imagem temporariamente
                var canvas = document.createElement(\'canvas\');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                var ctx = canvas.getContext(\'2d\');
                
                //Desenhando e convertendo as dimensões
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                //Criando o JPG
                var dataURI = canvas.toDataURL(\'image/jpeg\'); 
                //O resultado é um BASE64 de uma imagem.
                document.querySelector("#base_img").value = dataURI;                
            }
            loadCamera();
        </script>
        ';            
    return($sx . $js . $css);
    }
}