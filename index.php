<?php
//include "fb_config.php";
session_start();
//var_dump($_SESSION);
 if (isset($_SESSION['fb_access_token'] )) {
	header('location: https://'.$_SERVER['HTTP_HOST'].'/dashboard_main.php');
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login V7</title>
	<meta charset="UTF-8">
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
 
  	<link rel="stylesheet" type="text/css" href="assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="assets/css/main.css">
  <style>
  body{
  background-image:url(assets/images/fb.jpg);
  margin: 0; 
  height: 100%;
  overflow: hidden;
  background-repeat: no-repeat;
}
  
  .navbar-header {
    float: left;
    padding: 15px;
    text-align: center;
    width: 100%;
	border-color: #fff;
	
}
.navbar-brand {float:none;}

.navbar-default {
    background-color: #29487d;
    
}
.navbar {
     margin-bottom: 0px !importatnt;
	 border: 0px solid transparent; 
}
  </style>	
<!--===============================================================================================-->

<!--===============================================================================================-->
</head>
<body>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header" style="text-align:center">
      <a style="color:#fff; font-size:25px;" class="navbar-brand" href="#"><b>Facebook SA </b></a>
    </div>
   
  </div>
</nav>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100  p-b-70">
				<form class="login100-form validate-form">
				<div style="text-align:center">
				<img src="assets/images/analysis.png" alt=" " width="50%" >
					</div>
					<div>
					<?php
						//include 'fb_config.php';
						// $helper = $fb->getRedirectLoginHelper();
						// $permissions = array(
							// 'email',
							// 'user_photos',
							// 'read_insights'
						// );
						// $loginUrl = $helper->getLoginUrl('location: https://'.$_SERVER['HTTP_HOST'].'/dashboard_main.php', $permissions);
					?>
						<a href="<?php echo $loginUrl; ?>" class="btn-login-with bg1 m-b-10">
							<i class="fa fa-facebook-official"></i>
							Login with Facebook
						</a>

					</div>	
				</form>
			</div>
		</div>
	</div>


</body>
</html>
