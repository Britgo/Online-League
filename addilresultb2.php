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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/news.php';

$player = new Player();
$opp = new Player();

try  {
	$player->frompost("pl");
	$opp->fromsel($_POST["opp"]);
	$player->fetchdets();
	$opp->fetchdets();
}
catch (PlayerException $e)  {
	print <<<EOT
<html>
<head>
<title>Trouble with player details</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<h1>Trouble fetching player details</h1>
<p>Sorry something has gone wrong with your player detail posting.</p>
<p>Please start again from the top by <a href="index.php" title="Go back to home page">clicking here</a>.</p>
</body>
</html>

EOT;
	exit(0);
}
if ($player->ILdiv == 0)  {
	print <<<EOT
<html>
<head>
<title>Not in league</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="il">
<h1>Not in individual league</h1>
<p>Sorry, but you, {$player->display_name(false)} are not currently in the individual
league.</p>
<p>If you want to join it, please update your account
<a href="ownupd.php" title="Edit your own details, including whether you want to join the Individual League">here</a>,
otherwise please go back to the top by  <a href="index.php" title="Go back to the home page">clicking here</a>.</p>
<p>Actually I do not really know how you got here.</p>
</body>
</html>

EOT;
	exit(0);
}
if ($player->ILdiv != $opp->ILdiv) {
	print <<<EOT
<html>
<head>
<title>Not in same division</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="il">
<h1>Not in individual league</h1>
<p>Sorry, but {$player->display_name(false)} in {$player->ILdiv} 
is not currently in the same individual league division as
{$opp->display_name(false)} who is in division {$opp->ILdiv}.</p>
<p>Please
go back to the top by  <a href="index.php" title="Go back to the home page">clicking here</a>.</p>
<p>Actually I do not really know how you got here.</p>
</body>
</html>

EOT;
	exit(0);
}

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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Game Result Added";
include 'php/head.php';
?>
<body class="il">
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Add Game Result</h1>
<p>
Finished adding result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) as Black was {$g->display_result()}.
</p>

EOT;
$n = new News($userid, "Individual League game completed between {$player->display_name(false)} and {$opp->display_name(false)} in Division {$player->ILdiv}"); 
$n->addnews();	
?>
<p>Click <a href="ileague.php" title="View the individual league standings">here</a>
to see the league status now.</p>
</div>
</div>
</body>
</html>
