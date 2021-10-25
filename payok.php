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

// Must be logged in and secure

$Connection = opendatabase(true, true, true);

function apiapp(&$arr, $k, $v) {
	array_push($arr, "$k=$v");
}

try {
	$player = new Player();
	$player->fromid($Connection->userid);
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}

$ind = $_POST["ind"];
$tok = $_POST["token"];
$payerid = $_POST["payerid"];
if (strlen($ind) == 0 || strlen($tok) == 0 || strlen($payerid) == 0)
   wrongentry("No indicator given ind=$ind tok=$tok pid=$payerid???");

$ret = $Connection->query("SELECT league,descr1,descr2,token,amount FROM pendpay WHERE ind=$ind");
if (!$ret)
   database_error($Connection->error);

if ($ret->num_rows == 0)
   wrongentry("Cannot find pending payment, ind=$ind");

$row = $ret->fetch_assoc();

// Verify that the token matches up (change this later not to display them)

$rtok = $row["token"];

if ($tok != $rtok)
   wrongentry("Mismatch tokens r=$tok, d=$rtok");

$amount = $row["amount"];

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

// OK now perform the final PayPal phase to record the payment

include 'ppcredentials.php';
$ppcred = getppcredentials();

// Step 1 is to Set it up

$Req_array = array();
apiapp($Req_array, "METHOD", "DoExpressCheckoutPayment");
apiapp($Req_array, "VERSION", urlencode('51.0'));
apiapp($Req_array, "USER", $ppcred->Username);
apiapp($Req_array, "PWD", $ppcred->Password);
apiapp($Req_array, "SIGNATURE", $ppcred->Signature);
apiapp($Req_array, "AMT", $amount);
apiapp($Req_array, "PAYMENTACTION", "Sale");
apiapp($Req_array, "CURRENCYCODE", "GBP");
apiapp($Req_array, "TOKEN", urlencode($tok));
apiapp($Req_array, "PAYERID", urlencode($payerid));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ppcred->Endpoint);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $Req_array));
$chresp = curl_exec($ch);
if  (!$chresp)
	prob_pay("Curl failed: " . curl_error($ch) . " (" . curl_errno($ch) . ")");

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
	$Connection->query("DELETE FROM pendpay WHERE ind=$ind");
	prob_pay("API error in Do Express Checkout", $parsedresp);
}

// Set paid marker and add payment record to database

$qtype =  $Connection->real_escape_string($type);
if ($type == 'T')  {
	$team->setpaid();
	$qdescr1 =  $Connection->real_escape_string($teamname);
	$qdescr2 = '';
}
else  {
	$pplayer->setpaid();
	$qdescr1 =  $Connection->real_escape_string($first);
	$qdescr2 =  $Connection->real_escape_string($last);
}
$Connection->query("INSERT INTO paycompl (league,descr1,descr2,amount) VALUES ('$qtype','$qdescr1','$qdescr2',$amount)");

// Finally delete pending payment

$ret = $Connection->query("DELETE FROM pendpay WHERE ind=$ind");
if (!$ret)
   database_error($Connection->error);

lg_html_header("Payment OK");
lg_html_nav();
print <<<EOT
<h1>Payment OK</h1>

EOT;
if ($type == 'T')
	print <<<EOT
<p>Recorded payment of &pound;$amount on behalf of {$team->display_name()}.</p>

EOT;
else
	print <<<EOT
<p>Recorded payment of &pound;$amount on behalf of {$pplayer->display_name()}.</p>

EOT;
print <<<EOT
<p><strong>Thank you!</strong></p>

EOT;
lg_html_footer();
?>
