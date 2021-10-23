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
include 'php/teammemb.php';

$Connection = opendatabase(true);

try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}

$Playerlist = list_players("club,rank desc,last,first");
$Elist = $team->list_members();
$Title = "Update Team Members for {$team->display_name()}";
lg_html_header($Title);
print <<<EOT
<script language="javascript">
var playerlist = new Array();
var currteam = new Array();

EOT;
foreach ($Playerlist as $player) {
	$player->fetchdets();
	$player->fetchclub();
	print <<<EOT
playerlist.push({first:"{$player->display_first()}", last:"{$player->display_last()}",
rank:"{$player->display_rank()}", club:"{$player->Club->Name}",
ncurr:{$player->count_teams()}});

EOT;
}
foreach ($Elist as $ep) {
	$ep->fetchdets();
	$ep->fetchclub();
	print <<<EOT
currteam.push({first:"{$ep->display_first()}", last:"{$ep->display_last()}",
rank:"{$ep->display_rank()}", club:"{$ep->Club->Name}"});

EOT;
}
print <<<EOT
var teamurl = "{$team->urlof()}";
var changes = 0;
var createwind = null;

function killwind() {
	if (createwind) {
		createwind.close();
		createwind = null;
	}
}

// Replace message in final paragraph to warn users that there are
// changes to save

function set_changes() {
	changes++;
	var par = document.getElementById('changepara');
	var newtext = document.createTextNode("There are changes to save");
	var btext = document.createElement("b");
	btext.appendChild(newtext);
	var kids = par.childNodes;
	par.replaceChild(btext, kids[0]);
}

function addmembs() {
	killwind();
	createwind = window.open("membpick.html", "Select Team Member", "scrollbars=yes,width=450,height=400");
}

function insertmemb(pl) {
	var ttbod = document.getElementById('membbody');
	var rownum = ttbod.rows.length;
	var rownode = ttbod.insertRow(rownum);
	var cellnode = rownode.insertCell(0);
	var text = document.createTextNode(pl.first + " " + pl.last);
	cellnode.appendChild(text);

	cellnode = rownode.insertCell(1);
	text = document.createTextNode(pl.rank);
	cellnode.appendChild(text);

	cellnode = rownode.insertCell(2);
	text = document.createTextNode(pl.club);
	cellnode.appendChild(text);

	cellnode = rownode.insertCell(3);
	text = document.createTextNode("Del");
	var anch = document.createElement('a');
	anch.href = "javascript:delmembrow(" + rownum + ")";
	anch.appendChild(text);
	cellnode.appendChild(anch);
}

function delmembrow(rownum) {
	var ttbod = document.getElementById('membbody');
	while (ttbod.rows.length != 0)
		ttbod.deleteRow(0);
	currteam.splice(rownum,1);
	loadtab();
	set_changes();
	killwind();
}

function loadtab() {
	for (var i = 0;  i < currteam.length;  i++)
		insertmemb(currteam[i]);
}

function savemembs() {
	if (changes == 0)
		return;
	var args = new Array();
	args.push(teamurl);
	for (var i = 0; i < currteam.length; i++)  {
		var pl = currteam[i];
		// Remember that we may need to htmlspecialchars_decode the results from these.
		args.push("tm" + i + "f=" + encodeURI(pl.first));
		args.push("tm" + i + "l=" + encodeURI(pl.last));
	}
	var arglist = args.join("&");
	var newloc = "updmembs.php?" + arglist;
	document.location = newloc;
}

</script>

EOT;
lg_html_nav();
print <<<EOT
<h1>$Title</h1>
<p>This is the current team. To add a player to the team
<a href="javascript:addmembs()">click here</a>.
To remove a player click del against the player.</p>
<table class="updtmemb">
<thead><tr><th>Name</th><th>Rank</th><th>Club</th><th>Del</th></tr></thead>
<tbody id="membbody"></tbody>
</table>
<p>When done <a href="javascript:savemembs()">click here</a> or to forget the changes
click somewhere else.</p>
<p id="changepara">There are no changes at present.</p>

EOT;
lg_html_footer();
?>
