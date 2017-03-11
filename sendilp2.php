<?php
//   Copyright 2012 John Collins

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

include 'php/opendatabase.php';

$subj = $_POST["subject"];
$emailrep = $_POST["emailrep"];
$mess = $_POST["messagetext"];
$admins = isset($_POST["admintoo"]);
$active = $_POST["active"];
$paid = $_POST["paid"];
$cc = $_POST["ccto"];

// Need number of divisions to run over

$ml = max_ildivision();
$allilpl = array();
for ($d = 1; $d <= $ml; $d++) {
	$pl = list_players_ildiv($d);
	foreach ($pl as $p)
		array_push($allilpl, $p);
}

// This will be the list of email addresses to send to

$mlist = array();

foreach ($allilpl as $p)  {
	// Get details of player
	$p->fetchdets();
	
	// If no email, nothing to do
	if (strlen($p->Email) == 0)
		continue;
	
	// If person has paid and we are only messaging unpaid or vice versa, skip him
	
	if ($p->ILpaid)  {
		if ($paid == 'U')
			continue;
	}
	elseif  ($paid == 'P')
		continue;
		
	// If we are sending to active or inactive players, we have to get scores
	
	if ($active != "A")  {
		$p->get_scores();
		$ng = $p->played_games(true, 'I');
		if ($ng == 0)  {  // Nothing played
			if ($active == 'P')  // Skip him if he's played some
				continue;
		}
		elseif ($active == 'I')  // Played, skip if he hasn't
			continue;
	}
	
	// Record player by email address
	
	$mlist[$p->Email] = 1;
}

// Add in CC to

if (strlen($cc) != 0) {
	foreach (preg_split("/[\s,]+/", $cc) as $m)
		$mlist[$m] = 1;
}

//  Add in admins if required

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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Message Sent to individual league players";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Message sent to individual league players</h1>
<p>I think your message was sent OK to individual league players.</p>
</div>
</div>
</body>
</html>
