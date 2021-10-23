<?php
//   Copyright 2011-2021 John Collins

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

function  lg_html_header($title, $bodyclass = NULL, $onload = NULL, $onunload = NULL)  {

	// Do header and start of body

	print <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>$title</title>
<meta name="generator" content="Bluefish 2.2.11" >
<meta name="author" content="John M Collins" >
<meta name="date" content="2021-10-14T11:32:26+0100" >
<meta name="copyright" content="John Collins GPLv3">
<meta name="keywords" content="Go,British Go Association,League">
<meta name="description" content="League for BGA web pages">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8">
<meta http-equiv="content-style-type" content="text/css">
<meta http-equiv="expires" content="0">
<link href="bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>

EOT;
	print "<body";
	if  (!is_null($bodyclass))
		print " class=\"$bodyclass\"";
	if  (!is_null($onload))
		print " online=\"$onload\"";
	if  (!!s_null($onunload))
		print " onunload=\"$onunload\"";
	print ">\n";
	print <<<EOT
<script language="javascript" src="webfn.js"></script>
<script type="text/javascript">
var timeout	= 500;
var closetimer	= 0;
var ddmenuitem	= 0;

// open hidden layer
function mopen(id)
{
	// cancel close timer
	mcancelclosetime();

	// close old layer
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';

	// get new layer and show it
	ddmenuitem = document.getElementById(id);
	ddmenuitem.style.visibility = 'visible';

}
// close showed layer
function mclose()
{
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';
}

// go close timer
function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

// cancel close timer
function mcancelclosetime()
{
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

// close layer when click-out
document.onclick = mclose;
</script>

EOT;
}

function lg_html_nav($hasfoot = false)  {

	global $Connection;

	// Select CSS classes according to whether we are doing footer

	$classid = "Nav";
	$contentid = "Content";
	if ($hasfoot)  {
		$classid = "Navwf";
		$contentid = "Contentwf";
	}
	print <<<EOT
<div id="$classid">
<div class="innertube">
<a href="http://www.britgo.org" title="Go to BGA main site">
<img src="images/gohead12.gif" width="133" height="47" alt="BGA Logo" border="0" hspace="0" vspace="0"></a>

EOT;
	if (!isset($_SERVER['HTTPS']) || strlen($_SERVER['HTTPS']) == 0)
   	print <<<EOT
<p><span class="alarm">Use HTTPS!!</span></p>

EOT;
	print <<<EOT
<p style="margin-top: 20px; ">&nbsp;</p>
<ul id="sddm">
<li><a href="index.php" title="Go to league home page">League Home</a></li><br clear="ALL"/>
<li><a href="#" onmouseover="mopen('m1')" onmouseout="mclosetime()">Information</a>
<div id="m1" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
<a href="info.php" title="General Information">General Info</a>
<a href="playing.php" title="How to play in the leagues">Rules</a>
<a href="news.php" title="Read about recent results and updates">News</a>
<a href="clubs.php" title="View the list of clubs with members in the leagues">Clubs</a>
<a href="teams.php" title="View the teams participating in the league">Teams</a>
</div></li><br clear="ALL"/>
<li><a href="#" onmouseover="mopen('m2')" onmouseout="mclosetime()">Players</a>
<div id="m2" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
<a href="players.php" title="View players in the league by name">By name</a>
<a href="players.php?by=club" title="View players sorted by club">By club</a>
<a href="players.php?by=rank" title="View players sorted by rank">By rank</a>
<a href="players.php?by=clubrank" title="View players sorted by club then by rank">By club,rank</a>
<a href="pbt.php" title="View players by team">By team</a>
</div></li><br clear="ALL"/>
<li><a href="#" onmouseover="mopen('m3')" onmouseout="mclosetime()">Matches</a>
<div id="m3" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
<a href="matches.php" title="View league matches for current season">Matches</a>
<a href="results.php" title="View results table for team league matches">Results</a>
</div></li><br clear="ALL"/>
<li><a href="#" onmouseover="mopen('m4')" onmouseout="mclosetime()">League tables</a>
<div id="m4" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
<a href="league.php" title="Display current team league table">Current</a>
<a href="leaguehist.php" title="Display historical league tables including individual league">Historical</a>
<!-- <a href="ileague.php" class="il" title="View league table for Individual League">Individual League</a> -->
</div></li><br clear="ALL"/>

EOT;

	if ($Connection->logged_in)   {
		//	print <<<EOT
		//<li><a href="addilresult.php" class="il" title="For player use - add result in Individual League">Add IL Result</a></li><br clear="ALL"/>
		//
//EOT;
		if ($Connection->admin)  {
			print <<<EOT
<li><a href="admin.php" class="admin" title="Administer the leagues">Admin menu</a></li><br clear="ALL"/>

EOT;
		}

		$qu = htmlspecialchars($Connection->username);
		$nummsgs = $Connection->num_unread_msgs();

		print <<<EOT
<li><a href="#" onmouseover="mopen('m5')" onmouseout="mclosetime()">Account</a>
<div id="m5" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">

EOT;
		if ($nummsgs == 0)  {
			print <<<EOT
<a href="messages.php" title="Send and receive messages to/from opponents">Messages</a>

EOT;
		}
		elseif ($nummsgs == 1) {
			print <<<EOT
<a href="messages.php" class="message" title="Send and receive messages to/from opponents">1 Message</a>

EOT;
		}
		else  {
			print <<<EOT
<a href="messages.php" class="message" title="Send and receive messages to/from opponents">$nummsgs Messages</a>

EOT;
		}
		print <<<EOT
<a href="osmatches.php" title="Display your outstanding games in team league">Outstanding</a>
<a href="ownupd.php" title="Update your own account - rank etc">Update account</a>
<a href="https://league.britgo.org/payments.php" title="Pay league subscriptions via PayPal">Pay subscriptions</a>
<a href="logout.php" title="Log yourself out">Logout<br>$qu</a>
</div></li><br clear="ALL"/>

EOT;
	}
	print <<<EOT
</ul>

EOT;
	if (!$Connection->logged_in)  {
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
}

function lg_html_footer($hasfoot = false)  {
	if ($hasfoot)  {
		$dat = date("Y");
		print <<<EOT
</div>
</div>
<div id="Footer">
<div class="innertube">
<hr>
<p class="note">
This website was designed, authored and programmed by John Collins</p>
<p class="note">Copyright &copy; John Collins 2009-$dat. Licensed under <a href="http://www.gnu.org/licenses/">GPL v3</a>.</p>

// *********************************************************************
// Please do not edit the live file directly as it will break the "Git"
// mechanism to update the live files automatically when a new version
// is pushed. Thanks!
// *********************************************************************

EOT;
	}
	print <<<EOT
</div>
</div>
</body>
</html>

EOT;
}
?>
