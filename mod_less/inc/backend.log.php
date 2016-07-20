	<section class="be_container">
		
		<h2><?php echo $BLM['be_nav_log']; ?></h2>
				
			<h3>Compiler:</h3>
			<div class="logbox">
			<?php
				echo str_replace("\n","<br>",file_get_contents($modpath.'log/log.txt'));
			 ?>
			</div>
			
			<h3>other logs:</h3>
			<?php
				$logs = glob($modpath.'log/*.txt');
				foreach($logs as $log){
					if(str_replace($modpath.'log/','',$log) == "log.txt") continue;
					$logcontent = file_get_contents($log);
					if(strlen($logcontent) <= 0) continue;
			?>	
				<h4><?php echo str_replace($modpath.'log/','',$log); ?> <small>date: <?php echo date('Y-m-d H:i:s',filemtime($log)); ?></small></h4>
				<div class="logbox">
				<?php
					echo str_replace("\n","<br>",$logcontent);
				 ?>
				</div>
			<?php
				}	
			?>	
		
	</section>
	<?php
		// remove message
		file_put_contents($modpath.'log/log.txt', "");	
	?>	