<?php
//   Copyright 2009-2021 John Collins

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
include 'php/game.php';

$div = $_POST['div'];
if (strlen($div) == 0)
	wrongentry("No division");

$hteam = $_POST['hteam'];
$ateam = $_POST['ateam'];
$slack = $_POST['slackd'];
if (strlen($hteam) == 0 || strlen($ateam) == 0)
	wrongentry("Missing teams?");

$Connection = opendatabase(true);

$dat = new Matchdate();
$dat->frompost();
$mtch = new Match(0, $div);
$mtch->set_hometeam($hteam);
$mtch->set_awayteam($ateam);
$mtch->Date = $dat;
$mtch->Slackdays = $slack;
try {
	// Fetch the team details not because we need them, but
	// so as to check for garbled team names.
	$mtch->fetchteams();
	$mtch->create();
	// That sets the match ind in $mtch which the updmatch call uses later.
}
catch (MatchException $e)  {
	database_error($e->getMessage());
}

lg_html_header("Add Match division $div OK");
lg_html_nav();
print <<<EOT
<h1>Create Match division $div successful</h1>
<p>
Successfully completed creation of Match between
{$mtch->Hteam->display_name()} and
{$mtch->Ateam->display_name()} set for
{$mtch->Date->display()}.
</p>
<p><a href="updmatch.php?{$mtch->urlof()}" title="Add team members to match">Click here</a>
to add team members.</p>

EOT;
lg_html_footer();
?>
