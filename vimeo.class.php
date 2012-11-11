<?php
	class Vimeo {
		public $previousProgress = 0;
		public $title;
		public $count;
		
		public function downloadLinks($input) {
			global $count;
			$VimeoLinks = file($input);
			$max = count($VimeoLinks);
			foreach ($VimeoLinks as $playerUrl) {
			    $curr++;
			    $count = "$curr/$max";
			    echo("$count: Generating Vimeo download URL.\n");
			    $dlInfo = $this->getVimeo($playerUrl);
			    $url    = $dlInfo[0];
			    $title  = $dlInfo[1];
			    
			    $GLOBALS['previousProgress'] = 0;
			    $targetFile                  = fopen("$title.mp4", 'w');
			    
			    $ch = curl_init($url);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_NOPROGRESS, false);
			    curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array('Vimeo', 'progressCallback'));
			    curl_setopt($ch, CURLOPT_FILE, $targetFile);
			    curl_exec($ch);
			    curl_close($ch);
			    echo ("\n");
			    
			}
		}
		
		
		public function getVimeo($url)
		{
			global $title;
		    $queryResult = $this->httpQuery($url);
		    $content     = $queryResult['content'];
		    preg_match('#"timestamp":([0-9]+)#i', $content, $matches);
		    $timestamp = $matches[1];
		    preg_match('#"id":([0-9]+)#i', $content, $matches);
		    $id = $matches[1];
		    preg_match('#"signature":"([a-z0-9]+)"#i', $content, $matches);
		    $signature = $matches[1];
		    preg_match('#"title":"(.*?)"#i', $content, $matches);
		    $title = $matches[1];
		    
		    $url = 'http://player.vimeo.com/play_redirect?clip_id=' . $id . '&sig=' . $signature . '&time=' . $timestamp . '&quality=hd';
		    
		    $finalQuery =$this-> httpQuery($url);
		    preg_match('#Location: (.*)#', $finalQuery['content'], $matches);
		    $url    = $matches[1];
		    $output = array(
		        $url,
		        $title
		    );
		    return $output;
		}

		public function httpQuery($url)
		{
		    $options = array(
		        CURLOPT_REFERER => "http://www.simplepickup.com",
		        CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.19 (KHTML, like Gecko) Ubuntu/12.04 Chromium/18.0.1025.168 Chrome/18.0.1025.168 Safari/535.19',
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_HEADER => true
		    );
		    $ch      = curl_init($url);
		    curl_setopt_array($ch, $options);
		    $content = curl_exec($ch);
		    $info    = curl_getinfo($ch);
		    curl_close($ch);
		    $result            = $info;
		    $result['content'] = $content;
		    
		    return $result;
		}
		public function progressCallback($download_size, $downloaded_size, $upload_size, $uploaded_size)
		{
		    if ($download_size == 0)
		        $progress = 0;
		    else
		        $progress = round($downloaded_size * 100 / $download_size);
		    
		    if ($progress > $GLOBALS['previousProgress']) {
		        $GLOBALS['previousProgress'] = $progress;
		        $fname                       = $GLOBALS["title"];
		        echo ($GLOBALS['count'] . ": $fname: $progress% \r");
		    }
		    
		    
		}
	}
?>