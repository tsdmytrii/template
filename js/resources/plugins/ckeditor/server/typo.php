<?php

$html = stripslashes(urldecode($_POST['redactor']));
//echo typo($html);
echo $html;
require_once('../../../../../application/third_party/htmlpurifier/HTMLPurifier.auto.php');

//require_once '/path/to/htmlpurifier/library/HTMLPurifier.auto.php';
//echo html_entity_decode('
//<p>Normal  0      false  false  false                     MicrosoftInternetExplorer4    /* Style Definitions */ table.MsoNormalTable{mso-style-name:"Обычная таблица";mso-tstyle-rowband-size:0;mso-tstyle-colband-size:0;mso-style-noshow:yes;mso-style-parent:"";mso-padding-alt:0cm 5.4pt 0cm 5.4pt;mso-para-margin:0cm;mso-para-margin-bottom:.0001pt;mso-pagination:widow-orphan;font-size:10.0pt;font-family:"Times New Roman";mso-ansi-language:#0400;mso-fareast-language:#0400;mso-bidi-language:#0400;}</p>
//<p class="MsoNormal">Hcedc rhh he he ehf hehwfv<i>ewb b</i>b wvnwb dc<b style="mso-bidi-font-weight:normal">bdvennwb b</b></p>');
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
//echo strip_tags(word_cleanup($purifier->purify($html)));

function cleanHTML($html) {
/// <summary>
/// Removes all FONT and SPAN tags, and all Class and Style attributes.
/// Designed to get rid of non-standard Microsoft Word HTML tags.
/// </summary>
// start by completely removing all unwanted tags

	$html = ereg_replace("<(/)?(font|span|del|ins)[^>]*>", "", $html);

// then run another pass over the html (twice), removing unwanted attributes

	$html = ereg_replace("<([^>]*)(class|lang|style|size|face)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>", "<\\1>", $html);
	$html = ereg_replace("<([^>]*)(class|lang|style|size|face)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>", "<\\1>", $html);

	return $html;
}

function word_cleanup($str) {
	$pattern = "/<(\w+)>(\s|&nbsp;)*<\/\1>/";
	$str = preg_replace($pattern, '', $str);
	return mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8');
}

function typo($html, $lang = 'ru') {
	$html = stripslashes($html);

	// remove pre
	preg_match_all('/<pre>([\\w\\W]*?)<\/pre>/i', $html, $matches);

	foreach ($matches[0] as $k => $v) {
		$html = str_replace($v, md5($v), $html);
		$pre_cache[md5($v)] = $v;
	}

	// remove style
	preg_match_all('/<style([\\w\\W]*?)<\/style>/i', $html, $matches);

	foreach ($matches[0] as $k => $v) {
		$html = str_replace($v, md5($v), $html);
		$pre_css[md5($v)] = $v;
	}

	// remove script
	preg_match_all('/<script([\\w\\W]*?)<\/script>/i', $html, $matches);

	foreach ($matches[0] as $k => $v) {
		$html = str_replace($v, md5($v), $html);
		$pre_script[md5($v)] = $v;
	}

	// remove tags
	preg_match_all('/<(.*?)>/i', $html, $tag_cache);

	foreach ($tag_cache[1] as $k => $v) {
		$html = str_replace($tag_cache[0][$k], '<' . md5($v) . '>', $html);
		$full_cache['<' . md5($v) . '>'] = $tag_cache[0][$k];
	}

	// Blank
	$html = preg_replace('/(\s)+/i', "$1", $html);
	$html = preg_replace('/(\n?\A)\s+(?!\-)/i', "$1", $html);
	$html = preg_replace('/\s\z/i', "", $html);

	// Mdash
	$html = preg_replace('/(>|\A|\n)\-\s/i', "$1&mdash;&nbsp;", $html);

	// One-two words
	$html = preg_replace('/(?<![-:])\b([\w]{1,2}\b(?:[,:;]?))(?!\n)\s/i', "$1&nbsp;", $html);
	if ($lang == 'ru') {
		$html = preg_replace('/(\s|&nbsp;)(же|ли|ль|бы|б|ж|ка)([\.,!\?:;])?&nbsp;/i', "&nbsp;$2$3 ", $html);
	}

	// Replace special characters
	$html = preg_replace('/·/i', "&bull;", $html);
	$html = preg_replace('/•/i', "&bull;", $html);

	$html = preg_replace('/«/i', "&laquo;", $html);
	$html = preg_replace('/»/i', "&raquo;", $html);

	$html = preg_replace('/\.{3}/i', "&hellip;", $html);
	$html = preg_replace('/\((c|с)\)/i', "&copy;", $html);

	$html = preg_replace('/\(r\)/i', '<sup><small>&reg;</small></sup>', $html);
	$html = preg_replace('/\(tm\)/i', '<sup><small>&trade;</small></sup>', $html);
	$html = preg_replace('/(\d+)(x|х)(\d+)/i', '$1&times;$3', $html);

	$html = preg_replace('/(\+\-|\-\+|\+\-)/i', '&plusmn;', $html);
	$html = preg_replace('/([ ]+|&nbsp;)\-\s+/i', '&nbsp;&mdash; ', $html);

	// Russian quotes
	if ($lang == 'ru') {
		$html = preg_replace('/(?<!\s)([!?]|&hellip;)?"(?!\b)/i', '$1&raquo;', $html);
		$html = preg_replace('/(?<!\b)"(?!\s)/i', '&laquo;', $html);
	}

	// Dash
	$html = preg_replace('/(?<!\-)(?=\b)(\w+)\-(\w+)(?<=\b)(?!\-)/i', '<span style="white-space: none;">$1-$2</span>', $html);

	if (isset($pre_cache))
		foreach ($pre_cache as $k => $v)
			$html = str_replace($k, $v, $html);
	if (isset($pre_css))
		foreach ($pre_css as $k => $v)
			$html = str_replace($k, $v, $html);
	if (isset($pre_script))
		foreach ($pre_script as $k => $v)
			$html = str_replace($k, $v, $html);

	// return tags
	if (isset($full_cache))
		foreach ($full_cache as $k => $v)
			$html = str_replace($k, $v, $html);

	return $html;
}

?>