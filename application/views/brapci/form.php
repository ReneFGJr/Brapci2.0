<div style="margin-top: 150px"></div>
<div class="container" style="border: 0px solid #ff0000;">
	<div class="row">
		<div class="col-12">
			<form class="search-wrapper  ucase">
				<?php echo msg('search_term'); ?>
				<textarea class="form-control" name="q" id="q" placeholder="Search here..."><?php echo get("q");?></textarea>
				<?php
				$pos = get("type");
                if (strlen($pos) == 0)
                    {
                        $pos = 1;
                    }
				for ($r=1;$r <=6;$r++)
                    {
                        $check = '';
                        if ($r==$pos)
                            {
                                $check = 'checked';
                            }
                        echo '<input type="radio" name="type" value="'.$r.'" '.$check.'>'.cr();
                        echo '<span style="margin-right: 10px; font-size: 75%;">'.msg('search_'.$r).'</span>'; 
                        
                    }
                ?>
				<button type="submit" >
					<?php echo msg('Search');?>
				</button>
			</form>
		</div>
	</div>
</div>
