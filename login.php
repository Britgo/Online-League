<?php
//   Copyright 2011 John Collins

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

include 'php/opendatabase.php';

$userid = $_POST['user_id'];
$passwd = $_POST['passwd'];

$quserid = mysql_real_escape_string($userid);
$ret = mysql_query("select first,last,password,admin from player where user='$quserid'");

if (!$ret || mysql_num_rows($ret) == 0)  {
	print <<<EOT
<html>
<head>
<title>Unknown User</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="nomarg">
<h1>Unknown User</h1>
<p>User $userid is not known.
Please <a href="index.php" title="Go back to home page">click here</a> to return to the top.
</p>
</body>
</html>
EOT;
	exit(0);
}
$row = mysql_fetch_assoc($ret);
if ($passwd != $row['password'])  {
	print <<<EOT
<html>
<head>
<title>Incorrect Password</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="nomarg">
<h1>Incorrect Password</h1>
<p>The password is not correct.
Please <a href="index.php" title="Go back to home page">click here</a> to return to the top.
</p>
</body>
</html>
EOT;
	exit(0);
}
$username = $row['first'] . ' ' . $row['last'];
$priv = $row['admin'];
ini_set("session.gc_maxlifetime", "604800");
$phpsessiondir = $_SERVER["DOCUMENT_ROOT"] . "/league/phpsessions";
if (is_dir($phpsessiondir))
	session_save_path($phpsessiondir);
session_set_cookie_params(604800);
session_start();
$_SESSION['user_id'] = $userid;
$_SESSION['user_name'] = $username;
$_SESSION['user_priv'] = $priv;
setcookie("user_id", $userid, time()+60*60*24*60, "/");
setcookie("user_name", $username, time()+60*60*24*60, "/");
?>
<html>
<head>
<title>Login OK</title>
</head>
<body onload="onl();">
<script language="javascript">
function onl() {
<?php
$prev = $_SERVER['HTTP_REFERER'];
if (strlen($prev) == 0 || preg_match('/newacct/', $prev) != 0)
	$prev = 'index.php';
print <<<EOT
	document.location = "$prev";

EOT;
?>
}
</script>
</body>
</html>
