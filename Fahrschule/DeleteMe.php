<?php
// +----------------------------------------------------------------------+
// | webEdition Online Installer                                          |
// +----------------------------------------------------------------------+
// | Tue, 05 Mar 2013 11:49:26 +0100                                      |
// +----------------------------------------------------------------------+
// | Version                                                              |
// +----------------------------------------------------------------------+
// | PHP version 5.2.4 or greater                                         |
// +----------------------------------------------------------------------+
// | Applications: webEdition, pageLogger                                 |
// | Default Application: webEdition                                      |
// +----------------------------------------------------------------------+

// Start the session
session_start();

$http = "http" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] =='on' ? "s" : "") . "://";
$host = $_SERVER['HTTP_HOST'];
$setup = (dirname($_SERVER['SCRIPT_NAME'])!=DIRECTORY_SEPARATOR?dirname($_SERVER['SCRIPT_NAME']):"") . "/setup.php";
$parameters = "";
if(isset($_REQUEST['debug'])) {
	$parameters .= "debug=".$_REQUEST['debug']."&";
}
if($parameters != "") {
	$parameters = "?" . $parameters;
}
$GLOBALS['redirect'] = $http . $host . $setup . $parameters;
if(file_exists("../OnlineInstaller.php")) {
	@chmod("../OnlineInstaller.php", 0777);

	// Cannot unlink installer file
	if(!@unlink("../OnlineInstaller.php")) {
		echo getErrorScreen("Security Hint!", "Cannot delete file 'OnlineInstaller.php'.<br />For security reasons delete this file manually.", true);

	// Cannot unlink installer log file
	} elseif(file_exists("../OnlineInstaller.log.php") && !@rename("../OnlineInstaller.log.php", "../OnlineInstaller/OnlineInstaller.log.php")) {
		echo getErrorScreen("Security Hint!", "Cannot move file 'OnlineInstaller.log.php'.", true);

	// all files are written, now redirect to the real installer
	} else {
		header("Location: " . $http . $host . $setup . $parameters);
	}
} else {
	echo getErrorScreen("Security Hint!", "The file 'OnlineInstallerDeleteMe.php' is not used anymore.<br />For security reasons delete this file manually.", true);
	die();
}

function getErrorScreen($headline, $content, $showForm = true) {
	$form = "";
	if($showForm) {
		$form = <<<EOFORM
<form name="hint" action="{$GLOBALS['redirect']}" method="get">
<table id="hint">
<tr>
	<td><input type="checkbox" id="read" name="read" value="read" onclick="checkCheckBox(this);"/></td>
	<td><label for="read">I have read this hint and will continue with the installation</label></td>
</tr>
</table>
</form>
EOFORM;
		$js = <<<EOFORM
	<script type="text/JavaScript">
	function checkCheckBox(field) {
		if(field.checked) {
			document.forms.hint.submit();
		}
	}
	</script>
EOFORM;
	}
	$errorScreen = <<<EOIF
<html>
<head>
	<title>Online Installer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
	<style type="text/css">
	body {
		margin-top			: 100px;
		text-align			: center;
	}
	h1 {
		padding				: 5px;
		width				: 500px;
		color				: #ff0000;
		font-size			: 12px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		font-weight			: bold;
		margin-top			: 4px;
		border-style		: none none solid none;
		border-width		: 1px;
		border-color		: #ff0000;
	}
	p {
		width				: 500px;
		font-size			: 10px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		line-height			: 16px;
	}
	#hint {
		width				: 490px;
		cellpadding			: 5px;
		cellspacing			: 5px;
		vertical-align		: middle;
		font-size			: 10px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		line-height			: 16px;
	}
	</style>
{$js}
</head>
<body>
<div align="center">
	<h1>{$headline}</h1>
	<p>
		{$content}
	</p>
{$form}
</div>
</body>
</html>
EOIF;
	return $errorScreen;
}
?>