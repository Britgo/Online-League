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

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';

$Connection = opendatabase(false, false);  // Don't even think about logging in

$userid = $_POST['user_id'];
$passwd = $_POST['passwd'];

$quserid = $Connection->real_escape_string($userid);
$ret = $Connection->query("SELECT first,last,password FROM player WHERE user='$quserid'");

if (!$ret || $ret->num_rows == 0)  {
	err_html_header("Unknon user");
	$quserid = htmlspecialchars($userid);
	print <<<EOT
<p>User $quserid is not known.
Please <a href="index.php" title="Go back to home page">click here</a> to return to the top.</p>

EOT;
	exit(0);
}

$row = $ret->fetch_assoc();
if ($passwd != $row['password'])  {
	err_html_header("Incorrect password", NULL, "nomarg");
	print <<<EOT
<p>The password is not correct.
Please <a href="index.php" title="Go back to home page">click here</a> to return to the top.</p>
</body>
</html>

EOT;
	exit(0);
}

set_login($userid);
print <<<EOT
<html>
<head>
<title>Login OK</title>
</head>
<body onload="onl();">
<script language="javascript">
function onl() {

EOT;

$prev = $_SERVER['HTTP_REFERER'];
if (strlen($prev) == 0 || preg_match('/newacct/', $prev) != 0)
	$prev = 'index.php';
print <<<EOT
	document.location = "$prev";
}
</script>
</body>
</html>

EOT;
?>
