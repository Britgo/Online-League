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

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/matchdate.php';
include 'php/news.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "News items";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
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
<?php
if ($logged_in) {
	try {
		$player = new Player();
		$player->fromid($userid);
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
	$triv = " where trivial=0";
$ret = mysql_query("select ndate,user,item,link from news$triv order by ndate desc");
if ($ret && mysql_num_rows($ret) > 0)  {
	while ($row = mysql_fetch_assoc($ret))  {
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
?>
</table>
</div>
</div>
</body>
</html>
