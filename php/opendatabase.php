<?php

//   Copyright 2009-2017 John Collins

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

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

include 'credentials.php';

try {
	$dbcred = getcredentials('league');
}
catch (Credentials_error $e)  {
	$mess = htmlspecialchars($e->getMessage());
	print <<<EOT
<html>
<head>
<title>Credentials error</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<h1>Credentials Error</h1>
<p>Sorry but we were unable to fetch the credentials for the database - cannot proceed.
Error message was: $mess.
</p>
<p>
Please tell John Collins
<a href="mailto:jmc@toad.me.uk">jmc@toad.me.uk</a> about this ASAP, and advise the context
wherein it occurred. Thank you.</p>
<p>Please now go to the top of the site by <a href="index.php">clicking here</a>.</p>
</body>
</html>
EOT;
	exit(0);
}

if  (!mysql_connect("localhost", $dbcred->Username, $dbcred->Password)  ||  !mysql_select_db($dbcred->Databasename)) {

	$mess = mysql_error();

print <<<EOT
<html>
<head>
<title>Database error</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<h1>Database Error</h1>
<p>Sorry but there has been a database error - cannot proceed.
Database error message was $mess.
</p>
<p>
Please tell John Collins
<a href="mailto:jmc@toad.me.uk">jmc@toad.me.uk</a> about this ASAP, and advise the context
wherein it occurred. Thank you.</p>
<p>Please now go to the top of the site by <a href="index.php">clicking here</a>.</p>
</body>
</html>
EOT;
	exit(0);
}
?>
