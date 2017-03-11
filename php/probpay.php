<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Payment error</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<h1>Payment error.</h1>
<?php

//   Copyright 2012 John Collins

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

// If from API, grab codes from parsed response array

$apimsg = "";
if (isset($parsedresp))
	$apimsg = $parsedresp["L_SHORTMESSAGE0"] . ":" . $parsedresp["L_LONGMESSAGE0"] . " (" . $parsedresp["L_ERRORCODE0"] . ")";
$qmess = htmlspecialchars($mess);
print <<<EOT
<p>
Sorry but there was a problem setting up your payment.
Message was $qmess.
</p>

EOT;
if (strlen($apimsg) != 0)
	print <<<EOT
<p>The PayPal API reported: $apimsg</p>

EOT;
?>
<p>Please restart at the top by <a href="index.php">clicking here</a>.</p>
<p>If necessary, please tell John Collins
<a href="mailto:jmc@toad.me.uk">jmc@toad.me.uk</a> about this.
</p>
</body>
</html>
