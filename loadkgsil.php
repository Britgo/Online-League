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
include 'php/matchdate.php';
include 'php/team.php';
include 'php/game.php';
include 'php/kgsfetchsgf.php';
include 'php/news.php';

$player = new Player();
try {
	$player->fromid($userid);
	if (strlen($player->KGS) == 0)
		throw new PlayerException("You have no KGS name");
}
catch (PlayerException $e) {
	$msg = htmlspecialchars($e->getMessage());
	print <<<EOT
<html>
<head>
<title>Unknown player</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<h1>Unknown player</h1>
<p>Sorry, but player name $userid is not known.</p>
<p>Problem was $msg.</p>
<p>Please start again from the top by <a href="index.php">clicking here</a>.</p>
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
<a href="ownupd.php">here</a>, otherwise
start again from the top by <a href="index.php">clicking here</a>.</p>
</p>
</body>
</html>

EOT;
	exit(0);
}

$opp = new Player();
try {
	$opp->fromsel($_GET["opp"]);
	$opp->fetchdets();
	if (strlen($opp->KGS) == 0)
		throw new PlayerException("Opponent has no KGS name");
}
catch (PlayerException $e) {
	$msg = htmlspecialchars($e->getMessage());
	print <<<EOT
<html>
<head>
<title>No opponent</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>
<body class="il">
<h1>No opponent</h1>
<p>Sorry but I failed to work out who your opponent was.</p>
<p>Problem was $msg.</p>
<p>Please start again from the top by <a href="index.php">clicking here</a>.</p>
</body>
</html>

EOT;
	exit(0);
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Add Game Result";
include 'php/head.php';
?>
<body class="il">
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Add Game Result</h1>
<p>
Finished adding result with game record for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) as Black was {$g->display_result()}.
</p>

EOT;
if (strlen($msg) != 0)  {
	print <<<EOT
<p>However the game SGF could not be added because of
$msg.</p>

EOT;
}

$n = new News($userid, "Individual League game completed between {$player->display_name(false)} and {$opp->display_name(false)} in Division {$player->ILdiv}"); 
$n->addnews();	
?>
<p>Click <a href="ileague.php">here</a> to see the league status now.</p>
</div>
</div>
</body>
</html>
