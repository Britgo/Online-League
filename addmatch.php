<?php
//   Copyright 2009-2021 John Collins

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
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';

$div = $_GET['div'];
if (strlen($div) == 0)
	wrongentry("No division");

$Connection = opendatabase(true);

$mtch = new Match(0, $div);
$teams = list_teams($div);

lg_html_header("Add Match - Division $div");
print <<<EOT
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

EOT;

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

lg_html_nav();
print <<<EOT
<h1>Create Match</h1>
<p>
Please select teams and date for the required match.
</p>
<form name="matchform" action="addmatch2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkteamsvalid()">
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
print <<<EOT
days to play the games.</p>
<p>
<input type="submit" value="Click here"> when done.
</p>
</form>

EOT;
lg_html_footer();
?>
