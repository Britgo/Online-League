<?php
//   Copyright 2014 John Collins

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

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
include 'php/matchdate.php';
include 'php/game.php';
include 'php/season.php';
include 'php/news.php';

$unplayed_matches = $setdrawn_games = 0;

try  {
	
	//  Select and delete all matches which haven't been played at all
	
	$ret = mysql_query("select ind from lgmatch where result='N'");
	if ($ret)  {
		$inds = array();
		while ($row = mysql_fetch_array($ret))  {
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
	
	$ret = mysql_query("select ind from lgmatch where result='P'");
	if  ($ret)  {
		$inds = array();
		while ($row = mysql_fetch_array($ret))  {
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
		$n = new News($userid, "Closed season cancelling $matchmsg and drawing $gamemsg", true); 
		$n->addnews();
	}
}
catch (MatchException $e) {
	$mess = $e->getMessage();
   include 'php/dataerror.php';
   exit(0);        
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Closed season";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Close Season Completed</h1>
<?php
print <<<EOT
<p>
Successfully closed the season cancelling $matchmsg and setting drawn $gamemsg. 
</p>

EOT;
?>
<p>
<a href="admin.php" title="Go back to admin page">Click here</a> to go back to the admin page.
</p>
</div>
</div>
</body>
</html>
