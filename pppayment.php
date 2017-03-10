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

include 'php/checksecure.php';
include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';

function apiapp(&$arr, $k, $v) {
	array_push($arr, "$k=$v");
}

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
		// We will have to update it with the token later
		
		$qteam = mysql_real_escape_string($teamname);
		$ret = mysql_query("insert into pendpay (league,descr1,amount) values ('T','$qteam',$amount)");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		
		$Pdescr = "Online Team League payment of subscription $amount GBP for $teamname";
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
		$ret = mysql_query("insert into pendpay (league,descr1,descr2,amount) values ('I','$qfirst','$qlast',$amount)");
		if (!$ret)  {
			$mess = mysql_error();
			include 'php/dataerror.php';
			exit(0);
		}
		
		$Pdescr = "Online Individual League payment of subscription $amount GBP for $first $last";
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

$ret = mysql_query("select last_insert_id()");
if (!$ret || mysql_num_rows($ret) == 0)  {
	$mess = "Cannot get insert id";
	include 'php/dataerror.php';
	exit(0);
}
$row = mysql_fetch_array($ret);
$ind = $row[0];

// OK now we are ready to do the PayPal stuff.

include 'ppcredentials.php';
$ppcred = getppcredentials();

// Step 1 is to Set it up

$Req_array = array();
apiapp($Req_array, "METHOD", "SetExpressCheckout");
apiapp($Req_array, "VERSION", urlencode('51.0'));
apiapp($Req_array, "USER", $ppcred->Username);
apiapp($Req_array, "PWD", $ppcred->Password);
apiapp($Req_array, "SIGNATURE", $ppcred->Signature);
apiapp($Req_array, "AMT", "$amount.00");
apiapp($Req_array, "PAYMENTACTION", "Sale");
apiapp($Req_array, "CURRENCYCODE", "GBP");
apiapp($Req_array, "DESC", urlencode($Pdescr));
apiapp($Req_array, "RETURNURL", urlencode("https://league.britgo.org/payver.php?ind=$ind"));
apiapp($Req_array, "CANCELURL", urlencode("https://league.britgo.org/paycanc.php?ind=$ind"));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ppcred->Endpoint);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $Req_array));
$chresp = curl_exec($ch);
if  (!$chresp)  {
	$mess = "Curl failed: " . curl_error($ch) . " (" . curl_errno($ch) . ")";
	include 'php/probpay.php';
	exit(0);
}

// Make an array of the response

$responses = explode('&', $chresp);
$parsedresp = array();
foreach ($responses as $r) {
	$ra = explode('=', $r);
	if (count($ra) > 1)
		$parsedresp[strtoupper($ra[0])] = urldecode($ra[1]);
}

// Check success

$ret = strtoupper($parsedresp["ACK"]);
if ($ret != 'SUCCESS' && $ret != "SUCCESSWITHWARNING")  {
	$mess = "API error in Set Express Checkout";
	mysql_query("delete from pendpay where ind=$ind");
	include 'php/probpay.php';
	exit(0);
}

// Get token from response and put into pending payment record

$tok = $parsedresp["TOKEN"];
$qtok = mysql_real_escape_string($tok);
mysql_query("update pendpay set token='$qtok' where ind=$ind");

// Now for stage 2, invoke PayPal with the token

$enctoken = urlencode($tok);
$PPurl = $ppcred->Url;
header("Location: $PPurl&cmd=_express-checkout&token=$enctoken");
exit(0);
?>
