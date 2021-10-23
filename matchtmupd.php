<?php
//   Copyright 2011-2021 John Collins

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
include 'php/match.php';
include 'php/matchdate.php';

$Connection = opendatabase(true);
lg_html_header("Adjust Matches");
print <<<EOT
<script language="javascript">
function okdel(mi, teama, teamb, date) {
	if (confirm("OK to delete match: " + teama + " -v- " + teamb + " on " + date))
		location = "delmatch.php?mi=" + mi;
}
</script>

EOT;
lg_html_nav();
print <<<EOT
<h1>Update Matches</h1>
<p>Click on one of the team names to update details of the specified match, or click
delete match to delete that match, add match to create a new match.</p>
<table class="matchesupd">
<tr>
<th>Date</th>
<th>Team A</th>
<th>Team B</th>
</tr>

EOT;

// Expecting to get called with div=division number

$div = $_GET['div'];

// If division number specified, create selection criterion string

$crit = "";
if (strlen($div) != 0)
	$crit = " WHERE divnum=$div";
$ret = $Connection->query("SELECT ind FROM lgmatch$crit ORDER BY divnum,matchdate,hteam,ateam");

if ($ret && $ret->num_rows > 0)  {
	$lastdiv = -99;
	while ($row = $ret->fetch_array())  {
		$ind = $row[0];
		$mtch = new Match($ind);
		$mtch->fetchdets();
		if ($mtch->Division != $lastdiv)  {
			$lastdiv = $mtch->Division;
			print "<tr><th colspan=\"3\" align=\"center\">Division $lastdiv</th></tr>\n";
		}
		print <<<EOT
<tr>
<td>{$mtch->Date->display()}</td>

EOT;
		// Only allow guy to fiddle with matches that haven't been played

		if ($mtch->Result == 'N' || $mtch->Result == 'P') {
			print <<<EOT
<td><a href="updmatch.php?{$mtch->urlof()}">{$mtch->Hteam->display_name()}</a></td>
<td><a href="updmatch.php?{$mtch->urlof()}">{$mtch->Ateam->display_name()}</a></td>

EOT;
			// Only allow guy to delete matches which have had no games played

			if ($mtch->Result == 'N')
				// Elide quotes from team names so we don't get in a muddle
				print <<<EOT
<td><a href="javascript:okdel({$mtch->query_ind()}, '{$mtch->Hteam->noquote()}','{$mtch->Ateam->noquote()}','{$mtch->Date->display()}')">Delete match</a></td>

EOT;
		}
		else {
			// If match has been played, just display the names
			print <<<EOT
<td>{$mtch->Hteam->display_name()}</td>
<td>{$mtch->Ateam->display_name()}</td>

EOT;
		}
print <<<EOT
</tr>

EOT;
	}
}
else {
	print "<tr><td colspan=\"3\" align=\"center\">No matches yet please create some</td></tr>\n";
}

// Add link to add match

if (strlen($div) != 0)
	print <<<EOT
<tr>
<td colspan="3" align="center"><a href="addmatch.php?div=$div">Add a match for division $div</a></td>
</tr>

EOT;
print "</table>\n";
lg_html_footer();
?>
