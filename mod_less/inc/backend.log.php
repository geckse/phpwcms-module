	<section class="be_container">
		
		<h2><?php echo $BLM['be_nav_log']; ?></h2>

			<h3>Less:</h3>
			<div class="logbox">
			<?php
				$str = str_replace("\n","<br>",file_get_contents($modpath.'log/less.txt'));
				if(!strlen($str)){
					echo "Fine less.";
				} else {
					echo $str;
				}
			 ?>
			</div>
			
			<h3>Compiler:</h3>
			<div class="logbox">
			<?php
				echo str_replace("\n","<br>",file_get_contents($modpath.'log/log.txt'));
			 ?>
			</div>
		
		
	</section>