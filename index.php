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

include 'php/session.php';
include 'php/opendatabase.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "BGA Online League";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$hasfoot = true;
include 'php/nav.php'; ?>
<h1>BGA Online Leagues</h1>
<img src="images/gogod_shield.medium.jpg" width="359" height="400" alt="Shield picture" align="left" border="0" hspace="20" vspace="5">
<p>Welcome to the BGA online league site. Please use the menus on the left to select a destination.</p>
<p>To find out more about the league,please <a href="info.php" title="Give more information">go here</a>.</p>
<p>A full description of playing games is to be found
under <a href="playing.php" title="Read description of rules and instructions for playing">rules</a>.</p>
<p>To see the current league standings, please <a href="league.php" title="Show currennt standings">go here</a>.
</div>
</div>
<div id="Footer">
<div class="innertube">
<hr>
<p class="note">
This website was designed, authored and programmed by
<a href="http:/john.collins.name" target="_blank">John Collins</a>.
</p>
<?php
$dat = date("Y");
print <<<EOT
<p class="note">Copyright &copy; John Collins 2009-$dat. Licensed under

EOT;
?>
<a href="http://www.gnu.org/licenses/">GPL v3</a>.</p>
</div>
</div>
</body>
</html>
