<?php
//   Copyright 2009-2015 John Collins

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
$g = new Game();
try  {
	$g->fromget();
	$g->fetchdets();
}
catch (GameException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Add Game Result";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function checkreverse(gm) {
	if (!confirm("Are you sure the colours were reversed?"))
		return false;
	if (!confirm("I promise not to make a habit of this"))
		return false;
	document.location = "http://league.britgo.org/swapcolours.php?" + gm;
	return  true;
}
function loadkgs() {
	var fm = document.resform;
	var game = fm.gn.value;
	var dayel = fm.day;
	var monel = fm.month;
	var yrel = fm.year;
	var day = dayel.options[dayel.selectedIndex].value;
	var month = monel.options[monel.selectedIndex].value;
	var year = yrel.options[yrel.selectedIndex].value;
	var resel = fm.result;
	var resty = fm.resulttype;
	var reversedcol = fm.reversed.checked? "&rev=y": "";
	if (resel.selectedIndex < 0 || resty.selectedIndex < 0) {
		alert("No result selected");
		return false;
	}
	var res = resel.options[resel.selectedIndex].value;
	var restype = resty.options[resty.selectedIndex].value;
	if (restype == 'N') {
		alert("Result type not set");
		return false;
	}
	document.location = "loadkgs.php?gn=" + game + reversedcol +
							  "&md=" + year + "-" + month + "-" + day + "&r=" +
							  res + "&rt=" + restype;
	return false;
}
<?php
print <<<EOT
var white="{$g->Wplayer->KGS}";
var black="{$g->Bplayer->KGS}";

EOT;
?>
function checknokgs() {
	var fm = document.resform;
   if (fm.sgffile.value.length != 0)
		return true;
	if (white.length == 0 || black.length == 0)
		return true;
	return confirm("Are you sure that this match was not played on KGS between " + white + " and " + black);
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Add Game Result</h1>
<p>
Adding result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name(false)}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name(false)}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black.
</p>
<form name="resform" action="addresult2.php" method="post" enctype="multipart/form-data">
{$g->save_hidden()}
<p>
EOT;
$today = new Matchdate();
$today->dateopt("Game was played on");
print <<<EOT
</p>
<p>Please note that if the game is adjourned or crosses midnight, KGS normally stores the date it was <i>started</i> so please use that.</p>
<p><b>If you got the colours the wrong way round, please click <a href="javascript:checkreverse('{$g->urlof()}')">here</a>.</b></p>
<p>
Result was
<select name="result" size="0">
<option value="W">White Win</option>
<option value="B">Black Win</option>
<option value="J">Jigo</option>
</select>
by
<select name="resulttype" size="0">
<option value="N">Not known</option>
<option value="R">Resign</option>
<option value="T">Time</option>
EOT;
for ($v = 0; $v < 50; $v++)
	print "<option value=$v>$v.5</option>\n";
?>
<option value="H">Over 50</option>
</select>
</p>
<?php
if (strlen($g->Wplayer->KGS) != 0 && strlen($g->Bplayer->KGS) != 0) {
	print <<<EOT
<h2>Loading game file from KGS</h2>
<p>If the game was played on KGS using the online names
{$g->Wplayer->display_online()} and {$g->Bplayer->display_online()},
<ol>
<li>Get the date played right (note comment above about date started)</li>
<li>Get the result correct above</li>
<li>Check the colours are correct</li>
</ol>
Then click here to download the SGF from the KGS records.
<input type="submit" value="Load SGF from KGS" onclick="javascript:return loadkgs();">
</p>
<h2>No game record or have SGF file of game</h2>

EOT;
}
?>
<p>
If you have the game available on your computer as an SGF file to
upload browse for it here <input type=file name=sgffile>
</p>
<p>If you don't have the file available as an SGF anywhere just leave the above blank.</p>
<p>In either case click here <input type="submit" value="Add result" onclick="javascript:return checknokgs();">
</p>
</form>
<p>If you never meant to get to this page
<a href="javascript:history.back()">click here</a> to go back.</p>
</div>
</div>
</body>
</html>
