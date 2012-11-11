<?php


require("simplepickup.class.php");
require("vimeo.class.php");

if (count($_SERVER["argv"]) == 1) {
    die("Error, enter a mode.\n");
}

for ($i = 1; $i < $_SERVER["argc"]; $i++) {
    $value = $_SERVER["argv"][$i + 1];
    switch ($_SERVER["argv"][$i]) {
        case "-h":
        case "--help":
        case "-?":
            printHelp();
            exit(1);
            break;
        
        case "-m":
        case "--mode":
            if ($value == null || ($value != "download" && $value != "links"))
                die("Error, enter a mode.\n");
            $mode = $value;
            break;
        
        case "-u":
        case "--user":
            $user = $value;
            break;
        
        case "-p":
        case "--pass":
        case "--password":
            $pass = $value;
            break;
        
        case "-i":
        case "--input":
            $input = $value;
            break;
        
        case "-o":
        case "--output":
            $output = $value;
            break;
            
            
    }
    
}


processMode($mode, $user, $pass, $input, $output);


//Did someone say nested if statements?
function processMode($mode, $user, $pass, $input, $output)
{
    $ableToLogin = !empty($user) && !empty($pass);
    if ($mode == "download") {
        if (!$ableToLogin) {
            die("Error. Enter both the username and password.\n");
        }
		downloadVideos($user, $pass);
    } else if ($mode == "links") {
        if (empty($input) && empty($output)) {
            die("Enter either an input file or output file.\n");
        } else if (empty($input)) {
            if (!$ableToLogin) {
                die("Error. Enter both the username and password.\n");
            }
			getLinks($user, $pass, $output);
        } else if (empty($output)) {
			downloadLinks($input);
        }
    } else {
        die("Error! I ran into one crazy effed up error.\n");
    }
    
}



function downloadVideos($user, $pass)
{
	getLinks($user, $pass, "links.txt");
	downloadLinks("links.txt");

}

function downloadLinks($input)
{
	
	$vimeo = new Vimeo();
	$vimeo->downloadLinks($input);

}

function getLinks($user, $pass, $output)
{
	$sp = new SimplePickup();
	$sp->login($user, $pass);
	$sp->parseLinks();
	$sp->saveLinksToFile($output);
}

function printHelp()
{
    echo "Consult the GitHub page for an updated help list..\n";
}

?>