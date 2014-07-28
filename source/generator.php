<?php

/**
 * Copyright 2014 www.delight.im <info@delight.im>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

ini_set('display_errors', 'stdout');
error_reporting(E_ALL);

mb_internal_encoding('utf-8');
set_time_limit(0);

header('Content-type: text/plain; charset=utf-8');
mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'en_US.UTF-8');

require_once('config.php');

/**
 * Returns the UTF-8 representation of the given Unicode codepoint
 * 
 * @param int $c the hexadecimal Unicode codepoint
 * @throws Exception if the given codepoint could not be represented in UTF-8
 * 
 * @author velcrow (Stack Overflow)
 */
function utf8($c) {
	if ($c <= 0x7F) {
		return chr($c);
	}
	if ($c <= 0x7FF) {
		return chr(($c>>6)+192).chr(($c&63)+128);
	}
	if ($c <= 0xFFFF) {
		return chr(($c>>12)+224).chr((($c>>6)&63)+128).chr(($c&63)+128);
	}
	if ($c <= 0x1FFFFF) {
		return chr(($c>>18)+240).chr((($c>>12)&63)+128).chr((($c>>6)&63)+128).chr(($c&63)+128);
	}
	else {
		throw new Exception('Could not represent in UTF-8: '.$c);
	}
}

/**
 * Unicode-safe exec() function
 * 
 * @author code_angel (Stack Overflow)
 */
function unicode_exec($cmd, &$output=NULL, &$return=NULL) {
	if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
		return exec($cmd, $output, $return);
	}

	$cmdStr = "@echo off\r\n";
	$cmdStr .= "@chcp 65001 > nul\r\n";
	$cmdStr .= "@cd \"".getcwd()."\"\r\n";
	$cmdStr .= $cmd;

	$tempfile = 'php_exec.bat';
	file_put_contents($tempfile, $cmdStr);

	exec('start /b '.$tempfile, $output, $return);

	array_pop($output);
	array_pop($output);

	if (count($output) == 1) {
		$output = $output[0];
	}

	unlink($tempfile);

	return $output;
}

function codePointToHex($singleCodePoint) {
	return sprintf('%04s', dechex($singleCodePoint));
}

function getFilenameFromHexName($hexName) {
	return CONFIG_OUTPUT_DIR.'/emoji_'.$hexName.'.png';
}

class ImageMagick {

	private static $imageMagickPath;
	private static $font;
	private static $fontSize;
	private static $color;
	private static $size;
	private static $offset;

	public static function init($imageMagickPath) {
		self::$imageMagickPath = $imageMagickPath;
		self::$font = 'Arial';
		self::$fontSize = 28;
		self::$color = 'black';
		self::$size = 32;
		self::$offset = array(0, 0);
	}
	
	public static function setFont($font) {
		self::$font = $font;
	}
	
	public static function setFontSize($fontSize) {
		self::$fontSize = intval($fontSize);
	}
	
	public static function setColor($color) {
		self::$color = $color;
	}
	
	public static function setSize($size) {
		self::$size = intval($size);
	}
	
	public static function setOffset($offsetX, $offsetY) {
		self::$offset[0] = intval($offsetX);
		self::$offset[1] = intval($offsetY);
	}
	
	public static function saveAsPNG($text, $filename) {
		unicode_exec('"'.self::$imageMagickPath.'" -size '.self::$size.'x'.self::$size.' xc:none -fill '.self::$color.' -font "'.self::$font.'" -gravity center -pointsize '.self::$fontSize.' -annotate +'.self::$offset[0].'+'.self::$offset[1].' "'.escapeshellarg($text).'" PNG:"'.$filename.'" 2>&1');
	}

}

/**
 * Code points for 722 emoji from Unicode 6.3
 *
 * Reference: EmojiSources.txt by Unicode, Inc. (http://www.unicode.org/Public/UNIDATA/EmojiSources.txt)
 */
