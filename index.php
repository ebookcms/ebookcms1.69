<?php 
	session_start();
	include('ebookcms.php');
	require('css/index.tpl');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php get_header(); ?>
</head>

<body>

	<div class="tophead">

		<?php showSection('top-links'); ?>

	    <div id="search_place">
	    	<?php searchform(); ?>
	    </div>
	    
 
	</div>
	
	<div class="webmain">
	
		<!--  LOGOs PLACE  -->
		<div class="logo_place">
			<div class="yourlogo">
				<img src="css/images/logotipo.png" alt="eBookCMS 1.69"/>
				<p><?php echo load_cfg('web_title'); ?></p>
			</div>
			<div class="ebooklogo">
				<img src="css/images/logo.png" alt="Logo1" />
			</div>
		</div>
		
		<div class="content">
		
			<div class="main_content">
				<?php center(); ?>
			</div>
			
			<div class="footer">
				<div class="rights">
					&copy; 2015 by eBookCMS
				</div>
				<ul class="adminfoot">
					<?php loginlink(); ?>
				</ul>
			
			</div>
		</div>
		
	</div>
</body>
</html>