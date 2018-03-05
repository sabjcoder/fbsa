<?php
if (!session_id()) {
    session_start();
}
require_once "vadersentiment.php";

if($_POST['flag']=="pf") {
	getdata();
}

if($_POST['flag']=="nextPost") {
	nextPage();
}
if($_POST['flag']=="changePage"){
	changePage();
}

function getdata(){
	$access_token=$_SESSION['fb_access_token'];
	$post_id=$_POST['id'];
	$positiveSentiment=0;
	$negativeSentiment=0;
	$nutralSentiment=0;
	$sentimenter = new SentimentIntensityAnalyzer();
	$noOfComments = json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$post_id."/comments?limit=10000&access_token=".$access_token.""),true);
		foreach($noOfComments as $key2=>$comments) {
			if($key2=="data"){
				foreach($comments as $key3=>$cmntdata) {												
					if(array_key_exists('message', $cmntdata)) {					
						$result = $sentimenter->getSentiment($cmntdata['message']);
						if($result['pos'] > $result['neg'] && $result['pos'] > $result['neu']){
							$positiveSentiment++;
						}elseif($result['neg'] > $result['pos'] && $result['neg'] > $result['neu']){
							$negativeSentiment++;
						}else{
							$nutralSentiment++;
						}
					}					
				}	
			}
		}
	echo $positiveSentiment."/ ".$negativeSentiment."/ ".$nutralSentiment;
}

function nextPage(){	
$url=$_POST['url'];
	echo "<div class='list-group'>";
		
		$postClickFlag=0;
		$previousPage="0";
		$nextPage="0";
		$noOfPostsSentiment = json_decode(file_get_contents($url),true);
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
							$string = (strlen($value["story"]) > 15) ? substr($value["story"],0,48).'...' : $value["story"];
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
			
		
	echo "</div>";
	echo "<div class='pull-right'>";
		
			if($previousPage!="0") {
				echo "<button type='button' class='btn btn-round btn-primary' onClick=nextPost('".$previousPage."')>Previous</button>";
				$previousPage="0";
			}
			if($nextPage!="0") {
				echo "<button type='button' class='btn btn-round btn-primary' onClick=nextPost('".$nextPage."')>Next</button>";
				$nextPage="0";
			}
				
	echo "</div>";
}

function changePage(){
	$pageId=$_POST['id'];
	$access_token=$_SESSION['fb_access_token'];
?>
<div class="row tile_count">
	<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
	  <span class="count_top"><i class="fa fa-user"></i>Users</span>
	  <div class="count">
		<?php
			$fancount=json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$pageId."/?fields=fan_count&access_token=".$access_token.""),true);
			echo $fancount['fan_count'];
		?>
	  </div>
	</div>
	<div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
	  <span class="count_top"><i class="fa fa-clock-o"></i> Total  Reactions</span>
	  <div class="count" id="totalReaction"><?php 
		$sentimenter = new SentimentIntensityAnalyzer();
		$noOfPosts = json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$pageId."/?fields=feed.limit(100000)&access_token=".$access_token.""),true);
		$postcnt=0;
		$commentCount1=0;
		$positiveSentiment=0;
		$negativeSentiment=0;
		$nutralSentiment=0;
		foreach($noOfPosts as $key => $posts) {
			if($key=="feed") {
				foreach($posts as $key1=>$feed) {
					if($key1=="data") {
						foreach($feed as $data1) {
							$postcnt++;
							$noOfComments = json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$data1['id']."/comments?limit=10000&access_token=".$access_token.""),true);
							foreach($noOfComments as $key2=>$comments) {
								if($key2=="data"){
									foreach($comments as $key3=>$cmntdata) {												
										if(array_key_exists('message', $cmntdata)) {
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
									}		
								}
							}
						}
						
					}
					
				}
			}
		}
		echo $commentCount1;
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
<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
		  <div class="dashboard_graph">

			<div class="row x_title">
			  <div class="col-md-6">
				<h3>Sentimental Analysis </h3>
			  </div>
				<div class="col-md-12 col-sm-12 col-xs-12 margin_top_2px" style="margin-bottom:2%; margin-top:0%;" >
					  <!-- <div id="chart_plot_03" class="demo-placeholder" 	></div> -->
					<div id="curve_chart" style="width: 100%; height: 300px"></div>
				</div>
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
					$noOfPostsSentiment = json_decode(file_get_contents("https://graph.facebook.com/v2.9/".$pageId."/feed?limit=5&access_token=$access_token"),true);
						foreach ($noOfPostsSentiment as $key => $val) { 
							if($key == 'data')	{															
								 foreach($val as $value) {									
									 if(array_key_exists('message', $value)) {
										$string = (strlen($value["message"]) > 50) ? substr($value["message"],0,48).'...' : $value["message"];
										echo "<a href='#' class='list-group-item' onClick=postSentiment('".$value["id"]."');>".$string."</a>";
									 }elseif(array_key_exists('story', $value)){
										$string = (strlen($value["story"]) > 50) ? substr($value["story"],0,48).'...' : $value["story"];
										echo "<a href='#' class='list-group-item' onClick=postSentiment('".$value["id"]."');>".$string."</a>";
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
	<footer>
	  <div class="pull-right">
	  </div>
	  <div class="clearfix"></div>
	</footer>
</div>
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
<?php
}
?>