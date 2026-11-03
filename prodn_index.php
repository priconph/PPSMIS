<?php
  require_once('../common_functions/get_username.php');
require_once('./includes.php');
?>
<html>
  <head>
    <title>PPD Material Inventory System</title>
    <link href="./css/ppsStyleSheet.css" rel="stylesheet">
  </head>
  <body style="border-radius:5px;overflow-y:hidden;">
    <nav class="navbar navbar-inverse" role="navigation" style="background-color:#008181;border-radius:0;border:none;">
      <div class="container-fluid">
        <div class="navbar-header">
          <span style="position:absolute;font-size:140%;padding-top:10px;color:white;padding-left:5px;">PPDMIS - Production Module</span>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
          </ul>
          <ul class="nav navbar-nav navbar-right">
          </ul>
        </div>
      </div>
    </nav>
    <?php
      if(!isset($_GET['page'])){
        echo '<iframe src="./Production.php?username='.$username.'" id="iFrm_Main" style="margin-top:-21px;height:100%;border:none;" frameborder="0"></iframe>';
      }
      else{
        echo '<iframe src="./'.$_GET['page'].'?username='.$username.'" id="iFrm_Main" style="margin-top:-21px;height:100%;border:none;" frameborder="0"></iframe>';    
      }
    ?>
  </body>
</html>