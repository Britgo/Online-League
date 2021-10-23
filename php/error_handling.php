<?php

//   Copyright 2021 John Collins

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

// Routines for error handling.

function err_html_header($title, $header = NULL, $bodyclass = NULL)  {
	if (is_null($header))
		$header = $title;
	print <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>$title</title>
<link href="/league/bgaleague-style.css" type="text/css" rel="stylesheet"></link>
</head>

EOT;
	if  (is_null($bodyclass))
		print "<body>\n";
	else
		print "<body class=\"$bodyclass\">\n";
	print <<<EOT
<h1>$header</h1>

EOT;
}

function err_html_trailer() {
	print <<<EOT
<p>Please resart at the top by clicking <a href="/index.php">here</a>.
<p>Please report this to the BGA if you can. Thank you.</p>
</body>
</html>

EOT;
	exit(0);
}

function report_credentials_error($message)  {
	err_html_header("Credentials error");
	$qmess = htmlspecialchars($message);
	print <<<EOT
<p>Sorry but we were unable to fetch the credentials for the database - cannot proceed.</p>
<p>The message given was: $qmess</p>

EOT;
	err_html_trailer();
}

function  report_connection_error($message)  {
	err_html_header("Connection error", "Database connection error");
	$qmess = htmlspecialchars($message);
	print <<<EOT
<p>Sorry but there has been a database error - cannot proceed.</p>
<p>Database error message was $mess.</p>

EOT;
	err_html_trailer();
}

function database_error($message)  {
	err_html_header("Database or program error");
	$qmess = htmlspecialchars($message);
	print <<<EOT
<p>Sorry, but there has been a database or program error.</p>
<p>The message given was: $qmess</p>

EOT;
	err_html_trailer();
}

function il_player_details($message)  {
	err_html_header("Individual legue get player error");
	$qmess = htmlspecialchars($message);
	print <<<EOT
<p>Sorry something has gone wrong with your player detail posting.</p>
<p>Error message was $qmess.</p>
<p>Please start again from the top by <a href="index.php" title="Go back to home page">clicking here</a>.</p>

EOT;
	err_html_trailer();
	exit(0);
}

function il_not_in_league($player)  {
	err_html_header("Not in league", "Not in individual league", "il");
	print <<<EOT
<p>Sorry, but you, {$player->display_name(false)} are not currently in the individual
league.</p>
<p>If you want to join it, please update your account
<a href="ownupd.php" title="Edit your own details, including whether you want to join the Individual League">here</a>,
otherwise please go back to the top by  <a href="index.php" title="Go back to the home page">clicking here</a>.</p>
<p>Actually I do not really know how you got here.</p>

EOT;
	err_html_trailer();
}

function il_wrong_division($player, $opp) {
	err_html_header("Wrong IL division", NULL, "il");
	print <<<EOT
<p>Sorry, but {$player->display_name(false)} in {$player->ILdiv}
is not currently in the same individual league division as
{$opp->display_name(false)} who is in division {$opp->ILdiv}.</p>
<p>Please go back to the top by  <a href="index.php" title="Go back to the home page">clicking here</a>.</p>
<p>Actually I do not really know how you got here.</p>

EOT;
	err_html_trailer();
}
function il_unknown_player_id($userid)  {
	err_html_header("Unknown player id", NULL, "il");
	print <<<EOT
<p>Sorry, but player name $userid is not known.</p>
<p>Please start again from the top by <a href="index.php" title="Go back to home page">clicking here</a>.</p>

EOT;
	err_html_trailer();
}

function wrongentry($mess = "") {
	err_html_header("Wrong entry", "Wrong entry to form results page");
	print <<<EOT
<p>This page has not been entered correctly. Please try again from a standard page
or start at the top by <a href="index.php">clicking here</a>.</p>

EOT;
	if (strlen($mess) != 0)  {
		$qmess = htmlspecialchars($mess);
		print <<<EOT
<p>The actual error message was $qmess.</p>

EOT;
	}
	err_html_trailer();
}

function game_not_found($mess) {
	err_html_header("Could not find game", "Game scpre add failed");
	$qmess = htmlspecialchars($mess);
	print <<<EOT
<p>I could not find the game result because: $qmess.</p>
<p>Just start again from the top by <a href="index.php" title="Go back to home page">clicking here</a>.</p>

EOT;
	err_html_trailer();
}

function clash_item($description, $value)  {
	err_html_header("$description clash");
	$qval = htmlspecialchars($value);
	print <<<EOT
<p>Your value of $qval for the field $description clashes with an
existing value, please try again.</p>

EOT;
	err_html_trailer();
}

function prob_pay($message, $parsedresp = NULL)  {
	err_html_header("Payment error");
	$qmess = htmlspecialchars($message);
	print <<<EOT
<p>Sorry but there was a problem setting up your payment.
Message was $qmess.</p>

EOT;
	if ($parsedresp)  {
		$apimsg = $parsedresp["L_SHORTMESSAGE0"] . ":" . $parsedresp["L_LONGMESSAGE0"] . " (" . $parsedresp["L_ERRORCODE0"] . ")";
		if (strlen($apimsg) != 0)  {
			$qapimsg = htmlspecialchars($apimsg);
        	print <<<EOT
<p>The PayPal API reported: $qapimsg</p>

EOT;
		}
	}
	err_html_trailer();
}
?>
