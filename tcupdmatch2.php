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

// Complete "team captain" version of team allocation

include 'php/session.php';
if (!$logged_in) {
	include 'php/horses.php';
	exit(0);
}
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/sortrank.php';
include 'php/news.php';
include 'php/params.php';
include 'php/hcp_message.php';
include 'php/mailalloc.php';

$pars = new Params();
$pars->fetchvalues();

$mtch = new Match();
try  {
	$mtch->frompost();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
	$Myteam = new Team();
	$Myteam->frompost();
	if ($Myteam->is_same($mtch->Hteam)) {
		$Myteam = $mtch->Hteam;
		$Histeam = $mtch->Ateam;
	}
	else  {
		$Myteam = $mtch->Ateam;
		$Histeam = $mtch->Hteam;
	}
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}

// Is this a "handicappable" division

$hcapable = $mtch->Division >= $pars->Hdiv;
$hred = $pars->Hreduct;

for ($b = 0;  $b < 3;  $b++)  {
	$h = new Player();
	$h->fromsel($_POST["tm$b"]);
	$h->fetchdets();
	$player[$b] = $h;
}

// Sort players by rank unless turned off

if (!isset($_POST["forceass"]))
	sortrank($player, $pars->Rankfuzz);

// If we haven't got any games yet, select colours as WBW or BWB and create the game
// with our team filled in

$gnum = $mtch->ngames();

if ($gnum == 0)  {
	$col = rand(0,1);
	for ($b = 0; $b < 3; $b++) {
		$g = $mtch->newgame();
		if ($col > 0) {
			$g->Bteam = $Myteam;
			$g->Bplayer = $player[$b];
		}
		else  {
			$g->Wteam = $Myteam;
			$g->Wplayer = $player[$b];
		}
		$g->create_game();
		$col = 1 - $col;
	}
}
else {
	$col = 0;
	for ($b = 0; $b < 3; $b++) {
		if ($b < $gnum)  {
			// Existing game or half game
			$g = $mtch->Games[$b];
			if ($g->Wteam)  {
				// White team allocated
				// If our team force player and say last colour was white
				if  ($g->Wteam->is_same($Myteam))  {
					$g->Wplayer = $player[$b];
					$col = 0;
				}
				else  {
					// We must be black on this game, force player and team
					$g->Bteam = $Myteam;
					$g->Bplayer = $player[$b];
					$col = 1;
				}
			}
			else  {
				// White team not defined try black
				// If our team is black force player.
				if ($g->Bteam->is_same($Myteam))  {
					$g->Bplayer = $player[$b];
					$col = 1;
				}
				else  {
					// Black team must be other one
					// Force ours to be white
					$g->Wteam = $Myteam;
					$g->Wplayer = $player[$b];
					$col = 0;
				}
			} // End of looking at existing w/b defined
			
			// If handicapable and above number of stones to be deducted and
			// Weaker player is white, swap players
			
			if ($hcapable && $g->Bplayer->Rank->Rankvalue - $g->Wplayer->Rank->Rankvalue > $hred)  {
				$tmp = $g->Wteam;
				$g->Wteam = $g->Bteam;
				$g->Bteam = $tmp;
				$tmp = $g->Wplayer;
				$g->Wplayer = $g->Bplayer;
				$g->Bplayer = $tmp;
				$col = 1 - $col;				
			}
			
			$g->update_players();

		} // end of xisting game code
		else  {
				$g = $mtch->newgame();
				if ($col > 0) {
					$g->Bteam = $Myteam;
					$g->Bplayer = $player[$b];
				}
				else  {
					$g->Wteam = $Myteam;
					$g->Wplayer = $player[$b];
				}
				$g->create_game();
		}
		
		// Swap colours in case not defined	
		$col = 1 - $col;
	}	// End of loop over games
} // End of else for existing games
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Match Allocation Result";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Match Allocation Result</h1>
<?php

// If all that completes allocation, then tell the good news

if ($mtch->is_allocated())  {
	print <<<EOT
<p>
Match is set up between
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()})
in
{$mtch->Date->display_month()}.
</p>
<p>Team captains are {$mtch->Hteam->display_captain()} for {$mtch->Hteam->display_name()}
and {$mtch->Ateam->display_captain()} for {$mtch->Ateam->display_name()}.
</p>
<p>Player assignments are as follows:</p>
<table>
<tr><th colspan="4" align="center">White</th><th colspan="4" align="center">Black</th></tr>
<tr><th>Player</th><th>Rank</th><th>Online</th><th>Team</th><th>Player</th><th>Rank</th><th>Online</th><th>Team</th></tr>
EOT;
	foreach ($mtch->Games as $g) {
		print <<<EOT
<tr>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wplayer->display_rank()}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bplayer->display_rank()}</td>
<td>{$g->Bplayer->display_online()}</td>
<td>{$g->Bteam->display_name()}</td>
</tr>
EOT;
	}
	mail_allocated($mtch, $pars);
	$n = new News($userid, "Match now allocated between {$mtch->Hteam->Name} and {$mtch->Ateam->Name} in Division {$mtch->Division}", false, $mtch->showmatch());
	$n->addnews();
}
else  {
	print <<<EOT
<p>
Thank you for submitting the team for {$Myteam->display_name()}.
We haven't had the team for {$Histeam->display_name()} yet so you can
change it until we've received it.</p>
EOT;
}
?>
<p>Click <a href="matches.php">here</a> to edit some other match.</p>
</div>
</div>
</body>
</html>
