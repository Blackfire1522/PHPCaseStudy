<!-- API calls useable to get playlistId, videoData and the length of certain videos on youtube -->
<?php 
define('API_KEY', 'AIzaSyBR9JhYsTW5OX-CpG-Tu0_zm7aLs4YooDI');
/**
@param $url A YouTube-URL to a Channel or playlist, to get a playlistId from
@return the playlistId from the given playlist or the "uploads"-playlist of a given channel

Uses the YouTube-API to get the playlistId
Channels with long or complex names have a "youtube.com/channel/[channelId]" URL, these are accessed using the channelId,
other channels have a "youtube.com/user/[username]" URL, these are accessed using the username.
*/
function get_playlist_id($url){	
	$base_url = 'https://www.googleapis.com/youtube/v3/channels';
    $params = array(
        'part'=>'contentDetails',
        'key'=>API_KEY
    );			
	$parsed_url =   parse_url($url);
	if(preg_match('/channel/', $url)){	
		$path = $parsed_url['path'];
		$url_array = explode('/', $path);
		$index = array_search('channel', $url_array);
		$channel_id = $url_array[$index + 1];
		$params['id'] = $channel_id;		
	}
	
	elseif(preg_match('/user/', $url)){		
		$url_array = explode('/', $parsed_url['path']);
		$index = array_search('user', $url_array);
		$username = $url_array[$index + 1];
		$params['forUsername'] = $username;	
	}
	
	else{									
		$query = $parsed_url['query'];
        parse_str($query, $params);
        $id = $params['list'];
        return $id;
	}
	$api_call = $base_url.'?'.http_build_query($params);
	$data = file_get_contents($api_call);
	$chinfo = json_decode($data, true);
	if(empty($chinfo["items"])){
		echo 'Invalid URL';
		return false;
	}
	
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
