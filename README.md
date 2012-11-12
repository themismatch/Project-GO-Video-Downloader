Project GO Video Downloader
==========================

This script has two basic functionalities.  

1. Download videos directly from the Project GO
	* It can either download the videos or save the video links to a text file.
2. Download videos from a list of links 


Requirements
-----
You'll need `php` and `curl` on your system.


Usage
-----


<b>Download the files directly from your Project GO account to your computer:</b>

`php main.php --mode download -u USERNAME -p PASSWORD`

Note: You can also write `-m download` instead of `--mode download`.  

<b>Just save the Vimeo links to the videos from your Project GO account:</b>

`php main.php --mode links -u USERNAME -p PASSWORD -o links.txt`

Note: The links will be stored in `links.txt` in this example. 


<b>Download a list of Vimeo links:</b>   

`php main.php --mode links -i links.txt`  
Where `links.txt` is the file full of Vimeo links.

Flags
----
Username:  
`-u`  
`--username`

Password:  
`-p`  
`--password`

Mode:  
`-m`  
`--mode`  
The two modes are either `links` or `download`

Input File (full of Vimeo links to download):  
`-i`  
`--input`  

Output File (to save a list of Vimeo links from Project GO):  
`-o`  
`--output`


