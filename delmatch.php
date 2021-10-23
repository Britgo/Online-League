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

$Connection = opendatabase(true);

$mtch = new Match();
try  {
	$mtch->fromget();
	$mtch->fetchdets();
	$mtch->fetchteams();
	$mtch->delmatch();
}
catch (MatchException $e) {
	wrongentry($e->getMessage());
}

lg_html_header("Delete match completed");
lg_html_nav();
print <<<EOT
<h1>Delete Match Completed</h1>
<p>Successfully completed deletion of Match between
{$mtch->Hteam->display_name()} and
{$mtch->Ateam->display_name()} set for
{$mtch->Date->display()}.</p>
<p><a href="matchtmupd.php?div={$mtch->Division}" title="Go back to editing matches">Click here</a> to go back
to editing matches for division {$mtch->Division}.</p>

EOT;
lg_html_footer();
?>
