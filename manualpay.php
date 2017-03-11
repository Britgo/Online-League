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
if (!$admin) {
	$mess = "You have to be logged in as an admin to see this page";
	include 'php/wrongentry.php';
	exit(0);
}
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
$Title = "Record Manual Payments";
include 'php/head.php';
if (count($unpaid_teams) + count($unpaid_il) <= 0)
	print "<body>\n";
else
   print "<body onload=\"fillinvals();\">\n";
?>
<script language="javascript" src="webfn.js"></script>
<script language="javascript" src="payfuncs.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Record Manual Payments</h1>
<?php
if (count($unpaid_teams) + count($unpaid_il) <= 0)
	print <<<EOT
<p>Thanks for visiting but nothing actually needs paying!</p>
<p>Please feel virtuous and visit somewhere else!</p>

EOT;
else {
	print <<<EOT
<p>This page is for recording manual payments (other than Paypal). Please select the team or player involved
from the menu below and click where indicated.</p>
<form name="payform" action="manualpay2.php" method="post" enctype="application/x-www-form-urlencoded">
<div align="center">
<table id="pftab">

EOT;
include 'php/payopt.php';
print <<<EOT
<tr><td colspan="2"><input type="submit" name="pay" value="Record manual payment"></td></tr>
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
