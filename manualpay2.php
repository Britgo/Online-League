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

$sel = $_POST["actselect"];
$amount = $_POST["amount"];

$selarr = explode(':', $sel);
if  (count($selarr) < 3)
   wrongentry("Unexpected POST input");

switch  ($selarr[0])  {
default:
   wrongentry("Do not know how to do {$selarr[0]} payments yet");
case  'T':
	$type = 'T';
	$teamname = $selarr[1];
	$nonbga = $selarr[2];
	$tot = $selarr[3];
	break;
case  'I':
	$type = 'I';
	$first = $selarr[1];
	$last = $selarr[2];
	$nonbga = $selarr[3];
	$tot = $selarr[4];
	break;
}

// Just check this makes sense

if ($tot != $amount)
   wrongentry("Total $tot does not match amount $amount");

try {
	if ($type == 'T')  {
		$team = new Team($teamname);
		$team->fetchdets();

		// Error if this team has paid

		if ($team->Paid)
   		wrongentry("Team $teamname have already paid??");

		// Check we haven't already got a pending payment for this team

		$ret = $Connection->query("SELECT ind from pendpay WHERE league='T' AND {$team->queryof('descr1')}");
		if (!$ret)
   		database_error($Connection->error);

		if ($ret->num_rows > 0)
			prob_pay("Duplicated payment record for $teamname");

		// Create a payment record for the team

		$qteam = $Connection->real_escape_string($teamname);
		$ret = $Connection->query("INSERT INTO paycompl (league,descr1,amount,paypal) VALUES ('T','$qteam',$amount,0)");
		if (!$ret)
   		database_error($Connection->error);
		$team->setpaid(true);
	}
	else  {
		$pplayer = new Player($first, $last);
		$pplayer->fetchdets();

		// Error if this player has paid

		if ($pplayer->ILpaid)
   		wrongentry("$first $last is already paid??");

		// Check we haven't already got a pending payment for this person

		$ret = $Connection->query("SELECT ind FROM pendpay WHERE league='I' AND descr1='{$pplayer->queryfirst()}' AND descr2='{$pplayer->querylast()}'");
		if (!$ret)
   		database_error($Connection->error);

		if ($ret->num_rows > 0)
			prob_pay("Duplicated payment record for $first $last");

		// Create a payment record for the person
		// We will have to update it with the token later

		$qfirst = $Connection->real_escape_string($first);
		$qlast = $Connection->real_escape_string($last);
		$ret = $Connection->query("INSERT INTO paycompl (league,descr1,descr2,amount,paypal) VALUES ('I','$qfirst','$qlast',$amount,0)");
		if (!$ret)
   		database_error($Connection->error);
		$pplayer->setpaid();
	}
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}

lg_html_header("Payment Noted");
lg_html_nav();
print <<<EOT
<h1>Payment Noted</h1>

EOT;
if ($type == 'T') {
	print <<<EOT
<p>Recorded payment of &pound;$amount on behalf of {$team->display_name()}.</p>

EOT;
}
else {
	print <<<EOT
<p>Recorded payment of &pound;$amount on behalf of {$pplayer->display_name()}.</p>

EOT;
}
print <<<EOT
<p><strong>Thank you!</strong></p>

EOT;
lg_html_footer();
?>
