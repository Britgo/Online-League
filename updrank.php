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

// This is to let team captains update ranks of members of their team.

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
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update member ranks";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Adjust ranks of team members</h1>
<?php
print <<<EOT
<p>Use this page to adjust ranks of members of the team
{$team->display_name()} ({$team->display_description()})
in division {$team->display_division()}.</p>
<form name="trform" action="updrank2.php" method="post" enctype="application/x-www-form-urlencoded">
{$team->save_hidden()}
<table class="teamdisp">
<tr><th>Player</th><th>Rank</th></tr>
EOT;
$membs = $team->list_members();
$n=0;
foreach ($membs as $m) {
	$m->fetchdets();
	print "<tr><td>{$m->display_name(false)}</td>\n<td>";
	$m->rankopt($n);
	print "</td></tr>\n";
	$n++;
}
?>
</table>
<p>
Make any adjustments and
<input type="submit" value="Click here"> or <input type="reset" value="Reset form">
</p>
</form>
</div>
</div>
</body>
</html>
