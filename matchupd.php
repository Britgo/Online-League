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
include 'php/match.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update Matches";
include 'php/head.php';
?>
<body onunload="javascript:killwind()">
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
var miwind = null;

function killwind() {
	if (miwind) {
		miwind.close();
		miwind = null;
	}
}

function initmatches(div) {
	killwind();
	miwind = window.open("matchsup.php?div="+div, "Initialise Matches", "width=450,height=400,resizeable=yes,scrollbars=yes");
}

function okdel(div) {
	killwind();
	if (confirm("OK to delete matches for Division " + div))
		location = "matchesdel.php?div=" + div;
}

function endseas() {
	killwind();
	if (confirm("Sure you want to end the season and draw outstanding games"))
		location = "closeseason.php";
}

function archive() {
	killwind();
	if (confirm("Sure you want to archive played matches"))
		location = "archive.php";
}
</script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Update Matches</h1>
<p>
Use this page to allocate matches and assign players. 
</p>
<?php
$maxdiv = max_division();
for ($div = 1;  $div <= $maxdiv;  $div++)  {
	print <<<EOT
<h2>Division $div</h2>
EOT;
	$nm = count_matches_for($div);
	if ($nm == 0)  {
		print <<<EOT
<p>
Click <a href="javascript:initmatches($div)">here</a> to perform the draw of matches for division $div.
</p>
EOT;
	}
	else  {
		print <<<EOT
<p>Click <a href="matchtmupd.php?div=$div">here</a> to allocate/update team members for matches in division $div.</p>
<p>Click <a href="javascript:okdel($div)">here</a> to delete the matches for division $div.</p>
EOT;
	}
	print <<<EOT
<p>
Click <a href="addmatch.php?div=$div">here</a> to create an individual match in division $div
outside the draw, e.g. for tie-breaks.
</p>

EOT;
}
?>
<h2>End Season</h2>
<p>Click <a href="javascript:endseas()">here</a> to delete unplayed matches and mark as drawn
all outstanding games in partly-played matches.</p>
<h2>Complete league and assign to history</h2>
<p>Click <a href="javascript:archive()">here</a> to consign played matches to history,
promote and relegate teams ready to draw for next season.</p>
</div>
</div>
</body>
</html>
