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
include 'php/matchdate.php';
include 'php/team.php';
include 'php/game.php';
include 'php/kgsfetchsgf.php';
include 'php/news.php';

$Connection = opendatabase(true);
$player = new Player();
try {
	$player->fromid($userid);
	if (strlen($player->KGS) == 0)
		throw new PlayerException("You have no KGS name");
}
catch (PlayerException $e) {
	il_player_details($e->getMessage());
}

if ($player->ILdiv == 0)
	il_not_in_league($player);

$opp = new Player();
try {
	$opp->fromsel($_GET["opp"]);
	$opp->fetchdets();
	if (strlen($opp->KGS) == 0)
		throw new PlayerException("Opponent has no KGS name");
}
catch (PlayerException $e) {
	il_unknown_player_id($e->getMessage());
}

$mycolour = $_GET["col"];
$dat = new Matchdate();
$dat->fromget();
$myres = $_GET["r"];
$myrt = $_GET["rt"];

$g = new Game(0, 0, $player->ILdiv, 'I');
$g->Date = $dat;

$result = $myres;

if ($mycolour == 'B')  {
	$g->Wplayer = $opp;
	$g->Bplayer = $player;
	$wkgs = $opp->KGS;
	$bkgs = $player->KGS;
	if ($myres == 'W')
		$result = 'B';
	elseif ($myres == 'L')
		$result = 'W';
}
else  {
	$g->Wplayer = $player;
	$g->Bplayer = $opp;
	$bkgs = $opp->KGS;
	$wkgs = $player->KGS;
	if ($myres == 'W')
		$result = 'W';
	elseif ($myres == 'L')
		$result = 'B';
}

$g->setup_restype($result, $myrt);
try {
	$g->Sgf = kgsfetchsgf($g);
	$msg = "";
}
catch  (GameException $e)  {
	$msg = htmlspecialchars($e->getMessage());
}
$g->create_game();
lg_html_header("Add Game Result", "il");
lg_html_nav();
print <<<EOT
<h1>Add Game Result</h1>
<p>
Finished adding result with game record for Game between
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) as Black was {$g->display_result()}.</p>

EOT;
if (strlen($msg) != 0)  {
	print <<<EOT
<p>However the game SGF could not be added because of
$msg.</p>

EOT;
}
print <<<EOT
<p>Click <a href="ileague.php">here</a> to see the league status now.</p>

EOT;
$n = new News($Connection->userid, "Individual League game completed between {$player->display_name(false)} and {$opp->display_name(false)} in Division {$player->ILdiv}");
$n->addnews();
lg_html_footer();
?>
