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

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/matchdate.php';
include 'php/news.php';

$Connection = opendatabase();
lg_html_header("News Items");
lg_html_nav();
print <<<EOT
<h1>News</h1>
<p>The following is a list in reverse date order of events on the league and
the website.</p>
<p>The userid is that of the person who made the update and the date is when the
update was made not necessarily when a game was played.</p>
<table class="news">
<tr>
<th>Date</th>
<th>Userid</th>
<th>Item</th>
</tr>

EOT;
if ($Connection->logged_in) {
	try {
		$player = new Player();
		$player->fromid($Connection->userid);
		$triv = $player->Trivia;
	}
	catch (PlayerException $e) {
		$triv = false;
	}
}
else
	$triv = false;
if ($triv)
	$triv = "";
else
	$triv = " WHERE trivial=0";
$ret = $Connection->query("SELECT ndate,user,item,link FROM news$triv ORDER BY ndate desc");
if ($ret && $ret->num_rows > 0)  {
	while ($row = $ret->fetch_assoc())  {
		$n = new News();
		$n->fromrow($row);
		$lnk = $n->display_link();
		$b = $eb = "";
		if (strlen($lnk) > 0) {
			$b = "<b>";
			$eb = "</b>";
		}
		print <<<EOT
<tr>
<td valign="top">$b{$n->display_date()}$eb</td>
<td valign="top">$b{$n->display_user()}$eb</td>
<td>$b{$n->display_item()} $lnk$eb</td>
</tr>

EOT;
	}
}
print <<<EOT
</table>

EOT;
lg_html_footer();
