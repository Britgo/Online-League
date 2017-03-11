<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Data error</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<h1>Data or program error.</h1>
<?php

//   Copyright 2010 John Collins

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

$qmess = htmlspecialchars($mess);
print <<<EOT
<p>
Sorry but there has been a database or program error.
Message was $qmess.
</p>
<p>Please restart at the top by <a href="index.php">clicking here</a>.</p>

EOT;
?>
<p>
Please tell John Collins
<a href="mailto:jmc@toad.me.uk">jmc@toad.me.uk</a> about this.
</p>
</body>
</html>
