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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';

$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}

$which = $_GET["w"];
switch  ($which)  {
default:
	$mess = "Unknown default type";
	include 'php/wrongentry.php';
	exit(0);
case  'H':
case  'A':
	$mtch->set_defaulted($which);
	break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Match Defaulted";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Match Defaulted</h1>
<p>
The match between
<?php
$wteam = $mtch->Result == 'H'? $mtch->Hteam: $mtch->Ateam;
print <<<EOT
{$mtch->Hteam->display_name()} and
{$mtch->Ateam->display_name()}
on
{$mtch->Date->display()} has been defaulted in favour of {$wteam->display_name()}.
</p>

EOT;
?>
<p>Click <a href="matchupd.php" title="Edit some other match">here</a> to edit some other match.</p>
</div>
</div>
</body>
</html>
