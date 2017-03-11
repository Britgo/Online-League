<?php
//   Copyright 2011-2015 John Collins

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
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/news.php';

$g = new Game();
try  {
	$g->frompost();
	$g->fetchdets();
}
catch (GameException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}

$date_played = new Matchdate();
$date_played->frompost();
$sgfdata = "";
$fn = $_FILES["sgffile"];
if ($fn['error'] == UPLOAD_ERR_OK  &&  preg_match('/.*\.sgf$/i', $fn['name']) && $fn['size'] > 0)
	$sgfdata = file_get_contents($fn['tmp_name']);
if ($date_played->unequal($g->Date))
	$g->reset_date($date_played);
$mtch = $g->set_result($_POST["result"], $_POST["resulttype"]);
if (strlen($sgfdata) != 0)
	$g->set_sgf($sgfdata);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Game Result Added";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Add Game Result</h1>
<p>
Finished adding result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name(false)}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name(false)}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black was {$g->display_result()}.
</p>
EOT;
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
</div>
</div>
</body>
</html>
