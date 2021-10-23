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
include 'php/matchdate.php';
include 'php/game.php';
include 'php/news.php';

$Connection = opendatabase(true);

$player = new Player();
$opp = new Player();

try  {
	$player->frompost("pl");
	$opp->fromsel($_POST["opp"]);
	$player->fetchdets();
	$opp->fetchdets();
}
catch (PlayerException $e)  {
	il_player_details($e->getMessage());
}
if ($player->ILdiv == 0)
	il_not_in_league();

if ($player->ILdiv != $opp->ILdiv)
	il_wrong_division($player, $opp);

$dat = new Matchdate();
$dat->frompost();

$mycolour = $_POST["colour"];
$myresult = $_POST["result"];
$rtype = $_POST["resulttype"];
$sgfdata = "";
$fn = $_FILES["sgffile"];
if ($fn['error'] == UPLOAD_ERR_OK  &&  preg_match('/.*\.sgf$/i', $fn['name']) && $fn['size'] > 0)
	$sgfdata = file_get_contents($fn['tmp_name']);

$g = new Game(0, 0, $player->ILdiv, 'I');
$result = $myresult;
if  ($mycolour == 'B')  {
	switch  ($myresult)  {
	case 'W':
		$result = 'B';
		break;
	case 'L':
		$result = 'W';
		break;
	}
	$g->Bplayer = $player;
	$g->Wplayer = $opp;
}
else  {
	switch  ($myresult)  {
	case 'W':
		$result = 'W';
		break;
	case 'L':
		$result = 'B';
		break;
	}
	$g->Wplayer = $player;
	$g->Bplayer = $opp;
}
$g->setup_restype($result, $rtype);
if (strlen($sgfdata) != 0)
	$g->Sgf = $sgfdata;
$g->Date = $dat;
$g->create_game();

// Now generate acknowledgement page

lg_html_header("Game result added", "il");
lg_html_nav();
print <<<EOT
<h1>Add Game Result</h1>
<p>Finished adding result for Game between
<b>{$g->Wplayer->display_name()}</b> ({$g->Wplayer->display_rank()}) as White and
<b>{$g->Bplayer->display_name()}</b> ({$g->Bplayer->display_rank()}) as Black was {$g->display_result()}.
</p>

EOT;
$n = new News($Connection->userid, "Individual League game completed between {$player->display_name(false)} and {$opp->display_name(false)} in Division {$player->ILdiv}");
$n->addnews();
print <<<EOT
<p>Click <a href="ileague.php" title="View the individual league standings">here</a>
to see the league status now.</p>

EOT;
lg_html_footer();
?>
