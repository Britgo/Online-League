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

$classid = "Nav";
$contentid = "Content";
if (isset($hasfoot))  {
	$classid = "Navwf";
	$contentid = "Contentwf";
}

print <<<EOT
<div id="$classid">

EOT;
?>
<div class="innertube">
<a href="http://www.britgo.org" title="Go to BGA main site">
<img src="images/gohead12.gif" width="133" height="47" alt="BGA Logo" border="0" hspace="0" vspace="0"></a>
<table>
<tr><td><a href="index.php" title="Go to league home page">League Home</a></td></tr>
<tr><td><a href="info.php" title="General Information">Information</a></td></tr>
<tr><td><a href="playing.php" title="How to play in the leagues">Rules</a></td></tr>
<tr><td><a href="news.php" title="Read about recent results and updates">News</a></td></tr>
<tr><td><a href="clubs.php" title="View the list of clubs with members in the leagues">Clubs</a></td></tr>
<tr><td><a href="teams.php" title="View the teams participating in the league">Teams</a></td></tr>
<tr><td><a href="players.php" title="View players in the league">Players</a></td></tr>
<tr><td class="subind"><a href="players.php?by=club" title="VIew players sorted by club">By club</a></td></tr>
<tr><td class="subind"><a href="players.php?by=rank" title="View players sorted by rank">By rank</a></td></tr>
<tr><td class="subind"><a href="players.php?by=clubrank" title="View players sorted by club then by rank">By club/rank</a></td></tr>
<tr><td class="subind"><a href="pbt.php" title="View players by team">By team</a></td></tr>
<tr><td><a href="matches.php" title="View team league matches for current season">Team Matches</a></td></tr>
<tr><td><a href="results.php" title="View results table for team league matches">Team Results</a></td></tr>
<tr><th colspan="2">League Table</th></tr>
<tr><td class="subind"><a href="league.php" title="Display current team league table">Current</a></td></tr>
<tr><td class="subind"><a href="leaguehist.php" title="Display historical league tables including individual league">Historical</a></td></tr>
<!-- <tr><td class="subind"><a href="ileague.php" class="il" title="View league table for Individual League">Individual League</a></td></tr> -->
<?php
if ($logged_in) {
//	print <<<EOT
//<tr><td><a href="addilresult.php" class="il" title="For player use - add result in Individual League">Add IL Result</a></td></tr>
//
//EOT;
	if ($admin)  {
		print <<<EOT
<tr><td><a href="admin.php" class="memb" title="Administer the leagues">Admin menu</a></td></tr>

EOT;
	}
	$qu = htmlspecialchars($username);
	$nummsgs = num_unread_msgs();
	if ($nummsgs == 0)  {
		print <<<EOT
<tr><td><a href="messages.php" title="Send and receive messages to/from opponents">Messages</a></td></tr>

EOT;
	}
	elseif($nummsgs == 1) {
		print <<<EOT
<tr><td><a href="messages.php" class="message" title="Send and receive messages to/from opponents">1 Message</a></td></tr>

EOT;
	}
	else  {
		print <<<EOT
<tr><td><a href="messages.php" class="message" title="Send and receive messages to/from opponents">$nummsgs Messages</a></td></tr>

EOT;
	}
	print <<<EOT
<tr><td><a href="osmatches.php" title="Display your outstanding games in team league">Outstanding</a></td></tr>
<tr><td><a href="ownupd.php" title="Update your own account - rank etc">Update account</a></td></tr>
<tr><td><a href="https://league.britgo.org/payments.php" title="Pay league subscriptions via PayPal">Pay subscriptions</a></td></tr>
<tr><td><a href="logout.php" title="Log yourself out">Logout<br>$qu</a></td></tr>

EOT;
}
?>
</table>
<?php
if (!$logged_in)  {
	if (isset($_COOKIE['user_id']))
		$userid = $_COOKIE['user_id'];
	print <<<EOT
<form name="lifm" action="login.php" method="post" enctype="application/x-www-form-urlencoded">
<p>Userid:<input type="text" name="user_id" id="user_id" value="$userid" size="10"></p>
<p>Password:<input type="password" name="passwd" size="10"></p>
<p><input type="submit" value="Login"></p>
</form>
<p><a href="javascript:lostpw();" title="Get your lost password">Lost password?</a></p>
<p><a href="newacct.php" title="Create yourself an account">Create account</a></p>

EOT;
}
print <<<EOT
<p class="smallnote">(Please note a cookie will be used to save login name and session id only, no other
information).</p>
</div>
</div>
<div id="$contentid">
<div class="innertube">

EOT;
?>
