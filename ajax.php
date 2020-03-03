<?php
error_reporting(0);

function getVar($post_url){
			$output = array();
			$url = $post_url;
			$ch = curl_init();
			$timeout = 2;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			$contents = curl_exec($ch);
			curl_close($ch);
			
			$str = preg_match('#(.*?)<script type=\"text/javascript\">window._sharedData = (.*?)</script>(.*?)#',$contents,$mat);
			if(!$str){
				array_push($output,array(
					'Error'=>'404',
					'msg'=>'Can not get data.'
					)); 
				return json_encode($output);
			}

			$nstr = json_decode(mb_substr($mat[2],0,-1),true);
			
			$i=0;
			
			
			
			//array_push($output,array('a'=>'d','b')); 
			
			if($nstr['entry_data']['PostPage'][0]['graphql']['shortcode_media']['is_video']){
				$video=$nstr['entry_data']['PostPage'][0]['graphql']['shortcode_media']['video_url'];
				$photo=$nstr['entry_data']['PostPage'][0]['graphql']['shortcode_media']['display_url'];
				
				array_push($output,array(
					'isVideo'=>'1',
					'isProfile'=>'0',
					'image_url'=>$photo,
					'video_url'=>$video
					)); 
			}
			else if($nstr['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url_hd']){
				
				$photo=$nstr['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url_hd'];
				
				array_push($output,array(
					'isVideo'=>'0',
					'isProfile'=>'1',
					'image_url'=>$photo
					)); 
			}
			else{
				if($nstr['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']['edges']){
				foreach($nstr['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']['edges'] as $rs){
					$i++;

					if($rs['node']['is_video']){
						array_push($output,array(
							'isVideo'=>'1',
							'isProfile'=>'0',
							'image_url'=>$rs['node']['display_url'],
							'video_url'=>$rs['node']['video_url']
							));
					}
					else{
						array_push($output,array(
							'isVideo'=>'0',
							'isProfile'=>'0',
							'image_url'=>$rs['node']['display_url']
							));
					}
				}
				}
				else{
					array_push($output,array(
						'isVideo'=>'0',
						'isProfile'=>'0',
						'image_url'=>$nstr['entry_data']['PostPage'][0]['graphql']['shortcode_media']['display_url']
						));
				}
			}
			return json_encode($output);
}

if($_POST['url']){
			if(preg_match('#^https://(www\.)?instagram\.com/#i',$_POST['url'])){
				$url = preg_replace('#https://(www\.)?#i','https://www.',$_POST['url']);
				$lastWord = substr($url,-1);
				if($lastWord == '/'){
					echo getVar($url);
				}
				else{
					$url = explode('?',$url);
					$lastWord = substr($url[0],-1);
					if($lastWord == '/'){
						echo getVar($url[0]);
					}
					else{
						echo getVar($url[0].'/');
					}
				}
			}
			else{
				array_push($output,array(
					'Error'=>'401',
					'msg'=>'Please use url https://www.instagram.com/'
					)); 
				exit(json_encode($output));
			}
}