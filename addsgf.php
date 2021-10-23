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
include 'php/match.php';
include 'php/game.php';

$Connection = opendatabase(true);
lg_html_header("Add SGF file");
print <<<EOT
<script language="javascript">
function gkgs() {
	var gns = document.gnum.gn;
	if (gns.selectedIndex < 0) {
		alert("No game selected");
		return;
	}
	var game = gns.options[gns.selectedIndex].value;
	document.location = "aloadkgs.php?gn=" + game;
}
</script>

EOT;
lg_html_nav();
print <<<EOT
<h1>Add SGF record</h1>
<p>Use this page to add SGF records to results which do not have them.</p>

EOT;
$glist = list_nosgf_games();
if (count($glist) == 0)  {
print <<<EOT
<p>(None at present).</p>

EOT;
}
else {
	print <<<EOT
<p>
Select a game from below and <a href="javascript:gkgs();" title="Try to find game on KGS archives">click here</a>
to try to load it from KGS archives. Otherwise load from an SGF file on your computer.
</p>
<form name="gnum" action="addsgf2.php" method="post" enctype="multipart/form-data">
<p>
Select game:
<select name="gn">

EOT;
	foreach ($glist as $g) {
		print <<<EOT
<option value={$g->query_ind()}>
{$g->Date->display()} {$g->Wplayer->display_name()}-v-{$g->Bplayer->display_name()}
</option>
EOT;
	}
	print <<<EOT
</select>
</p>
<p>Upload SGF file from your computer:<input type=file name=sgffile></p>
<p>When done, press this:
<input type="submit" value="Add SGF">
</p>
</form>

EOT;
lg_html_footer();
}
?>
