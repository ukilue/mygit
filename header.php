<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>足達貨運有限公司</title>
		
		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="/assets/font-awesome/4.5.0/css/font-awesome.min.css" />
		
		<?php
			if($action == 'receipt'){
				switch ($type) {
					case 'add':
						echo '';
						break;
					case 'modify':
						echo 
						'
							<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
							<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
							<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
						';
						break;
				}
			}elseif($action == 'shipping'){
				switch ($type) {
					case 'add':
						echo '';
						break;
					case 'modify':
						echo 
						'
							<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
							<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
							<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
						';
						break;
				}
			}elseif($action == 'maintain'){
				switch ($type) {
					case '001':
						echo 
						'
							<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
							<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
							<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
						';
						break;
					case '002':
						echo  
						'
							<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
							<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
							<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
						';
						break;
					case '003':
						echo  
						'
							<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
							<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
							<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
						';
						break;
					case '004':
						echo  
						'
							<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
							<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
							<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
						';
						break;
					case '005':
						echo  
						'
							<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
							<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
							<link rel="stylesheet" href="assets/css/ui.jqgrid.min.css" />
						';
						break;
				}
			}elseif($action == 'query'){
				switch ($type) {
					case '001':
						echo '';
						break;
					case '002':
						echo '';
						break;				
				}
			}elseif($action == 'report'){
				switch ($type) {
					case '001':
						echo '<link rel="stylesheet" href="/assets/css/jquery-ui.min.css" />';
						break;
					case '002':
						echo '<link rel="stylesheet" href="/assets/css/jquery-ui.min.css" />';
						break;
					case '003':
						echo '';
						break;
					case '004':
						echo '';
						break;
					case '005':
						echo '';
						break;
					case '006':
						echo '';
						break;
				}
			}
		?>	
		
		<!-- text fonts -->
		<link rel="stylesheet" href="/assets/css/fonts.googleapis.com.css" />
		<!-- ace styles -->
		<link rel="stylesheet" href="/assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<!--[if lte IE 9]>
			<link rel="stylesheet" href="/assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="/assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="/assets/css/ace-rtl.min.css" />
		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="/assets/css/ace-ie.min.css" />
		<![endif]-->
		<!-- ace settings handler -->
		<script src="/assets/js/ace-extra.min.js"></script>
		<script src="/assets/js/jquery-2.1.4.min.js"></script>
		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="/assets/js/html5shiv.min.js"></script>
		<script src="/assets/js/respond.min.js"></script>
		<![endif]-->		
	</head>

	<body class="no-skin">
		<div id="navbar" class="navbar navbar-default  ace-save-state">
			<div class="navbar-container ace-save-state" id="navbar-container">
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				
				<div class="navbar-header pull-left">
					<a href="index.php" class="navbar-brand">
						<small>
							<i class="fa fa-leaf"></i>
							足達 Admin
						</small>
					</a>
				</div>

				<div class="navbar-buttons navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">						
						<li class="light-blue dropdown-modal">
							<a href="/logout.php">登出</a>
						</li>
					</ul>
				</div>
			</div><!-- /.navbar-container -->
		</div>

		<div class="main-container ace-save-state" id="main-container">
			<script type="text/javascript">
				try{ace.settings.loadState('main-container')}catch(e){}
			</script>

			<div id="sidebar" class="sidebar responsive ace-save-state">
				<script type="text/javascript">
					try{ace.settings.loadState('sidebar')}catch(e){}
				</script>
				<?php
					include_once("page_menu.php");
				?>
			</div>
			<?php				
				if(!empty($type)){
					if($action == "receipt"){
						$inner_menu = $s_receipt_menu;
						switch($type){
							case "add": $inner_submenu = $s_receipt_001; break;
							case "modify": $inner_submenu = $s_receipt_002; break;
						}
					}else if($action == "shipping"){
						$inner_menu = $s_shipping_menu;
						switch($type){
							case "add": $inner_submenu = $s_shipping_001; break;
							case "modify": $inner_submenu = $s_shipping_002; break;
						}
					}else if($action == "maintain"){
						$inner_menu = $s_maintain_menu;
						switch($type){
							case "001": $inner_submenu = $s_maintain_001; break;
							case "002": $inner_submenu = $s_maintain_002; break;
							case "003": $inner_submenu = $s_maintain_003; break;
							case "004": $inner_submenu = $s_maintain_004; break;
							case "005": $inner_submenu = $s_maintain_005; break;
						}
					}else if($action == "query"){
						$inner_menu = $s_query_menu;
						switch($type){
							case "001": $inner_submenu = $s_query_001; break;
							case "002": $inner_submenu = $s_query_002; break;
						}
					}else if($action == "report"){
						$inner_menu = $s_report_menu;
						switch($type){
							case "001": $inner_submenu = $s_report_001; break;
							case "002": $inner_submenu = $s_report_002; break;
							case "003": $inner_submenu = $s_report_003; break;
							case "004": $inner_submenu = $s_report_004; break;
							case "005": $inner_submenu = $s_report_005; break;
							case "006": $inner_submenu = $s_report_006; break;
						}
					}
				}
				$GLOBALS['submenu'] = $inner_submenu;
	
			?>
			<div class="main-content">
				<div class="main-content-inner">
					<div class="page-content">
						<div class="page-header">
							<h1>
								<?=$inner_menu?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?=$inner_submenu?>
								</small>
							</h1>
						</div><!-- /.page-header -->
