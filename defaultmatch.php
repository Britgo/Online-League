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
include 'php/match.php';
include 'php/matchdate.php';

$Connection = opendatabase(true);

$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
}
catch (MatchException $e) {
	wrongentry($e->getMessage());
}

$which = $_GET["w"];
switch  ($which)  {
default:
	wrongentry("Unknown default type");
case  'H':
case  'A':
	$mtch->set_defaulted($which);
	break;
}
lg_html_header("Match Defaulted");
lg_html_nav();
$wteam = $mtch->Result == 'H'? $mtch->Hteam: $mtch->Ateam;
print <<<EOT
<h1>Match Defaulted</h1>
<p>The match between {$mtch->Hteam->display_name()} and {$mtch->Ateam->display_name()}
on {$mtch->Date->display()} has been defaulted in favour of {$wteam->display_name()}.</p>
<p>Click <a href="matchupd.php" title="Edit some other match">here</a> to edit some other match.</p>

EOT;
lg_html_footer();
?>
