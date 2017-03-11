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

$ind = $_GET["ind"];
if (strlen($ind) == 0)  {
	$mess = "No indicator given???";
	include 'php/wrongentry.php';
	exit(0);
}

$ret = mysql_query("select league,descr1,descr2 from pendpay where ind=$ind");
if (!$ret)  {
	$mess = mysql_error();
	include 'php/dataerror.php';
	exit(0);
}
if (mysql_num_rows($ret) == 0)  {
	$mess = "Cannot find pending payment, ind=$ind";
	include 'php/wrongentry.php';
	exit(0);
}
$row = mysql_fetch_assoc($ret);

switch  ($row["league"])  {
default:
	$mess = "Do not know how to do {$row['league']} payments yet";
	include 'php/wrongentry.php';
	exit(0);
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
		
		if ($team->Paid)  {
			$mess = "Team $teamname have already paid??";
			include 'php/wrongentry.php';
			exit(0);
		}
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

// Finally delete pending payment

$ret = mysql_query("delete from pendpay where ind=$ind");
if (!$ret)  {
	$mess = mysql_error();
	include 'php/dataerror.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Payment Cancelled";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Payment OK</h1>
<?php
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
?>
<p>Please re-enter the <a href="https://league.britgo.org/payments.php">payments page</a>
when you are ready to start again.</p>
</div>
</div>
</body>
</html>
