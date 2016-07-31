<?php 
/**
@param $url A YouTube-URL to a Channel or playlist, to get a playlistId from
@return the playlistId from the given playlist or the "uploads"-playlist of a given channel

Uses the YouTube-API to get the playlistId
Channels with long or complex names have a "youtube.com/channel/[channelId]" URL, these are accessed using the channelId,
other channels have a "youtube.com/user/[username]" URL, these are accessed using the username.
*/
function get_playlist_id($url){				
	if(preg_match('/channel/', $url)){		
		$url_array = explode('/', $url);
		$index = array_search('channel', $url_array);
		$username = $url_array[$index + 1];
		$api_request = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=';		
	}
	
	elseif(preg_match('/user/', $url)){		
		$url_array = explode('/', $url);
		$index = array_search('user', $url_array);
		$username = $url_array[$index + 1];
		$api_request = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=';	
	}
	
	else{									
		$url_array = explode('playlist?list=', $url);
		return $url_array[1];
	}
	
	$api_key = '&key=AIzaSyBR9JhYsTW5OX-CpG-Tu0_zm7aLs4YooDI';
	$api_call = $api_request.$username.$api_key;
	
	$data = file_get_contents($api_call);
	$chinfo = json_decode($data, true);
	
	return $chinfo["items"][0]["contentDetails"]["relatedPlaylists"]["uploads"];
}

/**
@param a valid playlistId
@return an array containing the information of the first 12 videos in the given playlist

Uses the YouTube-API to get the video information
*/
function get_video_list($playlist_id){		
	$api_request = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=';
	$api_request_details = '&maxResults=12&fields=items%2Fsnippet';
	$api_key = '&key=AIzaSyBR9JhYsTW5OX-CpG-Tu0_zm7aLs4YooDI';
	$api_call = $api_request.$playlist_id.$api_request_details.$api_key;
	
	$data = file_get_contents($api_call);
	$video_list = json_decode($data, true);
	
	return $video_list;
}

/**
@param a valid videoId
@return a string containing the video length of the given video, formatted to "HH:MM:SS" or "MM:SS"

Uses the YouTube-API to get the video duration
*/
function get_video_length($video_id){		
	$api_request = 'https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=';
	$api_key = '&key=AIzaSyBR9JhYsTW5OX-CpG-Tu0_zm7aLs4YooDI';
	$api_call = $api_request.$video_id.$api_key;
	
	$data = file_get_contents($api_call);
	$video_details = json_decode($data, true);
	
	$length = $video_details['items'][0]['contentDetails']['duration'];
	
	$length = substr($length, 2, -1);
	$hm = array('H', 'M');
	$length = str_replace($hm, ':', $length);
	$length_arr = explode(':', $length);
	$formattedlength = '';
	foreach($length_arr as $i){
		if(strlen($i) == 1){
			$i = '0'.$i;
		}
		$formattedlength .= $i.':';
	}
	
	return substr($formattedlength, 0, -1);
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
			$regex = '/youtube\.com\/[user|channel|playlist]/';	//regex to check wether a url is linked to a youtube channel or playlist
			
			if(!empty($_GET['channel-url'])){
				$url = $_GET['channel-url'];
				
				if(preg_match($regex, $url) == 0){
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