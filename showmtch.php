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
$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->fetchgames();
}
catch (MatchException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
?>
<html>
<?php
$Title = "Match Edit Result";
include 'php/head.php';
?>
<body>
<h1>Match Details</h1>
<p>
Match is between
<?php
print <<<EOT
{$mtch->Hteam->display_name()} ({$mtch->Hteam->display_description()})
and
{$mtch->Ateam->display_name()} ({$mtch->Ateam->display_description()})
on
{$mtch->Date->display()} with {$mtch->Slackdays} days to play the games.
</p>
<p>Team captains are {$mtch->Hteam->Captain->display_name()} for {$mtch->Hteam->display_name()}
and {$mtch->Ateam->Captain->display_name()} for {$mtch->Ateam->display_name()}.
</p>
<p>Player and board assignments are as follows:</p>
<table>
<tr><th colspan="3" align="center">White</th><th colspan="3" align="center">Black</th><th>Result</th></tr>
<tr><th>Player</th><th>Rank</th><th>Team</th><th>Player</th><th>Rank</th><th>Team</th></tr>
EOT;
foreach ($mtch->Games as $g) {
	switch ($g->Result) {
	default:
		$res = '&nbsp;';
		break;
	case 'W':
		$res = "White Win";
		break;
	case 'J':
		$res = "Jigo";
		break;
	case 'B':
		$res = "Black Win";
		break;
	}
	print <<<EOT
<tr>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wplayer->display_rank()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bplayer->display_rank()}</td>
<td>{$g->Bteam->display_name()}</td>
<td>$res</td>
</tr>
EOT;
}
?>
</table>
<p>Click <a href="matchesb.php">here</a> to view some other match.</p>
</body>
</html>
