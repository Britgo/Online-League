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

ini_set("session.gc_maxlifetime", "18000");
session_start();
$userid = $_SESSION['user_id'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php

include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/kgsfetchsgf.php';
include 'php/news.php';

$g = new Game();
try  {
	$g->fromget();
	$g->fetchdets();
	if ($g->Result != 'N')
		throw new GameException("Game has already been entered");
}
catch (GameException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}

$date_played = new Matchdate();
$date_played->fromget();

//  Change date and set result

if ($date_played->unequal($g->Date))
	$g->reset_date($date_played);

$mtch = $g->set_result($_GET["r"], $_GET["rt"]);

//  Try to load KGS file

try  {
	$g->set_sgf(kgsfetchsgf($g));
	$msg = "";
}
catch  (GameException $e)  {
	$msg = htmlspecialchars($e->getMessage());
}
?>
<html>
<?php
$Title = "Game Result Added";
include 'php/head.php';
?>
<body>
<h1>Add Game Result complete</h1>
<p>
Finished adding result with SGF for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black on
{$g->date_played()} was {$g->display_result()}.
</p>

EOT;
if (strlen($msg) != 0)  {
	print <<<EOT
<p>However the game SGF could not be added because of
$msg.</p>

EOT;
}
if ($mtch->Result == 'P')  {
	print <<<EOT
<p>The match has not been completed yet.
</p>

EOT;
	$n = new News($userid, "Game completed in {$mtch->Hteam->Name} -v- {$mtch->Ateam->Name} in Division {$mtch->Division}", false, $mtch->showmatch()); 
	$n->addnews();	
}
else  {
	$result = 'The winner of the match was ';
	if ($mtch->Result == 'H')
		$result .= $mtch->Hteam->Name;
	elseif ($mtch->Result == 'A')
		$result .= $mtch->Ateam->Name;
	else
		$result = 'The match was drawn';
	print <<<EOT
<p>The match has now been completed.</p>
<p>$result.</p>
EOT;
	$n = new News($userid, "Match now completed between {$mtch->Hteam->Name} and {$mtch->Ateam->Name} in Division {$mtch->Division}. $result.", true, $mtch->showmatch());
	$n->addnews();
}
?>
</body>
</html>
