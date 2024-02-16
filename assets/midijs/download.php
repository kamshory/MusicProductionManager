<?php
for($i = 0; $i < 128; $i++)
{
	// http://www.midijs.net/lib/pat/MT32Drums/mt32drum-19.pat
	$url = "http://www.midijs.net/lib/pat/MT32Drums/mt32drum-$i.pat";
	echo "URL = ".($url)."<br>";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
	$data = curl_exec($ch);
	curl_close($ch);
	//echo "DATA = ".($data)."<br><br>";
	
	file_put_contents("pat/MT32Drums/mt32drum-$i.pat", $data);
}
?>