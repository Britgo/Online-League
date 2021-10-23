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
include 'php/matchdate.php';
include 'php/season.php';
include 'php/news.php';

$Connection = opendatabase(true);

$Sname = $_POST["seasname"];
if (strlen($Sname) == 0)
	wrongentry("Not entered from POST");

// Create the Season
// Set the name and dates

$Seas = new Season(0, 'I');
$Seas->Name = $Sname;
$earliest = new Matchdate();
$latest = new Matchdate();
$ret = $Connection->query("SELECT matchdate FROM game WHERE league='I' ORDER BY matchdate limit 1");
if ($ret && $ret->num_rows > 0)  {
	$row = $ret->fetch_array();
	if ($row)
			$earliest->enctime($row[0]);
}
$ret = $Connection->query("SELECT matchdate FROM game ORDER BY matchdate desc limit 1");
if ($ret && $ret->num_rows > 0)  {
	$row = $ret->fetch_array();
	if ($row)
		$latest->enctime($row[0]);
}
$Seas->Startdate = $earliest;
$Seas->Enddate = $latest;
$Seasind = $Seas->create();

//  OK so all we have to do is convert individual league games to historic ones
//  by turning off "current" and setting the season number

$ret = $Connection->query("UPDATE game SET current=0,seasind=$Seasind WHERE current!=0 and league='I'");
if (!$ret)  {
   database_error($Connection->error);
}
$Ngames = $Connection->affected_rows;

// I think that just about does it. Create a news item unless we didn't do anything.

if  ($Ngames > 0)  {
	$nws = new News('ADMINS', "Individual League season now closed and archived as $Sname.", true, "ileague.php");
	$nws->addnews();
}

lg_html_header("End of individual league season complete");
lg_html_nav();
print <<<EOT
<h1>End of individual league season complete</h1>

EOT;
$Sname = htmlspecialchars($Sname);
print <<<EOT
<p>Cleared and archived the individual league season as $Sname.</p>
<p>Archived a total of $Ngames games.</p>

EOT;
lg_html_footer();
?>
