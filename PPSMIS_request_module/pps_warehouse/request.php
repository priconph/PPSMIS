<!DOCTYPE html>
<html>
<head>
	<title>PMMIS PKG Request</title>
	<?php require_once('../header-includes.inc.php');?>
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
					$_GET['page'] = 'req';
					require_once('./pages/request.php');
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