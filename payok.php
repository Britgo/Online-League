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

$ind = $_POST["ind"];
$tok = $_POST["token"];
$payerid = $_POST["payerid"];
if (strlen($ind) == 0 || strlen($tok) == 0 || strlen($payerid) == 0)  {
	$mess = "No indicator given ind=$ind tok=$tok pid=$payerid???";
	include 'php/wrongentry.php';
	exit(0);
}

$ret = mysql_query("select league,descr1,descr2,token,amount from pendpay where ind=$ind");
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

// Verify that the token matches up (change this later not to display them)

$rtok = $row["token"];

if ($tok != $rtok) {
	$mess = "Mismatch tokens r=$tok, d=$rtok";
	include 'php/wrongentry.php';
	exit(0);
}

$amount = $row["amount"];

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
	$mess = "API error in Do Express Checkout";
	mysql_query("delete from pendpay where ind=$ind");
	include 'php/probpay.php';
	exit(0);
}

// Set paid marker and add payment record to database

$qtype =  mysql_real_escape_string($type);
if ($type == 'T')  {
	$team->setpaid();
	$qdescr1 =  mysql_real_escape_string($teamname);
	$qdescr2 = '';
}
else  {
	$pplayer->setpaid();
	$qdescr1 =  mysql_real_escape_string($first);
	$qdescr2 =  mysql_real_escape_string($last);
}
mysql_query("insert into paycompl (league,descr1,descr2,amount) values ('$qtype','$qdescr1','$qdescr2',$amount)");

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
$Title = "Payment OK";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Payment OK</h1>
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
