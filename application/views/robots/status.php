<?php
$ver = 'v0.19.04.12';
?>
<meta http-equiv="refresh" content="60" />
<body style="background-color:#ffffff;">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>Brapci Robots Monitor</h1>            
        </div>              
        
        <div class="col-md-6 robotStatus01" style="min-height: 300px;">
            <?php
            if (file_exists('status_listIdentifier.php')) {

            } else {
                echo "null";
            }
            ?>
        </div>
        <div class="col-md-6 robotStatus02" style="min-height: 300px;">
            <?php
            if (file_exists('status_listGetrecord.php')) {

            } else {
                echo "null";
            }
            ?>            
        </div>
        
        <div class="col-md-6 robotStatus03" style="min-height: 300px;">
            <?php
            $file = 'application/views/robots/status_lastupdate.php';
            if (file_exists($file)) {
                require($file);
            } else {
                echo "null";
            }
            ?>
        </div>
        <div class="col-md-6 robotStatus04" style="min-height: 300px;">
            <?php
            if (file_exists('status_resume.php')) {

            } else {
                echo "null";
            }
            ?>            
        </div>  
        <div class="col-md-6">
            <?php
            echo date("d-m-Y H:i:s");
            ?>            
        </div>  
        <div class="col-md-6 text-right">
            <?php
            echo 'Version '.$ver;
            ?>            
        </div>                    
    </div>
</div>
</body>

