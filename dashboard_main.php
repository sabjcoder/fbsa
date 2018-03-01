<?php
if (!session_id()) {
    session_start();
}
require_once "vadersentiment.php";
include 'fb_config.php';
$helper = $fb->getRedirectLoginHelper();
if(isset($_GET['logout']))
{
	$fbLogoutUrl = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'https://'.$_SERVER['HTTP_HOST'].'/poc/fbsa/index.php');    
	session_destroy();
	unset($_SESSION['access_token']);
	header("Location: $fbLogoutUrl");
	exit;
}
if (!isset($_SESSION['fb_access_token'] )) {
	
	try {
		$accessToken = $helper->getAccessToken();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	if (! isset($accessToken)) {
		if ($helper->getError()) {
			header('HTTP/1.0 401 Unauthorized');
			echo "Error: " . $helper->getError() . "\n";
			echo "Error Code: " . $helper->getErrorCode() . "\n";
			echo "Error Reason: " . $helper->getErrorReason() . "\n";
			echo "Error Description: " . $helper->getErrorDescription() . "\n";
		} else {
			header('HTTP/1.0 400 Bad Request');
			echo 'Bad request';
		}
		exit;
	}
	// The OAuth 2.0 client handler helps us manage access tokens
	$oAuth2Client = $fb->getOAuth2Client();
	// Get the access token metadata from /debug_token
	$tokenMetadata = $oAuth2Client->debugToken($accessToken);
	// Validation (these will throw FacebookSDKException's when they fail)
	$tokenMetadata->validateAppId($appId);
	$tokenMetadata->validateExpiration();
	if (! $accessToken->isLongLived()) {
		// Exchanges a short-lived access token for a long-lived one
		try {
			$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
			exit;
		}
	}
	$_SESSION['fb_access_token'] = (string) $accessToken;
	$accessToken1=$_SESSION['fb_access_token'];
	// glob $commentCount=0;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FBSA |  </title> <!-- Bootstrap -->
	<!-- SPINNER -->
	<link rel="stylesheet" href="easy-loading.css">

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
   <!-- bootstrap-daterangepicker -->
    <link href="assets/css/daterangepicker.css" rel="stylesheet"> 
    <!-- Custom Theme Style -->
    <link href="assets/css/custom.min.css" rel="stylesheet">
  </head>

<body class="nav-md">
    <div class="container body">
		<div class="main_container">
			<div class="col-md-3 left_col">
				<div class="left_col scroll-view">
					<div class="navbar nav_title" style="border: 0;">
					  <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>FacebookSA</span></a>
					</div>

					<div class="clearfix"></div>

					<!-- menu profile quick info -->
					<div class="profile clearfix">
					  <div class="profile_pic">
						<?php
							$fb->setDefaultAccessToken($_SESSION['fb_access_token']);
							$accessToken1=$_SESSION['fb_access_token'];
							// Get User Details
							$res = $fb->get( '/me?fields=name,gender,email' );
							$user = $res->getGraphObject();
							$profilePath='https://graph.facebook.com/'. $user->getProperty( 'id' ) .'/picture?type=normal';
							$userName=$user->getProperty( 'name' );
							$userId=$user->getProperty( 'id' );
							//----------------------------PAGE DETAILS------------------------------
							$page_graph=$fb->get( '/me/accounts' );
							$page_edge=$page_graph->getGraphEdge();
							$noOfPages=$page_edge->asArray();
							//----------------------------------------------------------------------					
							
						?>
						<img src="<?php echo $profilePath;?>" alt="..." class="img-circle profile_img">
					  </div>
					  <div class="profile_info">
						<span>Welcome,</span>
						<h2><?php echo $userName; ?></h2>
					  </div>
					</div>
					<!-- /menu profile quick info -->

					<br />

					<!-- sidebar menu -->
					<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
					  <div class="menu_section">
						<h3>General</h3>
						<ul class="nav side-menu">
						  <li><a href="https://www.facebook.com/pages/create" target="_BLANK"><i class="fa fa-plus-square-o"></i>Create Page</a></li>
						  <li><a><i class="fa fa-edit"></i> Pages <span class="fa fa-chevron-down"></span></a>
							<ul class="nav child_menu">
								<?php 			
								$cnt=0;
								foreach($noOfPages as $eachPage){
									//echo $eachPage['name']."\n";
									echo "<li><a href='#' onClick=changePage('".$eachPage['id']."');>".$eachPage['name']."</a></li>";
									$globPageId[$cnt]=$eachPage['id'];
									$cnt++;
								}
								?>
							</ul>
						  </li>
						 </ul>
					  </div>

					</div>
					<!-- /sidebar menu -->

				  
				</div>
			</div>

			<!-- top navigation -->
			<div class="top_nav">
			  <div class="nav_menu">
				<nav>
				  <div class="nav toggle">
					<a id="menu_toggle"><i class="fa fa-bars"></i></a>
				  </div>

				  <ul class="nav navbar-nav navbar-right">
					<li class="">
					  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<img src="<?php echo $profilePath;?>" alt=""><?php echo $userName; ?>
						<span class=" fa fa-angle-down"></span>
					  </a>
					  <ul class="dropdown-menu dropdown-usermenu pull-right">
						<li><a href="https://facebook.com/<?php echo $userId;?>" target="_BLANK"> Profile</a></li>
						<li><a href="logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
					  </ul>
					</li>
				  </ul>
				</nav>
			  </div>
			</div>
			<!-- /top navigation -->

			<!-- page content -->
			<div class="right_col" role="main">
			<div id="mainDataHolder">
					  <!-- top tiles -->
					  <div class="row tile_count">
						<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
						  <span class="count_top"><i class="fa fa-user"></i>Users</span>
						  <div class="count">
							<?php
								$fancount=json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$globPageId[0]."/?fields=fan_count&access_token=".$accessToken1.""),true);
								echo $fancount['fan_count'];
							?>
						  </div>
						</div>
						<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
						  <span class="count_top"><i class="fa fa-clock-o"></i> Total  Reactions</span>
						  <div class="count" id="totalReaction"><?php 
							$sentimenter = new SentimentIntensityAnalyzer();
							$noOfPosts = json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$globPageId[0]."/?fields=feed.limit(100000)&access_token=".$accessToken1.""),true);
							//$cnt=count($noOfPosts);
							$postcnt=0;
							$commentCount1=0;
							$positiveSentiment=0;
							$negativeSentiment=0;
							$nutralSentiment=0;
							foreach($noOfPosts as $key => $posts) {
								if($key=="feed") {
									//$post=$posts;
									foreach($posts as $key1=>$feed) {
										if($key1=="data") {
											foreach($feed as $data1) {
												//echo $data1['id'];
												$postcnt++;
												$noOfComments = json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$data1['id']."/comments?limit=10000&access_token=".$accessToken1.""),true);
												foreach($noOfComments as $key2=>$comments) {
													// echo "<pre>";
													// print_r($comments);
													// echo "</pre>";
													if($key2=="data"){
														foreach($comments as $key3=>$cmntdata) {												
															if(array_key_exists('message', $cmntdata)) {
																//echo $commentCount1."\n"; we have to sentimental analysis here
																$commentCount1++;
																$result = $sentimenter->getSentiment($cmntdata['message']);
																if($result['pos'] > $result['neg'] && $result['pos'] > $result['neu']){
																	$positiveSentiment++;
																}elseif($result['neg'] > $result['pos'] && $result['neg'] > $result['neu']){
																	$negativeSentiment++;
																}else{
																	$nutralSentiment++;
																}
															}
															//echo $cmntdata['message']."\n";
														}											
														//$commentCount1=$commentCount1+$comments["total_count"];
													}
												}
											}
											
										}
										
									}
									//echo $commentCount1;
									//print_r($post);
								}
							}
							echo $commentCount1;
							//print_r($noOfPosts);
							//echo $postcnt;
						  ?>
						  </div>
						</div>
						<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
						  <span class="count_top"><i class="fa fa-user"></i> Average no. of comments</span>
							  <div class="count" id="avgCount">
								<?php
									if($postcnt==0) {
										echo "0";
									}else {
										$avgCount=$commentCount1/$postcnt;
										echo number_format((float)$avgCount, 2, '.', '');	
									}
									
								?>
							  </div>
						  </div>
						<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="color: #00bf29">
						  <span class="count_top"><i class="fa fa-user"></i> Positive Sentiments</span>
						  <div class="count" ><?php echo $positiveSentiment;?></div>
						</div>
						<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="color: #1281f3">
						  <span class="count_top"><i class="fa fa-user"></i> Nutral Sentiments</span>
						  <div class="count" ><?php echo $nutralSentiment;?></div>
						</div>
						<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count" style="color: #ff1800">
						  <span class="count_top"><i class="fa fa-user"></i> Negative Sentiments</span>
						  <div class="count" ><?php echo $negativeSentiment;?></div>
						</div>
					  </div>
					  <!-- /top tiles -->

				<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
						  <div class="dashboard_graph">

							<div class="row x_title">
							  <div class="col-md-6">
								<h3>Sentimental Analysis </h3>
							  </div>
							 <!--  <div class="col-md-6">
								<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
								  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
								  <span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
								</div>
							  </div >-->
							</div>

							<div class="col-md-12 col-sm-12 col-xs-12 margin_top_2px" style="margin-bottom:2%; margin-top:0%;" >
							  <!-- <div id="chart_plot_03" class="demo-placeholder" 	></div> -->
							<div id="curve_chart" style="width: 100%; height: 300px"></div>
							</div>
						</div>
					  </div>
					  <br />

					  
					<div class="row">


						<div class="col-md-8 col-sm-8 col-xs-12">
						  <div class="x_panel tile fixed_height_320">
							<div class="x_title">
							  <h2>Postwise Analysis</h2>
							 
							  <div class="clearfix"></div>
							</div>
							<div class="x_content" id="nextPostData">
								<div class="list-group">
									<?php
									$postClickFlag=0;
									$previousPage="0";
									$nextPage="0";
									$noOfPostsSentiment = json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$globPageId[0]."/feed?limit=5&access_token=$accessToken1"),true);
										foreach ($noOfPostsSentiment as $key => $val) { 
											if($key == 'data')	{															
												 foreach($val as $value) {									
													 if(array_key_exists('message', $value)) {
														//echo $value["message"];
														//echo $value["id"];
														$string = (strlen($value["message"]) > 50) ? substr($value["message"],0,48).'...' : $value["message"];
														echo "<a href='#' class='list-group-item' onClick=postSentiment('".$value["id"]."');>".$string."</a>";
														//echo "<a href='#' class='list-group-item'>First item</a>";
													 }elseif(array_key_exists('story', $value)){
														//echo $value["story"]; 
														//echo $value["id"]; 
														$string = (strlen($value["story"]) > 50) ? substr($value["story"],0,48).'...' : $value["story"];
														echo "<a href='#' class='list-group-item' onClick=postSentiment('".$value["id"]."');>".$string."</a>";
														//echo "<a href='#' class='list-group-item'>First item</a>";
													 }			
												 }
											}
											elseif($key=='paging'){
												if(array_key_exists('next', $val)) {
													$nextPage=$val['next'];
												}
												if(array_key_exists('previous', $val)) {
													$previousPage=$val['previous'];
												}
												
											}
										}
										
									?>
									<!--<a href="#" class="list-group-item">First item</a>
									<a href="#" class="list-group-item">Second item</a>
									<a href="#" class="list-group-item">Third item</a>
									<a href="#" class="list-group-item">First item</a>
									<a href="#" class="list-group-item">Second item</a>-->
									
								</div>
								<div class="pull-right">
									<?php 
										if($previousPage!="0") {
											echo "<button type='button' class='btn btn-round btn-primary' onClick=nextPost('".$previousPage."')>Previous</button>";
											$previousPage="0";
										}
										if($nextPage!="0") {
											echo "<button type='button' class='btn btn-round btn-primary' onClick=nextPost('".$nextPage."')>Next</button>";
											$nextPage="0";
										}
									?>					
								</div>
							</div>
						  </div>
						</div>

						<div class="col-md-4 col-sm-4 col-xs-12">
						  <div class="x_panel tile fixed_height_320 overflow_hidden">
							<div class="x_title">
							  <h2>Device Usage</h2>
							 
							  <div class="clearfix"></div>
							</div>
							<div class="x_content">
							  <table class="" style="width:100%">
								<tr>
								  <th style="width:37%;">
									<p></p>
								  </th>
								  <th>
									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
									  <p class="">Device</p>
									</div>
									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
									  <p class="">Progress</p>
									</div>
								  </th>
								</tr>
								<tr>
								  <td>
									<canvas class="canvasDoughnut" height="140" width="140" style="margin: 15px 10px 10px 0"></canvas>
								  </td>
								  <td>
									<table class="tile_info">
									  <tr>
										<td>
										  <p><i class="fa fa-square green"></i>Positive </p>
										</td>
										<td><span id="positiveSentiment" class="positiveSentiment"></span></td>
									  </tr>
									  <tr>
										<td>
										  <p><i class="fa fa-square red"></i>Negative</p>
										</td>
										<td><p id="negativeSentiment"></p></td>
									  </tr>
									  <tr>
										<td>
										  <p><i class="fa fa-square orange"></i>Nutral</p>
										</td>
										<td><p id="nutralSentiment"></p></td>
									  </tr>
									
									</table>
								  </td>
								</tr>
							  </table>
							</div>
						  </div>
						</div>
					</div>
						<!-- /page content -->

					<!-- footer content -->
					<footer>
					  <div class="pull-right">
					   <!--  Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a> -->
					  </div>
					  <div class="clearfix"></div>
					</footer>
					<!-- /footer content -->
				</div>
			</div>
			</div>
		</div>
	</div>
    <!-- jQuery -->
    <script src="assets/js/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Chart.js -->
    <script src="assets/js/Chart.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="assets/js/bootstrap-progressbar.min.js"></script>   
    <!-- Flot -->
    <script src="assets/js/jquery.flot.js"></script>
        <!-- Flot plugins -->
    <script src="assets/js/jquery.flot.orderBars.js"></script>
    <script src="assets/js/jquery.flot.spline.min.js"></script>
    <script src="assets/js/curvedLines.js"></script>
    <!-- DateJS -->
    <script src="assets/js/date.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/daterangepicker.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="assets/js/custom.min.js"></script>
	<script>
		var pos=1,neg=1,nut=1;
		function init_chart_doughnut() {
			if ("undefined" != typeof Chart && (console.log("init_chart_doughnut"), $(".canvasDoughnut").length)) {
				var a = {
					type: "doughnut",
					tooltipFillColor: "rgba(51, 51, 51, 0.55)",
					data: {
						labels: ["Positive", "Negative", "Nutral"],
						datasets: [{
							data: [pos, neg, nut],
							backgroundColor: ["#00bf29", "#ff1800", "#1281f3"]
						}]
					},
					options: {
						legend: !1,
						responsive: !1
					}
				};
				$(".canvasDoughnut").each(function() {
					var b = $(this);
					new Chart(b, a)
				})
			}
		}
		
		function postSentiment(id) {
			//alert(id);	
			$.ajax({		
				url:"func.php",
				type:"POST",
				data:{"flag":"pf","id":id},
				success:function(info){
					//alert(info);
					var res=info.split('/');
					pos=parseInt(res[0]);
					neg=parseInt(res[1]);
					nut=parseInt(res[2]);
					var ttl=pos+neg+nut;
					if(ttl==0){
						pos=0; neg=0; nut=0;
						$("#positiveSentiment").html(pos+" %");
						$("#negativeSentiment").html(neg+" %");
						$("#nutralSentiment").html(nut+" %");
						init_chart_doughnut();
					}else{
						posp=((pos/ttl)*100).toFixed(2);
						negp=((neg/ttl)*100).toFixed(2);
						nutp=((nut/ttl)*100).toFixed(2);
						//alert("total:"+ttl+" posp: "+posp);
						$("#positiveSentiment").html(posp+" %");
						$("#negativeSentiment").html(negp+" %");
						$("#nutralSentiment").html(nutp+" %");
						init_chart_doughnut();
					}
				}
			});
			//alert("Ha bhai aai aayo ho");
		}
		
		function nextPost(nurl) {			
			$.ajax({		
				url:"func.php",
				type:"POST",
				data:{"flag":"nextPost","url":nurl},
				success:function(info){							
					$("#nextPostData").html(info);					
				}
			});
		}
		
		function changePage(id) {
			EasyLoading.show({
			  //type: TYPE.PACMAN
			});

			$.ajax({		
				url:"func.php",
				type:"POST",
				data:{"flag":"changePage","id":id},
				success:function(info){		
					EasyLoading.hide();				
					$("#mainDataHolder").html(info);					
				}
			});
		}
	</script>
	<!-- SPINNER JS---->
	<script src="easy-loading.js"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Negative', 'Nutral','Positive'],
          ['01-03-18',  8,   5,   10],
          ['02-03-18',  3,   0,   8],
          ['03-03-18',  1,   7,    1],
          ['04-03-18',  2,   0,   5],
		  ['05-03-18',  4,   8,   0],
          ['06-03-18',  0,   1,   6]          
        ]);

        var options = {
				
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
  </body>
</html>