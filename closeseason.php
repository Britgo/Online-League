<?php
//   Copyright 2014-2021 John Collins

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
include 'php/game.php';
include 'php/season.php';
include 'php/news.php';

$Connection = opendatabase(true);

$unplayed_matches = $setdrawn_games = 0;

try  {

	//  Select and delete all matches which haven't been played at all

	$ret = $Connection->query("SELECT ind FROM lgmatch WHERE result='N'");
	if ($ret)  {
		$inds = array();
		while ($row = $ret->fetch_array())  {
			array_push($inds, $row[0]);
		}
		foreach ($inds as $ind)  {
			$mtch = new Match($ind);
			$mtch->fetchdets();
			$mtch->fetchteams();
			$mtch->delmatch();
		}
		$unplayed_matches = count($inds);
	}

	// Mark unfinished games as drawn

	$ret = $Connection->query("SELECT ind FROM lgmatch WHERE result='P'");
	if  ($ret)  {
		$inds = array();
		while ($row = $ret->fetch_array())  {
			array_push($inds, $row[0]);
		}
		$today = new Matchdate();
		foreach ($inds as $ind)  {
			$mtch = new Match($ind);
			$mtch->fetchdets();
			$mtch->fetchteams();
			$mtch->fetchgames();
			foreach ($mtch->Games as $g) {
				if ($g->Result == 'N')  {
					$g->set_result('J', 'Jigo');
					$g->reset_date($today);
					$setdrawn_games++;
				}
			}
		}
	}

	//  Create news item if we actually did anything

	$matchmsg = "$unplayed_matches match";
	if ($unplayed_matches != 1)
		$matchmsg .= 'es'	;
	$gamemsg = "$setdrawn_games game";
	if ($setdrawn_games != 1)
		$gamemsg .= 's'	;
	if ($unplayed_matches + $setdrawn_games > 0)  {
		$n = new News($Connection->userid, "Closed season cancelling $matchmsg and drawing $gamemsg", true);
		$n->addnews();
	}
}
catch (MatchException $e) {
	database_error($e->getMessage());
}

lg_html_header("Closed season");
lg_html_nav();
print <<<EOT
<h1>Close Season Completed</h1>
<p>Successfully closed the season cancelling $matchmsg and setting drawn $gamemsg.</p>
<p><a href="admin.php" title="Go back to admin page">Click here</a> to go back to the admin page.</p>

EOT;
lg_html_footer();
?>
