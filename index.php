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
<h2>Congratulations to the 2015 winners!</h2>
<p>Congratulations to the <b>Cambridge A</b> team (Lou Yuxiang, Paul Smith, Atta Chui and James Wood)
for winning the seventh season of the online league.
In second place were Edinburgh 1. Winning division 2 and promotion to division 1 were UK Youth.</p>
<p>The winning teams received prizes consisting of places in Guo Juan's Group Classes and subscriptions to the Internet Lectures and Training system.
See the <a href="http://internetgoschool.com/index.vhtml" target="_blank">Internet Go School site</a> for more details.</p> 
<h2>Winning teams to date</h2>
<p>The following are the winning teams to date:</p>
<table cellpadding="2" cellspacing="2">
<tr><th>Season</th><th>Winner</th></tr>
<tr><td>October 2009 to February 2010</td><td>Cambridge</td></tr>
<tr><td>May to December 2010</td><td>Dundee</td></tr>
<tr><td>All of 2011</td><td>Central London Go Club A</td></tr>
<tr><td>All of 2012</td><td>Edinburgh</td></tr>
<tr><td>All of 2013</td><td>Edinburgh A</td></tr>
<tr><td>All of 2014</td><td>Central London Go Club A</td></tr>
<tr><td>All of 2015</td><td>Cambridge A</td></tr>
</table>
<p>You can see the current league standings and historical records using the menu items to the left, together with historical records.
In the majority of cases you can download and review the actual games.</p>
<h2>Information</h2>
<p>For more information about the league, please <a href="info.php" title="Give more information">go here</a>.</p>
<h2>Joining</h2>
<p>The 2016 league is now recruiting new teams and players for the new season which we hope to get going in February 2016.</p>
<p>We can easily add extra players to teams, so please let us know if you would like to join in. We normally group players from the same locale into teams,
but we are happy to accommodate other players as needed.</p>
<h2>Rules</h2>
<p>A full description of playing games is to be found
under <a href="playing.php" title="Read description of rules and instructions for playing">rules</a>.</p>
<h2>Prizes</h2>
<p>Prizes for 2016 and subsequent years awarded to the winning team in each division will be vouchers redeemable with
<a href="http://internetgoschool.com/index.vhtml" target="_blank">Guo Juan's Internet Go School</a>.</p>
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
