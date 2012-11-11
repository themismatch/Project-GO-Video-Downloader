<?php
class SimplePickup
{
    public $cookiefile;
    public $html;
    public $VimeoLinks;
    
    public function login($user, $pass)
    {
        global $cookiefile;
        $cookiefile = tempnam("/tmp", "cookies");
        global $html;

        //We need to get the cookies
		echo("Loading login page.\n");
        $url     = "http://www.simplepickup.com/premium/login/index?amember_redirect_url=%2Fpgo%2Fnav%2Fstuff%2F"; //Url should redirect to "My Stuff" page
        $referer = "";
        $html    = $this->curlGet($url, $referer);
        
        //Parsing for login_attempt_id - needed for logging in.
        preg_match('#name="login_attempt_id" value="(.*?)"#', $html, $matches);
        $id = $matches[1];
        
        //Send the post data for login
		echo("Logging in.\n");
        $referer = $url;
        $post    = "amember_login=$user&amember_pass=Password&amember_pass=$pass&login_attempt_id=$id&amember_redirect_url=%2Fpgo%2Fnav%2Fstuff%2F";
        $html    = $this->curlPost($url, $post, $referer);
        
    }
    public function parseLinks()
    {
        global $VimeoLinks;
        global $html;
        //Basically, a way to check if we've logged in, or there's an error.
        if (!preg_match_all('#http://www.simplepickup.com/pgo/m[0-9]{2}/.*?/#', $html, $matches)) {
            die("Error parsing links.");
        }
        
        $links = array_unique($matches[0]); //Remove any duplicates
        $max   = count($links);
        $curr  = 0;
        
        foreach ($links as $url) {
            $curr++;
            $count = "$curr/$max"; //This becomes our counter
            echo ("$count: Getting link.\n");
            $html = $this->curlGet($url, "");
            preg_match('#http(s)://player.vimeo.com/video/[0-9]{8}#', $html, $matches); //Look for a Vimeo URL.
            $VimeoLinks[] = $matches[0];
            
        }
        
    }
    public function saveLinksToFile($output)
    {
        global $VimeoLinks;
        $content = "";
        foreach ($VimeoLinks as $link) {
            $content .= $link . "\n";
        }
        file_put_contents($output, $content, FILE_APPEND);
    }
    
    public function curlGet($url, $ref)
    {
        //Initialise CURL
        $ch = curl_init();
        //Set all the various options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $ref);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_POST, 0); // set POST method
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //Set the cookie file you want to use
        curl_setopt($ch, CURLOPT_COOKIEFILE, $GLOBALS['cookiefile']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $GLOBALS['cookiefile']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        //execute the CURL
        $result = curl_exec($ch);
        curl_close($ch);
        
        return ($result);
    }
    
    
    public function curlPost($url, $post, $ref)
    {
        //Initialise CURL
        $ch = curl_init();
        //Set all the various options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $ref);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($ch, CURLOPT_POST, 1); // set POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //Set the cookie file you want to use
        curl_setopt($ch, CURLOPT_COOKIEFILE, $GLOBALS['cookiefile']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $GLOBALS['cookiefile']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        //execute the CURL
        $result = curl_exec($ch);
        curl_close($ch);
        
        return ($result);
    }
}
?>