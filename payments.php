<?php
//   Copyright 2011 John Collins

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
include 'php/listppay.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Payment of subscriptions";
include 'php/head.php';
if (count($unpaid_teams) + count($unpaid_il) <= 0)
	print "<body>\n";
else
   print "<body onload=\"fillinvals();\">\n";
?>
<script language="javascript" src="webfn.js"></script>
<script language="javascript" src="payfuncs.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Payment of subscriptions</h1>
<?php
$Name = $player->display_name(false);
print <<<EOT
<p>Welcome, $Name, to the payments page.</p>
<p>We manage payments via PayPal, which accepts major
credit and debit cards (not including Amex in the UK, however)
as well as payments via a PayPal account.</p>
<p>You <strong>do not</strong> have to have a PayPal account to use this.</p>
<p>You do not have to use your PayPal account if you do have one, you may prefer instead to use your
credit or debit card (and avoid an immediate debit of your bank account). To do this,
just change the Payment Method once you are on the PayPal screen.</p>

EOT;
if (count($unpaid_teams) + count($unpaid_il) <= 0)
	print <<<EOT
<p>Thanks for visiting but nothing actually needs paying!</p>
<p>Please feel virtuous and visit somewhere else!</p>

EOT;
else {
	print <<<EOT
<!-- PayPal Logo -->
<table border="0" cellpadding="10" cellspacing="0" align="center">
<tr><td align="center"></td></tr>
<tr><td align="center" valign="middle">
<a href="#" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');">
<img src="images/PayPal_mark_180x113.gif" width="180" height="113" alt="Acceptance Mark" border="0" /></a></td>
<td align="center" valign="middle">
<a href="http://www.mastercard.com" target="_blank" title="Visit Mastercard Site"><img src="images/cc_accept.gif" width="316" height="65" alt="Mastercard logo" /></a>
</td>
<!-- <td align="center" valign="middle">
<a href="http://www.maestrocard.com" target="_blank" title="Visit Maestro Site"><img src="images/maestro.gif" width="105" height="65" alt="Maestro Logo" /></a>
</td>
<td align="center" valign="middle">
<a href="http://www.visa.com" target="_blank" title="Visit VISA site"><img src="images/visa.gif" width="104" height="65" alt="VISA Logo" /></a>
</td> -->
</tr>
</table><!-- PayPal Logo -->
<form name="payform" action="pppayment.php" method="post" enctype="application/x-www-form-urlencoded">
<div align="center">
<table id="pftab">

EOT;
include 'php/payopt.php';
print <<<EOT
<tr><td colspan="2"><input type="submit" name="pay" value="Pay Subscription via PayPal"></td></tr>
</table>
</div>
<input type="hidden" name="amount" value="$total">
</form>

<h2>Please note</h2>
<p><strong>Please be sure to check the subscription amount shown is correct before clicking the Pay button!</strong>
You might, in particular, want to check that the surcharge for non-BGA members is correct,
if need be by going to the <a href="teams.php" title="Bring up list of teams">teams list</a> and checking.</p>

EOT;
}
?>
</div>
</div>
</body>
</html>