$codePoints = array(
	array(0x0023, 0x20E3),
	array(0x0030, 0x20E3),
	array(0x0031, 0x20E3),
	array(0x0032, 0x20E3),
	array(0x0033, 0x20E3),
	array(0x0034, 0x20E3),
	array(0x0035, 0x20E3),
	array(0x0036, 0x20E3),
	array(0x0037, 0x20E3),
	array(0x0038, 0x20E3),
	array(0x0039, 0x20E3),
	0x00A9,
	0x00AE,
	0x203C,
	0x2049,
	0x2122,
	0x2139,
	0x2194,
	0x2195,
	0x2196,
	0x2197,
	0x2198,
	0x2199,
	0x21A9,
	0x21AA,
	0x231A,
	0x231B,
	0x23E9,
	0x23EA,
	0x23EB,
	0x23EC,
	0x23F0,
	0x23F3,
	0x24C2,
	0x25AA,
	0x25AB,
	0x25B6,
	0x25C0,
	0x25FB,
	0x25FC,
	0x25FD,
	0x25FE,
	0x2600,
	0x2601,
	0x260E,
	0x2611,
	0x2614,
	0x2615,
	0x261D,
	0x263A,
	0x2648,
	0x2649,
	0x264A,
	0x264B,
	0x264C,
	0x264D,
	0x264E,
	0x264F,
	0x2650,
	0x2651,
	0x2652,
	0x2653,
	0x2660,
	0x2663,
	0x2665,
	0x2666,
	0x2668,
	0x267B,
	0x267F,
	0x2693,
	0x26A0,
	0x26A1,
	0x26AA,
	0x26AB,
	0x26BD,
	0x26BE,
	0x26C4,
	0x26C5,
	0x26CE,
	0x26D4,
	0x26EA,
	0x26F2,
	0x26F3,
	0x26F5,
	0x26FA,
	0x26FD,
	0x2702,
	0x2705,
	0x2708,
	0x2709,
	0x270A,
	0x270B,
	0x270C,
	0x270F,
	0x2712,
	0x2714,
	0x2716,
	0x2728,
	0x2733,
	0x2734,
	0x2744,
	0x2747,
	0x274C,
	0x274E,
	0x2753,
	0x2754,
	0x2755,
	0x2757,
	0x2764,
	0x2795,
	0x2796,
	0x2797,
	0x27A1,
	0x27B0,
	0x2934,
	0x2935,
	0x2B05,
	0x2B06,
	0x2B07,
	0x2B1B,
	0x2B1C,
	0x2B50,
	0x2B55,
	0x3030,
	0x303D,
	0x3297,
	0x3299,
	0x1F004,
	0x1F0CF,
	0x1F170,
	0x1F171,
	0x1F17E,
	0x1F17F,
	0x1F18E,
	0x1F191,
	0x1F192,
	0x1F193,
	0x1F194,
	0x1F195,
	0x1F196,
	0x1F197,
	0x1F198,
	0x1F199,
	0x1F19A,
	array(0x1F1E8, 0x1F1F3),
	array(0x1F1E9, 0x1F1EA),
	array(0x1F1EA, 0x1F1F8),
	array(0x1F1EB, 0x1F1F7),
	array(0x1F1EC, 0x1F1E7),
	array(0x1F1EE, 0x1F1F9),
	array(0x1F1EF, 0x1F1F5),
	array(0x1F1F0, 0x1F1F7),
	array(0x1F1F7, 0x1F1FA),
	array(0x1F1FA, 0x1F1F8),
	0x1F201,
	0x1F202,
	0x1F21A,
	0x1F22F,
	0x1F232,
	0x1F233,
	0x1F234,
	0x1F235,
	0x1F236,
	0x1F237,
	0x1F238,
	0x1F239,
	0x1F23A,
	0x1F250,
	0x1F251,
	0x1F300,
	0x1F301,
	0x1F302,
	0x1F303,
	0x1F304,
	0x1F305,
	0x1F306,
	0x1F307,
	0x1F308,
	0x1F309,
	0x1F30A,
	0x1F30B,
	0x1F30C,
	0x1F30F,
	0x1F311,
	0x1F313,
	0x1F314,
	0x1F315,
	0x1F319,
	0x1F31B,
	0x1F31F,
	0x1F320,
	0x1F330,
	0x1F331,
	0x1F334,
	0x1F335,
	0x1F337,
	0x1F338,
	0x1F339,
	0x1F33A,
	0x1F33B,
	0x1F33C,
	0x1F33D,
	0x1F33E,
	0x1F33F,
	0x1F340,
	0x1F341,
	0x1F342,
	0x1F343,
	0x1F344,
	0x1F345,
	0x1F346,
	0x1F347,
	0x1F348,
	0x1F349,
	0x1F34A,
	0x1F34C,
	0x1F34D,
	0x1F34E,
	0x1F34F,
	0x1F351,
	0x1F352,
	0x1F353,
	0x1F354,
	0x1F355,
	0x1F356,
	0x1F357,
	0x1F358,
	0x1F359,
	0x1F35A,
	0x1F35B,
	0x1F35C,
	0x1F35D,
	0x1F35E,
	0x1F35F,
	0x1F360,
	0x1F361,
	0x1F362,
	0x1F363,
	0x1F364,
	0x1F365,
	0x1F366,
	0x1F367,
	0x1F368,
	0x1F369,
	0x1F36A,
	0x1F36B,
	0x1F36C,
	0x1F36D,
	0x1F36E,
	0x1F36F,
	0x1F370,
	0x1F371,
	0x1F372,
	0x1F373,
	0x1F374,
	0x1F375,
	0x1F376,
	0x1F377,
	0x1F378,
	0x1F379,
	0x1F37A,
	0x1F37B,
	0x1F380,
	0x1F381,
	0x1F382,
	0x1F383,
	0x1F384,
	0x1F385,
	0x1F386,
	0x1F387,
	0x1F388,
	0x1F389,
	0x1F38A,
	0x1F38B,
	0x1F38C,
	0x1F38D,
	0x1F38E,
	0x1F38F,
	0x1F390,
	0x1F391,
	0x1F392,
	0x1F393,
	0x1F3A0,
	0x1F3A1,
	0x1F3A2,
	0x1F3A3,
	0x1F3A4,
	0x1F3A5,
	0x1F3A6,
	0x1F3A7,
	0x1F3A8,
	0x1F3A9,
	0x1F3AA,
	0x1F3AB,
	0x1F3AC,
	0x1F3AD,
	0x1F3AE,
	0x1F3AF,
	0x1F3B0,
	0x1F3B1,
	0x1F3B2,
	0x1F3B3,
	0x1F3B4,
	0x1F3B5,
	0x1F3B6,
	0x1F3B7,
	0x1F3B8,
	0x1F3B9,
	0x1F3BA,
	0x1F3BB,
	0x1F3BC,
	0x1F3BD,
	0x1F3BE,
	0x1F3BF,
	0x1F3C0,
	0x1F3C1,
	0x1F3C2,
	0x1F3C3,
	0x1F3C4,
	0x1F3C6,
	0x1F3C8,
	0x1F3CA,
	0x1F3E0,
	0x1F3E1,
	0x1F3E2,
	0x1F3E3,
	0x1F3E5,
	0x1F3E6,
	0x1F3E7,
	0x1F3E8,
	0x1F3E9,
	0x1F3EA,
	0x1F3EB,
	0x1F3EC,
	0x1F3ED,
	0x1F3EE,
	0x1F3EF,
	0x1F3F0,
	0x1F40C,
	0x1F40D,
	0x1F40E,
	0x1F411,
	0x1F412,
	0x1F414,
	0x1F417,
	0x1F418,
	0x1F419,
	0x1F41A,
	0x1F41B,
	0x1F41C,
	0x1F41D,
	0x1F41E,
	0x1F41F,
	0x1F420,
	0x1F421,
	0x1F422,
	0x1F423,
	0x1F424,
	0x1F425,
	0x1F426,
	0x1F427,
	0x1F428,
	0x1F429,
	0x1F42B,
	0x1F42C,
	0x1F42D,
	0x1F42E,
	0x1F42F,
	0x1F430,
	0x1F431,
	0x1F432,
	0x1F433,
	0x1F434,
	0x1F435,
	0x1F436,
	0x1F437,
	0x1F438,
	0x1F439,
	0x1F43A,
	0x1F43B,
	0x1F43C,
	0x1F43D,
	0x1F43E,
	0x1F440,
	0x1F442,
	0x1F443,
	0x1F444,
	0x1F445,
	0x1F446,
	0x1F447,
	0x1F448,
	0x1F449,
	0x1F44A,
	0x1F44B,
	0x1F44C,
	0x1F44D,
	0x1F44E,
	0x1F44F,
	0x1F450,
	0x1F451,
	0x1F452,
	0x1F453,
	0x1F454,
	0x1F455,
	0x1F456,
	0x1F457,
	0x1F458,
	0x1F459,
	0x1F45A,
	0x1F45B,
	0x1F45C,
	0x1F45D,
	0x1F45E,
	0x1F45F,
	0x1F460,
	0x1F461,
	0x1F462,
	0x1F463,
	0x1F464,
	0x1F466,
	0x1F467,
	0x1F468,
	0x1F469,
	0x1F46A,
	0x1F46B,
	0x1F46E,
	0x1F46F,
	0x1F470,
	0x1F471,
	0x1F472,
	0x1F473,
	0x1F474,
	0x1F475,
	0x1F476,
	0x1F477,
	0x1F478,
	0x1F479,
	0x1F47A,
	0x1F47B,
	0x1F47C,
	0x1F47D,
	0x1F47E,
	0x1F47F,
	0x1F480,
	0x1F481,
	0x1F482,
	0x1F483,
	0x1F484,
	0x1F485,
	0x1F486,
	0x1F487,
	0x1F488,
	0x1F489,
	0x1F48A,
	0x1F48B,
	0x1F48C,
	0x1F48D,
	0x1F48E,
	0x1F48F,
	0x1F490,
	0x1F491,
	0x1F492,
	0x1F493,
	0x1F494,
	0x1F495,
	0x1F496,
	0x1F497,
	0x1F498,
	0x1F499,
	0x1F49A,
	0x1F49B,
	0x1F49C,
	0x1F49D,
	0x1F49E,
	0x1F49F,
	0x1F4A0,
	0x1F4A1,
	0x1F4A2,
	0x1F4A3,
	0x1F4A4,
	0x1F4A5,
	0x1F4A6,
	0x1F4A7,
	0x1F4A8,
	0x1F4A9,
	0x1F4AA,
	0x1F4AB,
	0x1F4AC,
	0x1F4AE,
	0x1F4AF,
	0x1F4B0,
	0x1F4B1,
	0x1F4B2,
	0x1F4B3,
	0x1F4B4,
	0x1F4B5,
	0x1F4B8,
	0x1F4B9,
	0x1F4BA,
	0x1F4BB,
	0x1F4BC,
	0x1F4BD,
	0x1F4BE,
	0x1F4BF,
	0x1F4C0,
	0x1F4C1,
	0x1F4C2,
	0x1F4C3,
	0x1F4C4,
	0x1F4C5,
	0x1F4C6,
	0x1F4C7,
	0x1F4C8,
	0x1F4C9,
	0x1F4CA,
	0x1F4CB,
	0x1F4CC,
	0x1F4CD,
	0x1F4CE,
	0x1F4CF,
	0x1F4D0,
	0x1F4D1,
	0x1F4D2,
	0x1F4D3,
	0x1F4D4,
	0x1F4D5,
	0x1F4D6,
	0x1F4D7,
	0x1F4D8,
	0x1F4D9,
	0x1F4DA,
	0x1F4DB,
	0x1F4DC,
	0x1F4DD,
	0x1F4DE,
	0x1F4DF,
	0x1F4E0,
	0x1F4E1,
	0x1F4E2,
	0x1F4E3,
	0x1F4E4,
	0x1F4E5,
	0x1F4E6,
	0x1F4E7,
	0x1F4E8,
	0x1F4E9,
	0x1F4EA,
	0x1F4EB,
	0x1F4EE,
	0x1F4F0,
	0x1F4F1,
	0x1F4F2,
	0x1F4F3,
	0x1F4F4,
	0x1F4F6,
	0x1F4F7,
	0x1F4F9,
	0x1F4FA,
	0x1F4FB,
	0x1F4FC,
	0x1F503,
	0x1F50A,
	0x1F50B,
	0x1F50C,
	0x1F50D,
	0x1F50E,
	0x1F50F,
	0x1F510,
	0x1F511,
	0x1F512,
	0x1F513,
	0x1F514,
	0x1F516,
	0x1F517,
	0x1F518,
	0x1F519,
	0x1F51A,
	0x1F51B,
	0x1F51C,
	0x1F51D,
	0x1F51E,
	0x1F51F,
	0x1F520,
	0x1F521,
	0x1F522,
	0x1F523,
	0x1F524,
	0x1F525,
	0x1F526,
	0x1F527,
	0x1F528,
	0x1F529,
	0x1F52A,
	0x1F52B,
	0x1F52E,
	0x1F52F,
	0x1F530,
	0x1F531,
	0x1F532,
	0x1F533,
	0x1F534,
	0x1F535,
	0x1F536,
	0x1F537,
	0x1F538,
	0x1F539,
	0x1F53A,
	0x1F53B,
	0x1F53C,
	0x1F53D,
	0x1F550,
	0x1F551,
	0x1F552,
	0x1F553,
	0x1F554,
	0x1F555,
	0x1F556,
	0x1F557,
	0x1F558,
	0x1F559,
	0x1F55A,
	0x1F55B,
	0x1F5FB,
	0x1F5FC,
	0x1F5FD,
	0x1F5FE,
	0x1F5FF,
	0x1F601,
	0x1F602,
	0x1F603,
	0x1F604,
	0x1F605,
	0x1F606,
	0x1F609,
	0x1F60A,
	0x1F60B,
	0x1F60C,
	0x1F60D,
	0x1F60F,
	0x1F612,
	0x1F613,
	0x1F614,
	0x1F616,
	0x1F618,
	0x1F61A,
	0x1F61C,
	0x1F61D,
	0x1F61E,
	0x1F620,
	0x1F621,
	0x1F622,
	0x1F623,
	0x1F624,
	0x1F625,
	0x1F628,
	0x1F629,
	0x1F62A,
	0x1F62B,
	0x1F62D,
	0x1F630,
	0x1F631,
	0x1F632,
	0x1F633,
	0x1F635,
	0x1F637,
	0x1F638,
	0x1F639,
	0x1F63A,
	0x1F63B,
	0x1F63C,
	0x1F63D,
	0x1F63E,
	0x1F63F,
	0x1F640,
	0x1F645,
	0x1F646,
	0x1F647,
	0x1F648,
	0x1F649,
	0x1F64A,
	0x1F64B,
	0x1F64C,
	0x1F64D,
	0x1F64E,
	0x1F64F,
	0x1F680,
	0x1F683,
	0x1F684,
	0x1F685,
	0x1F687,
	0x1F689,
	0x1F68C,
	0x1F68F,
	0x1F691,
	0x1F692,
	0x1F693,
	0x1F695,
	0x1F697,
	0x1F699,
	0x1F69A,
	0x1F6A2,
	0x1F6A4,
	0x1F6A5,
	0x1F6A7,
	0x1F6A8,
	0x1F6A9,
	0x1F6AA,
	0x1F6AB,
	0x1F6AC,
	0x1F6AD,
	0x1F6B2,
	0x1F6B6,
	0x1F6B9,
	0x1F6BA,
	0x1F6BB,
	0x1F6BC,
	0x1F6BD,
	0x1F6BE,
	0x1F6C0
);

ImageMagick::init(CONFIG_IMAGE_MAGICK_PATH_CONVERT);
ImageMagick::setFont(CONFIG_EMOJI_FONT_PATH);
ImageMagick::setFontSize(CONFIG_GLYPH_SIZE_POINT);
ImageMagick::setSize(CONFIG_ICON_SIZE);
ImageMagick::setOffset(round(CONFIG_ICON_SIZE * CONFIG_OFFSET_X_FACTOR), 0);

foreach ($codePoints as $codePoint) {
	if (is_array($codePoint)) {
		$textParts = array();
		$hexNameParts = array();
		foreach ($codePoint as $codePointItem) {
			$textParts[] = utf8($codePointItem);
			$hexNameParts[] = codePointToHex($codePointItem);
		}
		$text = implode('', $textParts);
		$hexName = implode('_', $hexNameParts);
	}
	elseif (is_int($codePoint)) {
		$text = utf8($codePoint);
		$hexName = codePointToHex($codePoint);
	}
	else {
		throw new Exception('Unknown type for: '.serialize($codePoint));
	}
	$filename = getFilenameFromHexName($hexName);
	ImageMagick::saveAsPNG($text, $filename);
}

echo 'Done '.utf8(0x1F60A);

?>
