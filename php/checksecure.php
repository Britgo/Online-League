<?php
//   Copyright 2011-2021 John Collins

// *********************************************************************
// Please do not edit the live file directly as it will break the "Git"
// mechanism to update the live files automatically when a new version
// is pushed. Thanks!
// *********************************************************************

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

if (strlen($_SERVER['HTTPS']) == 0) {
	print <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Insecure access/title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<h1>Insecure access</h1>
<p>Sorry but this page needs to be entered securely (with https: at the
start of the URL).</p>
<p>Please start again from the top by <a href="index.php">clicking here</a>.</p>
</body>
</html>

EOT;
	exit(0);
}
?>
