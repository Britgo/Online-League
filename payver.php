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

// This is the module called when all goes OK with the initial transaction, we need to get details of the
// transaction from Paypal and set up to call the final confirmation.

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

$ind = $_GET["ind"];
$tok = $_GET["token"];
if (strlen($ind) == 0 || strlen($tok) == 0)  {
	$mess = "No indicator given ind=$ind tok=$tok???";
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
$amount = $row["amount"];
if ($tok != $rtok) {
	$mess = "Mismatch tokens r=$tok, d=$rtok";
	include 'php/wrongentry.php';
	exit(0);
}

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

// OK now we are ready to do the PayPal stuff stage 3.

include 'ppcredentials.php';
$ppcred = getppcredentials();

// Step 3 is to get the details

$Req_array = array();
apiapp($Req_array, "METHOD", "GetExpressCheckoutDetails");
apiapp($Req_array, "VERSION", urlencode('51.0'));
apiapp($Req_array, "USER", $ppcred->Username);
apiapp($Req_array, "PWD", $ppcred->Password);
apiapp($Req_array, "SIGNATURE", $ppcred->Signature);
$utok = urlencode($tok);
apiapp($Req_array, "TOKEN", $utok);

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
$payerid = $parsedresp["PAYERID"];
$qpayerid = htmlspecialchars($payerid);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Please confirm payment";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Please Confirm Payment OK</h1>
<?php
if ($type == 'T') {
	print <<<EOT
<p>About to record payment of &pound;$amount on behalf of {$team->display_name()}.</p>

EOT;
}
else {
	print <<<EOT
<p>About to record payment of &pound;$amount on behalf of {$pplayer->display_name()}.</p>

EOT;
}
print <<<EOT
<p>The payment has been entered by {$player->display_name(false)}, PayPal account details are for
EOT;
print " ";
print htmlspecialchars($parsedresp["FIRSTNAME"] . " " . $parsedresp["LASTNAME"]);
print <<<EOT
.</p>

<p>Please confirm this is OK and click the button below:</p>
<form action="payok.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="ind" value="$ind" />
<input type="hidden" name="token" value="$utok" />
<input type="hidden" name="payerid" value="$qpayerid" />
<p>Choose option <input type="submit" name="Confirm" value="Confirm payment" /> or
<a href="http://league.britgo.org/paycanc.php?ind=$ind">Cancel the payment</a>.</p>
</form>

EOT;
?>
</table>
</div>
</div>
</body>
</html>
