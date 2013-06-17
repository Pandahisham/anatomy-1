<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Takes an array and sorts it by time in descending order
function mysort($a, $i = 0, $sort = 'time')
{
	if ($i == sizeof($a) - 1)
		return $a;
	else if ($a[$i][$sort] < $a[$i + 1][$sort]) {
		$temp = $a[$i];
		$a[$i] = $a[$i + 1];
		$a[$i + 1] = $temp;
		return mysort($a, 0, $sort);
	} else
		return mysort($a, ++$i, $sort);
}

// Takes a UNIX timestamp and converts it into an elapsed time or human form
// depending on how long ago the date is
function timestamp($then)
{
	setTimezone();
	$time = time() - $then;
	if ($time < 60) { // 1 minute
		if ($time <= 1)
			return "1 second ago";
		else
			return $time . " seconds ago";
	}
	else if ($time < 3600) { // 1 hour
		$time = round($time / 60, 0);
		if ($time < 1.5)
			return "1 minute ago";
		else
			return $time . " minutes ago";
	}
	else if ($time < 86400) {// 24 hours
		$hr = floor($time / 3600);
		$time -= $hr * 3600;
		$min = round($time / 60, 0);
		if ($hr < 2)
			return "1 hour " . $min . " min ago";
		else
			return $hr . " hours " . $min . " min ago";
	}
	else if ($time < 561600) { // 6.5 days
		if (($time / 86400) < 1.7)
			return date("\Y\e\s\\t\e\\r\d\a\y \a\\t g:ia", $then);
		else
			return date("l \a\\t g:ia", $then);
	}
	else if ($time < 31536000) // 1 year
		return date("F j \a\\t g:ia", $then); // March 10 at 5:16pm
	else
		return date("F j, Y", $then); // January 1, 1970
}

// Takes a UNIX timestamp and converts to just the date
// E.g. 9/2/1986 (month, day, year)
function datestamp($then)
{
	setTimezone();
	return date("n/j/Y", $then);
}

// Takes a UNIX timestamp and returns a timespan (shouldn't exceed 7 days)
// Used for time left for item auctions
function timespan($future)
{
	$time = $future - time();
	if ($time <= 0)
		return "Closed";
	else if ($time < 3600) { // 1 hour
		$time = round($time / 60, 0);
		if ($time <= 1)
			return "<span class=\"red bold\">< 1 minute</span>";
		else
			return "<span class=\"red bold\">" . $time . " minutes</span>";
	}
	else if ($time < 86400) { // 24 hours
		$hr = floor($time / 3600);
		$time -= $hr * 3600;
		$min = round($time / 60, 0);
		if ($hr < 2)
			return "1 hour $min min";
		else
			return "$hr hours $min min";
	}
	else {
		$day = floor($time / 86400);
		$time -= $day * 86400;
		$hr = round($time / 3600, 0);
		if ($hr <= 1)
			$hr = "1 hour";
		else
			$hr = $hr . " hours";
		if ($day < 2)
			return $day . " day " . $hr;
		else
			return $day . " days " . $hr;
	}
}

// Takes an image, scales it to the dwidth and dheight, and returns an img tag
function scale($img, $dwidth, $dheight)
{
	list($width, $height, $type, $attr) = getimagesize($img);
	// Only resize if img height and width are larger than destinations
	// Also perform a catchall in case width and height are the same
	if (($width > $height || $width == $height) && $width > $dwidth) {
		$nwidth = $dwidth;
		$perc = $nwidth / $width;
		$nheight = $height * $perc;
		return "<img src='" . $img . "' width='" . $nwidth . "px' height='" . $nheight . "px' border='0' />";
		
	} 
	else if ( $height > $width && $height > $dheight ) {
		$nheight = $dheight;
		$perc = $nheight / $height;
		$nwidth = $width * $perc;
		return "<img src='" . $img . "' width='" . $nwidth . "px' height='" . $nheight . "px' border='0' />";
	}
	else
		return "<img src='" . $img . "' " . $attr . " border='0' />";
}

// Sets the default time zone for all date functions based on user's settings
function setTimezone()
{
	$CI =& get_instance();
	$timezone = "America/Mexico_City";
	if ($CI->session->userdata('timezone'))
		$timezone = zoneList($CI->session->userdata('timezone'));
	date_default_timezone_set($timezone);
}

// Returns the key when matched with CodeIgniter's timezone abbreviation value
function zoneList($tz)
{
    $zones = array
    (
        'Pacific/Kwajalein' => 'UM12',
        'Pacific/Midway' => 'UM11',
        'Pacific/Honolulu' => 'UM10',
		'Pacific/Marquesas' => 'UM95',
        'America/Anchorage' => 'UM9',
        'America/Los_Angeles' => 'UM8',
        'America/Denver' => 'UM7',
        'America/Mexico_City' => 'UM6',
        'America/Bogota' => 'UM5',
		'America/Caracas' => 'UM45',
        'America/Halifax' => 'UM4',
        'America/St_Johns' => 'UM35',
        'America/Buenos_Aires' => 'UM3',
        'Atlantic/St_Helena' => 'UM2',
        'Atlantic/Azores' => 'UM1',
        'Europe/Dublin' => 'UTC',
        'Europe/Berlin' => 'UP1',
        'Europe/Warsaw' => 'UP2',
        'Asia/Baghdad' => 'UP3',
        'Asia/Tehran' => 'UP35',
        'Asia/Muscat' => 'UP4',
		'Asia/Kabul' => 'UP45',
        'Asia/Karachi' => 'UP5',
		'Asia/Kolkata' => 'UP55',
		'Asia/Katmandu' => 'UP575',
        'Asia/Dhaka' => 'UP6',
		'Asia/Rangoon' => 'UP65',
        'Asia/Bangkok' => 'UP7',
        'Asia/Hong_Kong' => 'UP8',
		'Australia/West' => 'UP875',
        'Asia/Seoul' => 'UP9',
        'Australia/Darwin' => 'UP95',
        'Australia/Melbourne' => 'UP10',
		'Australia/Lord_Howe' => 'UP105',
        'Asia/Magadan' => 'UP11',
		'Pacific/Norfolk' => 'UP115',
        'Pacific/Fiji' => 'UP12',
		'Pacific/Chatham' => 'UP1275',
		'Pacific/Tongatapu' => 'UP13'
		
    );
	
	foreach ($zones as $key => $z) {
		if ($tz == $z)
			return $key;
	}
}

/* End of file oo_helper.php */
/* Location: ./application/helpers/oo_helper.php */