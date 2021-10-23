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
include 'php/season.php';
include 'php/teambase.php';
include 'php/matchbase.php';
include 'php/histteam.php';
include 'php/histmatch.php';
include 'php/matchdate.php';
include 'php/params.php';
include 'php/itrecord.php';

$Connection = opendatabase();
try {
	$seas = new Season();
	$seas->fromget();
	$seas->fetchdets();
}
catch (SeasonException $e) {
   wrongentry($e->getMessage());
}
lg_html_header("League Standings");
lg_html_nav();
print <<<EOT
<h1>Historic League Standings</h1>
<p>This is the final league table for
<b>{$seas->display_name()}</b>, the start date for which was
{$seas->display_start()} and the end was {$seas->display_end()}.</p>
<div align="center">

EOT;
$pars = new Params();
$pars->fetchvalues();
$ml = hist_max_division($seas);
for ($d = 1; $d <= $ml; $d++) {
	$tl = hist_list_teams($seas, $d);
	$cn = 7 + count($tl);
	print <<<EOT
<table class="league">
<tr><th colspan="$cn" align="center">Division $d</th></tr>
<tr>
<th>Team</th>

EOT;

	// Historical teams now have sort order saved

	foreach ($tl as $t)  {
		$t->fetchdets();
	}
	usort($tl, 'hist_score_compare');
		// Insert column header

	foreach ($tl as $t)  {
		$hd = substr($t->Name, 0, 3);
		print "<th>$hd</th>\n";
	}
	print <<<EOT
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<th>F</th>
<th>J</th>
<th>A</th>
</tr>

EOT;
	$maxrank = $tl[0]->Sortrank;
	$minrank = $tl[count($tl)-1]->Sortrank;
	// This avoids showing prom/releg if they're all the same as with nothing played.
	if ($maxrank == $minrank)
		$maxrank = $minrank = -9999999;
	foreach ($tl as $t) {
		$n = $t->display_name();
		if ($t->Sortrank == $maxrank)
			$n = "<span class=\"prom\">$n</span>";
		elseif ($t->Sortrank == $minrank)
			$n = "<span class=\"releg\">$n</span>";
		$n = "<a href=\"histteamdisp.php?{$t->urlof()}&{$seas->urlof()}\" class=\"nound\">$n</a>";
		print <<<EOT
<tr>
<td>$n</td>

EOT;
		foreach ($tl as $ot) {
			$reca = $t->record_against($ot);
			print "<td>{$reca->display()}</td>\n";
		}
		print <<<EOT
<td align="right">{$t->Playedm}</td>
<td align="right">{$t->Wonm}</td>
<td align="right">{$t->Drawnm}</td>
<td align="right">{$t->Lostm}</td>
<td align="right">{$t->Wong}</td>
<td align="right">{$t->Drawng}</td>
<td align="right">{$t->Lostg}</td>
</tr>

EOT;
	}
	print "</table>\n";
	if ($d != $ml)
		print "<br><br><br>\n";
}

print <<<EOT
</div>
<p>Key to above: Matches <b>P</b>layed, <b>W</b>on, <b>D</b>rawn, <b>L</b>ost, Games <b>F</b>or, <b>J</b>igo and Games <b>A</b>gainst.
<span class="prom">Promotion Zone</span> and <span class="releg">Relegation Zone</span>.</p>
<h2>Other Seasons</h2>
<p>Please <a href="javascript:history.back()">click here</a> to go back.</p>

EOT;
lg_html_footer();
?>
