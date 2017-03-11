<?php
//   Copyright 2009 John Collins

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
include 'php/game.php';

$div = $_GET['div'];
if (strlen($div) == 0)  {
	$mess = "No division?";
	include 'php/wrongentry.php';
	exit(0);	
}
$mtch = new Match(0, $div);
$teams = list_teams($div);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Add Match - Division $div";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function checkteamsvalid() {
	var form = document.matchform;
	var ht = form.hteam;
	var at = form.ateam;
	if (ht.selectedIndex < 0) {
		alert("No team A selected");
		return false;
	}
	if (at.selectedIndex < 0)  {
		alert("No team B selected");
		return false;
	}
	if (ht.selectedIndex == at.selectedIndex) {
		alert("Both teams selected are the same");
		return false;
	}
	return true;
}
</script>
<?php

// Generate team select code

function teamselect($name, $tl) {
	print <<<EOT
<select name="$name" size="0">
EOT;
	foreach ($tl as $t)
		print <<<EOT
<option>{$t->display_name()}</option>
EOT;
	print "</select>\n";
}
?>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Create Match</h1>
<p>
Please select teams and date for the required match.
</p>
<form name="matchform" action="addmatch2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkteamsvalid()">
<?php
print <<<EOT
<input type="hidden" name="div" value="$div">
<p>
Match is between
EOT;
teamselect('hteam', $teams);
print "and";
teamselect('ateam', $teams);
print <<<EOT
</p>
<p>
EOT;
$mtch->Date->dateopt("Date of match");
print "with";
$mtch->slackdopt();
?>
days to play the games.</p>
<p>
<input type="submit" value="Click here"> when done.
</p>
</form>
</div>
</div>
</body>
</html>
