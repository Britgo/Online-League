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

$Connection = opendatabase(true);

$subj = $_POST["subject"];
$emailrep = $_POST["emailrep"];
$mess = $_POST["messagetext"];
$admins = isset($_POST["admintoo"]);
$actonly = isset($_POST["actonly"]);
$paid = $_POST["paid"];
$cc = $_POST["ccto"];
$tlist = list_teams();
$mlist = array();
foreach ($tlist as $team) {
	$team->fetchdets();
	if (strlen($team->Captain->Email) == 0)
		continue;
	if ($actonly && !$team->Playing)
		continue;
	if ($team->Paid)  {
		if ($paid == 'U')
			continue;
	}
	elseif ($paid == 'P')
		continue;
	$mlist[$team->Captain->Email] = 1;
}
if (strlen($cc) != 0) {
	foreach (preg_split("/[\s,]+/", $cc) as $m)
		$mlist[$m] = 1;
}
if ($admins) {
	$la = list_admins();
	foreach ($la as $p)
		if (strlen($p->Email) != 0)
			$mlist[$p->Email] = 1;
}
// Set up reply to address.
$rt = "";
if (strlen($emailrep) != 0)
	$rt = "REPLYTO='$emailrep' ";
foreach (array_keys($mlist) as $dest) {
	$fh = popen("{$rt}mail -s 'Go League email - $subj' $dest", "w");
	fwrite($fh, "$mess\n");
	pclose($fh);
}
lg_html_header("Message Sent to team captains");
lg_html_nav();
print <<<EOT
<h1>Message sent to team captains</h1>
<p>I think your message was sent OK to team captains.</p>

EOT;
lg_html_footer();
?>
