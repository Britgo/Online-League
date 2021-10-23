<?php
//   Copyright 2012-2021 John Collins

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
	$player = new Player();
	$player->fromid($Connection->userid);
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}

$ind = $_GET["ind"];
if (strlen($ind) == 0)
   wrongentry("No indicator given???");

$ret = $Connection->query("SELECT league,descr1,descr2 FROM pendpay WHERE ind=$ind");
if (!$ret)
   database_error($Connection->error);
if ($ret->num_rows == 0)
   wrongentry("Cannot find pending payment, ind=$ind");
$row = $ret->fetch_assoc();

switch  ($row["league"])  {
default:
   wrongentry("Do not know how to do {$row['league']} payments yet");
case  'T':
	$type = 'T';
	$teamname = $row["descr1"];
	break;
case  'I':
	$type = 'I';
	$first = $row["descr1"];
	$last = $row["descr2"];
	break;
}

try {
	if ($type == 'T')  {
		$team = new Team($teamname);
		$team->fetchdets();

		// Error if this team has paid

		if ($team->Paid)
   		wrongentry("Team $teamname have already paid??");
	}
	else  {
		$pplayer = new Player($first, $last);
		$pplayer->fetchdets();

		// Error if this player has paid

		if ($pplayer->ILpaid)
   		wrongentry("$first $last is already paid??");
	}
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}

// Finally delete pending payment

$ret = $Connection->query("DELETE FROM pendpay WHERE ind=$ind");
if (!$ret)
   database_error($Connection->error);

lg_html_header("Payment Cancelled");
lg_html_nav();
print <<<EOT
<h1>Payment Cancelled</h1>

EOT;
if ($type == 'T') {
	print <<<EOT
<p>Payment on behalf of {$team->display_name()} has been cancelled.</p>

EOT;
}
else {
	print <<<EOT
<p>Payment on behalf of {$pplayer->display_name()} has been cancelled.</p>

EOT;
}
print <<<EOT
<p>Please re-enter the <a href="https://league.britgo.org/payments.php">payments page</a>
when you are ready to start again.</p>

EOT;
lg_html_footer();
?>
