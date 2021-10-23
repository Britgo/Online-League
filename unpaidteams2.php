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

$Connection = opendatabase(true);
$emailrep = $_POST["emailrep"];
$mess = $_POST["messagetext"];

if (strlen($mess) == 0)  {
   wrongentry("No message given");
}
if (!isset($_POST['tnum']))  {
   wrongentry("No teams selected");
}
$tar = $_POST['tnum'];
if (empty($tar))  {
   wrongentry("No teams selected");
}
$lookup = array();
foreach ($tar as $t) {
	$lookup[$t] = 1;
}
$teams = array();
$ret = $Connection->query("SELECT name FROM team WHERE paid=0 AND playing!=0 ORDER BY divnum,name");
if (!$ret || $ret->num_rows == 0)
   database_error("Trouble fetching teams");

$num = 0;
while ($row = $ret->fetch_array())  {
	$tname = $row[0];
	if ($lookup[$num])  {
		$team = new Team($tname);
		$team->fetchdets();
		array_push($teams, $team);
	}
	$num++;
}
$rt = "";
if (strlen($emailrep) != 0)
	$rt = "REPLYTO='$emailrep' ";
foreach ($teams as $team) {
	$dest = $team->Captain->Email;
	if (strlen($dest) != 0)  {
		$fh = popen("{$rt}mail -s 'Go League email - Subscription unpaid' $dest", "w");
		fwrite($fh, "$mess\n");
		pclose($fh);
	}
}
lg_html_header("Messages sent");
lg_html_nav();
print <<<EOT
<h1>Messages sent</h1>
<p>Your message regarding unpaid subscriptions has been sent to the following people:</p>
<table class="teamsb">
<tr>
	<th>Name</th>
	<th>Full Name</th>
	<th>Captain</th>
</tr>

EOT;

foreach ($teams as $team) {
	print <<<EOT
<tr>
<td>{$team->display_name()}</td>
<td>{$team->display_description()}</td>
<td>{$team->display_captain()}</td>
</tr>

EOT;
}

print "</table>\n";
lg_html_footer();
?>
