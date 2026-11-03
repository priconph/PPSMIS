<!DOCTYPE>
<html>
<head>
	<title>PMMIS PKG Request</title>
	<?php require_once('../header-includes.inc.php');

	session_start();
	
 	$username = $_SESSION['username'];
 	// echo $username;
 	?>

</head>
<body id="page-top" class="bg_gray">
	<!-- Page Wrapper -->
	<div id="wrapper">
		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">
			<!-- Main Content -->
			<div id="content">
				<!-- Begin Page Content -->
				<div class="container-fluid" style="padding-bottom: 50px;">
					<?php
						if ($username == 'aaduenas' || $username =='mpgarcia' || $username =='jangeco' || $username =='cpagtalunan' || $username =='cbretusto'){
						// if ($username == 'aaduenas' || $username =='mpgarcia'){
							$_GET['page'] = 's';
							require_once('./pages/request_f3.php');
						}else{
							$_GET['page'] = 's';
							require_once('./pages/request.php');
						}
					?>
				</div>
				<!-- End of Container Fluid -->
			</div>
			<!-- End of Main Content -->
		</div>
		<!-- End of Content Wrapper -->
		<footer class="main-footer">
		</footer>
	</div>
	<!-- End of Page Wrapper -->
</body>
</html>