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

$Connection = opendatabase(true);

$player = new Player();
try {
	$player->fromid($Connection->userid);
}
catch (PlayerException $e) {
	il_unknown_player_id($Connection->userid);
}
if ($player->ILdiv == 0)
	il_not_in_league($player);

lg_html_header("Add result in individual league", "il");
print <<<EOT
<script language="javascript">
function checkform() {
	var fm = document.ilresf;
	if (fm.resulttype.selectedIndex < 0)  {
		alert("No result selected");
		return  false;
	}
	if (fm.opp.selectedIndex < 0)  {
		alert("No opponent selected");
		return  false;
	}
	return  true;
}

function loadkgs() {
	var fm = document.ilresf;
	var dayel = fm.day;
	var monel = fm.month;
	var yrel = fm.year;
	var day = dayel.options[dayel.selectedIndex].value;
	var month = monel.options[monel.selectedIndex].value;
	var year = yrel.options[yrel.selectedIndex].value;
	var resty = fm.resulttype;
	var resel = fm.result;
	var res;
	if (resel[0].checked)
		res = resel[0].value;
	else if (resel[1].checked)
		res = resel[1].value;
	else
		res = resel[2].value;
	var colel = fm.colour;
	var colour;
	if  (colel[0].checked)
		colour = colel[0].value;
	else
		colour = colel[1].value;
	var restype = resty.options[resty.selectedIndex].value;
	if (restype == 'N') {
		alert("Result type not set");
		return  false;
	}
	var plf = fm.plf.value;
	var pll = fm.pll.value;
	var oppel = fm.opp;
	if  (oppel.selectedIndex < 0)  {
		alert("No opponent selected");
		return  false;
	}
	var opp = oppel.options[oppel.selectedIndex].value;
	document.location = "loadkgsil.php?pl=" + plf + ":" + pll + "&opp=" + opp +
							  "&col=" + colour +
							  "&md=" + year + "-" + month + "-" + day + "&r=" +
							  res + "&rt=" + restype;
	return  false;
}
function checknokgs() {
	var fm = document.ilresf;
	if (fm.sgffile.value.length != 0)
		return true;
	return confirm("Are you sure that this game was not played on KGS so you can download it?");
}
</script>

EOT;
lg_html_nav();
print<<<EOT
<h1>Add result for Individual League</h1>
<p>Welcome
<?php
print <<<EOT
<b>{$player->display_name()}</b>
online name
<b>{$player->display_online()}</b>
from Division
{$player->ILdiv}.
</p>
<p>To enter the individual league match result, please complete the form below:</p>
<form action="addilresultb2.php" method="post" enctype="multipart/form-data" name="ilresf" onsubmit="javascript: return checkform();">
<?php print $player->save_hidden("pl"); ?>
<table cellpadding="2" cellspacing="5" border="0">
<tr>
	<td>Match was played on</td>
	<td>

EOT;
$dat = new Matchdate();
$dat->dateopt("");
print <<<EOT
	</td>
</tr>
<tr><td></td><td>NB Date <b>started</b> if it crosses midnight</td></tr>
<tr>
	<td>Opponent was</td>
	<td><select name="opp">

EOT;

$pl = list_players_ildiv($player->ILdiv);
foreach ($pl as $p) {
	if ($p->is_same($player))
		continue;
	$p->fetchdets();
	print <<<EOT
<option value="{$p->selof()}">
{$p->display_name(false)}
({$p->display_rank()}) {$p->display_online()}
</option>

EOT;
}
print <<<EOT
	</select></td>
</tr>
<tr>
	<td>I was playing</td>
	<td><input type="radio" name="colour" value="B" checked>Black
	<input type="radio" name="colour" value="W">White</td>
</tr>
<tr><td>Outcome</td><td><input type="radio" name="result" value="W" checked>I won
<input type="radio" name="result" value="J">Jigo
<input type="radio" name="result" value="L">I lost</td></tr>
<tr><td>Score was</td>
<td><select name="resulttype" size="0">
<option value="N" selected>Not known</option>
<option value="R">Resign</option>
<option value="T">Time</option>

EOT;
for ($v = 0; $v < 50; $v++)
	print "<option value=$v>$v.5</option>\n";
print <<<EOT
<option value="H">Over 50</option>
</select>
</td>
</tr>
<tr>
	<td>SGF file of game here</td>
	<td><input type=file name=sgffile></td>
</tr>
<tr>
	<td colspan=2>
	Click <input type="submit" name="sub" value="Here" onclick="javascript:return checknokgs();">
	if uploading file from my computer or no SGF</td>
</tr>
<tr>
	<td colspan=2>
	Or <input type="submit" value="click here to load result from KGS" onclick="javascript:return loadkgs();">
	getting the date, result and score right.
	</td>
</tr>
</table>
</form>
EOT;

lg_html_footer();
?>
