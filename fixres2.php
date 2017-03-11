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
$Title = "Edit Game Result";
include 'php/head.php';
?>
<body>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Edit Game Result</h1>
<p>
Editing result for Game between
<?php
print <<<EOT
<b>{$g->Wplayer->display_name(false)}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name(false)}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black.
</p>
EOT;
if ($g->Result != 'N') {
	print <<<EOT
<p>If the result is completely wrong and should be deleted as if the game had not
been played <a href="delres.php?{$g->urlof()}">click here</a>.</p>
EOT;
}
print <<<EOT
<form action="fixres3.php" name="resform" method="post" enctype="multipart/form-data">
{$g->save_hidden()}
<p>
EOT;
$g->Date->dateopt("Game was played on");
$ws=$bs=$js=$selr=$selt=$selh="";
$seln = " selected";
$selnum = 999;

switch ($g->Result) {
	case 'W':
		$ws = " selected";
		if (preg_match('/W\+(.*)(\.5)?/', $g->Resultdet, $rm)) {
			switch ($rm[1])  {
			default:
				if (preg_match('/^(\d+)\.5$/', $rm[1], $dm))
					$selnum = $dm[1];
				break;
			case 'H':
				$selh = ' selected';
				break;
			case 'R':
				$selr = ' selected';
				break;
			case 'T':
				$selt = ' selected';
				break;
			}
		}
		break;
	case 'B':
		$bs = " selected";
		if (preg_match('/B\+(.*)(\.5)?/', $g->Resultdet, $rm)) {
			switch ($rm[1])  {
			default:
				if (preg_match('/^(\d+)\.5$/', $rm[1], $dm))
					$selnum = $dm[1];
				break;
			case 'H':
				$selh = ' selected';
				break;
			case 'R':
				$selr = ' selected';
				break;
			case 'T':
				$selt = ' selected';
				break;
			}
		}
		break;
	case 'J':
		$js = " selected";
		break;
}
print <<<EOT
</p>
<p>
Result was
<select name="result" size="0">
<option value="W"$ws>White Win</option>
<option value="B"$bs>Black Win</option>
<option value="J"$js>Jigo</option>
</select>
by
<select name="resulttype" size="0">
<option value="N"$seln>Not known</option>
<option value="R"$selr>Resign</option>
<option value="T"$selt>Time</option>

EOT;
for ($v = 0; $v < 50; $v++)
	if ($v == $selnum)
		print "<option value=$v selected>$v.5</option>\n";
	else
		print "<option value=$v>$v.5</option>\n";
print <<<EOT
<option value="H"$selh>Over 50</option>
EOT;
?>
</select></p>
<p>SGF file of the game to upload or replace <input type=file name=sgffile></p>
<p>When done, press this:<input type="submit" value="Edit result"></p>
</form>
<p>If you never meant to get to this page
<a href="javascript:history.back()">click here</a> to go back.</p>
</div>
</div>
</body>
</html>
