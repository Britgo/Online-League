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
include 'php/team.php';
include 'php/teammemb.php';

try {
	$player = new Player();
	$player->fromid($userid);
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$sel = $_POST["actselect"];
$amount = $_POST["amount"];

$selarr = explode(':', $sel);
if  (count($selarr) < 3)  {
	$mess = "Unexpected POST input";
	include 'php/wrongentry.php';
	exit(0);
}

switch  ($selarr[0])  {
default:
	$mess = "Do not know how to do {$selarr[0]} payments yet";
	include 'php/wrongentry.php';
	exit(0);
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

if ($tot != $amount) {
	$mess = "Total $tot does not match amount $amount";
	include 'php/wrongentry.php';
	exit(0);
}

try {
	if ($type == 'T')  {
		$team = new Team($teamname);
		$team->fetchdets();
		
		// Error if this team has paid
		
		if ($team->Paid)  {
			$mess = "Team $teamname have already paid??";
			include 'php/wrongentry.php';
			exit(0);
		}

		// Check we haven't already got a pending payment for this team
		
		$ret = mysql_query("select ind from pendpay where league='T' and {$team->queryof('descr1')}");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		if (mysql_num_rows($ret) > 0)  {
			$mess = "Duplicated payment record for $teamname";
			include 'php/probpay.php';
			exit(0);
		}
		
		// Create a payment record for the team
		
		$qteam = mysql_real_escape_string($teamname);
		$ret = mysql_query("insert into paycompl (league,descr1,amount,paypal) values ('T','$qteam',$amount,0)");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		$team->setpaid(true);
	}
	else  {
		$pplayer = new Player($first, $last);
		$pplayer->fetchdets();
		
		// Error if this player has paid
		
		if ($pplayer->ILpaid)  {
			$mess = "$first $last is already paid??";
			include 'php/wrongentry.php';
			exit(0);
		}

		// Check we haven't already got a pending payment for this person
		
		$ret = mysql_query("select ind from pendpay where league='I' and descr1='{$pplayer->queryfirst()}' and descr2='{$pplayer->querylast()}'");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		if (mysql_num_rows($ret) > 0)  {
			$mess = "Duplicated payment record for $first $last";
			include 'php/probpay.php';
			exit(0);
		}
		
		// Create a payment record for the person
		// We will have to update it with the token later
		
		$qfirst = mysql_real_escape_string($first);
		$qlast = mysql_real_escape_string($last);
		$ret = mysql_query("insert into paycompl (league,descr1,descr2,amount,paypal) values ('I','$qfirst','$qlast',$amount,0)");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		$pplayer->setpaid();
	}
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Payment Noted";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Payment Noted</h1>
<?php
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
?>
<p><strong>Thank you!</strong></p>
</div>
</div>
</body>
</html>
