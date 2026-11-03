<?php
  require_once('../common_functions/get_username.php');
  require_once('./includes.php');
?>
<html>
<title>PPD Database System</title>
<head>
<link href="./css/ppsStyleSheet.css" rel="stylesheet">

</head>
<body style="overflow-y: hidden;">
  <nav class="navbar navbar-inverse" role="navigation" style="background-color:#008181;border-radius:0;border:none;">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <span style="position:absolute;font-size:140%;padding-top:10px;color:white;padding-left:5px;">PPDMIS - Planner Module</span>
        </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
      </ul>
      <ul class="nav navbar-nav navbar-right">
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<?php
  if(!isset($_GET['page'])){
    echo '<iframe src="./Consolidated_ppc.php?username='.$username.'" id="iFrm_Main" style="margin-top:-21px;height:90%;border:none;" frameborder="0"></iframe>';
  }
  else{
    echo '<iframe src="./'.$_GET['page'].'?username='.$username.'" id="iFrm_Main" style="margin-top:-21px;height:90%;border:none;" frameborder="0"></iframe>';    
  }
?>

</body>
</html>