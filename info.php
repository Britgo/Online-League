<?php
//   Copyright 2009-2021 John Collins

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

$Connection = opendatabase();
lg_html_header("General Information");
lg_html_nav();
print <<<EOT
<h1>Information about the league</h1>
<h2>Organisation</h2>
<p>The BGA League Tournament is a team tournament, played online over the course of each year, almost entirely on the
<a href="http://www.gokgs.com" target="_blank">KGS Go Server</a> and in the "British Room".</p>
<p>The purpose of the league is to encourage interaction between players in different areas of the country and to
encourage online play amongst members of the BGA.</p>
<p>Each team has three or more members, in most cases taken from a local Go club of the same name, but in a few cases from
widely geographically separated members as a means of including those who are unable to attend a local Go club.</p>
<p>The teams are arranged into divisions of roughly equal size. Each team plays each other team in the same
division at least once, and usually twice over the course of the season.</p>
<p>New entrant teams are assigned to a division containing players of similar strength at the organisers'
discretion.</p>
<p>Each team has a captain, who is repsonsible for marshalling the players and organising the matches in
co-operation with his or her counterparts in the other teams.</p>
<h2>Matches</h2>
<p>Three players from each team participate in each match. Each player from each team plays a game against his or her
counterpart in the opposing team at a time to suit the players concerned.</p>
<p>The match is won by the team with the majority of won games.</p>
<p>Most games are recorded and from time to time teachers and experts may provide reviews
of games to assist player development. From time to time reviews of games may appear
in the <i>British Go Journal</i>.</p>
<h2>Joining</h2>
<p>We are now taking entries for the 2016 league season, which will get under way hopefully in February 2016.
If you would like to join it, either as an individual wanting to be assigned to a team, or a new team wanting to join,
please let us know as soon as possible. Please contact the Online League coordinators at j-m-c AT toad DOT me DOT uk (without the hyphens).</p>
<p>Note that we will consider applications from non-UK players to join teams and play in the league, but they must be prepared to
pay their share of the subscription and also fit in with UK times.</p>
<h2>Rules</h2>
<p>A full description of playing games is to be found
under <a href="playing.php" title="Read description of rules and instructions for playing">rules</a>.</p>

<p>Players wishing to captain a team must be members of the British Go Association,
which can be joined <a href="http://www.britgo.org/join" target="_blank" title="Join the BGA">here</a>.
<h2>Subscriptions</h2>
<p>We charge &pound;15 per season for each team in the team league, together with
an extra &pound;5 each non-BGA member in each team.</p>
<h2>Prizes</h2>
<p>The champions of the top division in the league will win the League Tournament Shield,
<a href="index.php" title="Home page to view shield">shown on the first page</a> and in addition for the first 5 years will receive three copies
of the GoGoD Database and Encyclopaedia.</p>
<p>The BGA Council wishes to express its gratitude to Games of Go on Disk
for their generous donation of the online league shield and copies of the GoGoD CD.</p>
<p>The winners of the other divisions in each season will be promoted to the next higher division for the next season.</p>
<p>The bottommost team in each division in each season, apart from the bottom division, will be relegated to the next lower division for the next season.</p>
<h2>Individual League</h2>
<p>Originally there was an Individual League, but this was discontinued in October 2012. We named the prizes for that the <i>Xinyi Lu Prize</i>,
after Xinyi Lu, an enthusiastic and well-liked player, who whilst leading (and ultimately still winning) the division 2
in the first full season of the Individual Leauge, was tragically killed whilst on holiday in China in October 2011.</p>
<h2>League standings and history</h2>
<p>You can see the current league standings and historical records using the menu items to the left, together with historical records.
In the majority of cases you can download and review the actual games. For any further questions, please contact the Online League
coordinators at online-league AT britgo DOT org (without the hyphen).</p>

EOT;
lg_html_footer();
?>
