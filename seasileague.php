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
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/matchdate.php';
include 'php/params.php';
include 'php/itrecord.php';
include 'php/season.php';

try {
	$Season = new Season();
	$Season->fromget();
	$Season->fetchdets();
}
catch (SeasonException $e) {
	$mess = $e->getMessage();
	include 'php/dataerror.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Historical Individual League Results";
include 'php/head.php';
?>
<body class="il">
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Historical Individual League Standings</h1>
<?php
print <<<EOT
<p>This is the final league table for <strong>{$Season->display_name()}</strong>,
which ran from {$Season->display_start()} to {$Season->display_end()}.</p>
<p>Only players who played games are included.</p>

EOT;
?>
<div align="center">
<?php
$pars = new Params();
$pars->fetchvalues();
$ml = max_ildivision();
for ($d = 1; $d <= $ml; $d++) {
	$players = list_hist_players_ildiv($d, $Season);
	if (count($players) < 2)  {
		print <<<EOT
No games played in Division $d.

EOT;
	}
	else  {
		$cn = 6 + count($players);
		print <<<EOT
<table class="league">
<tr>
<th colspan="$cn" align="center">Division $d</th>
</tr>
<tr>
<th>Player</th>
<th>Rank</th>

EOT;

		foreach ($players as $p) {
			$p->fetchdets();
			$p->get_scores($pars, $Season->Ind);
		}
		usort($players, 'ilscore_compare');
	
		// Insert column header
	
		foreach ($players as $p)  {
			print "<th>{$p->display_initials()}</th>\n";
		}

		print <<<EOT
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
</tr>

EOT;

		$maxrank = $players[0]->Sortrank;
		$minrank = $players[count($players)-1]->Sortrank;
		// This avoids showing prom/releg if they're all the same as with nothing played.
		if ($maxrank == $minrank)
			$maxrank = $minrank = -9999999;
		foreach ($players as $p) {
			$n = $p->display_name(false);
			if ($p->Sortrank == $maxrank)
				$n = "<span class=\"prom\">$n</span>";
			elseif ($p->Sortrank == $minrank)
				$n = "<span class=\"releg\">$n</span>";
			//  Do this by hand so span overrides colour of link
			$n = "<a href=\"playgames.php?{$p->urlof()}\" class=\"name\">$n</a>";
			print <<<EOT
<tr>
<td>$n</td>
<td>{$p->display_rank()}</td>

EOT;
			foreach ($players as $op) {
				$reca = $p->record_against($op, $Season->Ind);
				print "<td>{$reca->display(false)}</td>\n";
			}

			print <<<EOT
<td align="right">{$p->histplayed($Season->Ind)}</td>
<td align="right">{$p->histwon($Season->Ind)}</td>
<td align="right">{$p->histdrawn($Season->Ind)}</td>
<td align="right">{$p->histlost($Season->Ind)}</td>
</tr>

EOT;
		}
		print "</table>\n";
	}
	if ($d != $ml)
		print "<br><br><br>\n";
}
?>
</div>
<p>Key to above: Matches <b>P</b>layed, <b>W</b>on, <b>D</b>rawn, <b>L</b>ost.
<span class="prom">Promotion Zone</span> and <span class="releg">Relegation Zone</span>.
</p>
<h2>Other Seasons</h2>
<p>Please <a href="javascript:history.back()">click here</a> to go back.</p>
</div>
</div>
</body>
</html>
