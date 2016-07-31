<?php
function check_url($url){
	$regex = '/youtube\.com\/[user|channel|playlist]/';	//regex to check wether a url is linked to a youtube channel or playlist
	if(preg_match($regex, $url) == 0){
		return false;
	}
	else{
		return true;
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8"/>
	<title>Videos</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="buttonfunctions.js"></script>
</head>

<body>
	<div id="homepage">
		<div id="header">
			<h1>Videos</h1>
		
			<form action="">
			<input type="url" name="channel-url" placeholder="YouTube-URL"/>
			<button type="submit">
			submit
			</button>
			
		</div>
		<div id="main">
			
			<?php
			include 'apiCalls.php';
			
			
			if(!empty($_GET['channel-url'])){
				$url = $_GET['channel-url'];
				
				if(!check_url($url)){
					echo 'Please enter the URL of a Youtube-Channel (e.g. "https://youtube.com/user/*username*")';
				}
				else{
					$playlist_id = get_playlist_id($url);
					$video_list = get_video_list($playlist_id);
					
					$count = 0;
					
					foreach($video_list['items'] as $video_data) {
					$count++;
						if($count > 8){
							$id = 'hiddenvideo'.$count;
							echo(
				'<div id='.$id.'>'
								);
						}
						else{
							echo(
				'<div id="video">'
							);
						}
				?>
				
					<div id="video-title">
						<?php 
							echo $video_data['snippet']['title']; 
						?>
					</div>
					
					<div id="player">
						<iframe 
							src="https://www.youtube.com/embed/<?=$video_data['snippet']['resourceId']['videoId']?>" 
							allowfullscreen>
						</iframe>
					</div>
					<div id="description">
					
						<?php
							echo $video_data['snippet']['description'];
						?>		
						
					</div>
					
					<div id="duration">
						<span class="bluetext">&#9719;</span>
						
						<?php
							echo get_video_length($video_data['snippet']['resourceId']['videoId']);
						?>
						
					</div>
				</div>
				
				<?php 
					}
				?>
					<button type="button" id="more" onclick="showMore()";>more</button>
				
				<?php
				}
			}
				?>
				
			</div>
		</div>
	</div>
</body>
</html>