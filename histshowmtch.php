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
include 'php/histteammemb.php';
include 'php/histmatch.php';
include 'php/matchdate.php';
include 'php/game.php';

$Connection = opendatabase();

$mtch = new HistMatch(null);
try  {
	$mtch->fromget();
	$mtch->getseason();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (MatchException $e) {
	wrongentry($e->getMessage());
}
lg_html_header("Historic Match Details");
lg_html_nav();
print <<<EOT
<h1>Historic Match Details</h1>
<p>Match was between
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()})
for
{$mtch->Date->display_month()}.</p>

EOT;

if ($mtch->Defaulted)  {
	$wdef = $mtch->Result == 'A'? $mtch->Ateam: $mtch->Hteam;
	print <<<EOT
<p>This match was defaulted in favour of {$wdef->display_name()}.</p>

EOT;
}
else {
	print <<<EOT
<p>The final score was {$mtch->summ_score()}</p>
<p>Player and board assignments were as follows:</p>
<table class="showmatch">
<tr><th colspan="3" align="center">White</th><th colspan="2" align="center">Black</th><th>Result</th></tr>
<tr><th>Date</th><th>Player</th><th>Team</th><th>Player</th><th>Team</th></tr>
EOT;
	foreach ($mtch->Games as $g) {
		$bpre = $bpost = $wpre = $wpost = "";
		switch ($g->Result)  {
		case 'W':
			$wpre = "<b>";
			$wpost = "</b>";
			break;
		case 'B':
			$bpre = "<b>";
			$bpost = "</b>";
			break;
		}
		print <<<EOT
<tr>
<td>{$g->date_played()}</td>
<td>$wpre{$g->Wplayer->display_name()}$wpost</td>
<td>{$g->Wteam->display_name()}</td>
<td>$bpre{$g->Bplayer->display_name()}$bpost</td>
<td>{$g->Bteam->display_name()}</td>
<td>{$g->display_result()}</td>
</tr>
EOT;
	}
	print "</table>\n";
}
print <<<EOT
<p>Click <a href="javascript:history.back()">here</a> to view some other match.</p>

EOT;
lg_html_footer();
?>
