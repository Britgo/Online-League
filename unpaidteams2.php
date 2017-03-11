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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';

$emailrep = $_POST["emailrep"];
$mess = $_POST["messagetext"];

if (strlen($mess) == 0)  {
	$mess = "No message given";
	include 'php/wrongentry.php';
	exit(0);
}
if (!isset($_POST['tnum']))  {
	$mess = "No teams selected";
	include 'php/wrongentry.php';
	exit(0);
}	
$tar = $_POST['tnum'];
if (empty($tar))  {
	$mess = "No teams selected";
	include 'php/wrongentry.php';
	exit(0);
}
$lookup = array();
foreach ($tar as $t) {
	$lookup[$t] = 1;
}
$teams = array();
$ret = mysql_query("select name from team where paid=0 and playing!=0 order by divnum,name");
if (!$ret || mysql_num_rows($ret) == 0)  {
	$mess = "Trouble fetching teams";
	include 'php/dataerror.php';
	exit(0);
}
$num = 0;
while ($row = mysql_fetch_array($ret))  {
	$tname = $row[0];
	if ($lookup[$num])  {
		$team = new Team($tname);
		$team->fetchdets();
		array_push($teams, $team);
	}
	$num++;
}
$rt = "";
if (strlen($emailrep) != 0)
	$rt = "REPLYTO='$emailrep' ";
foreach ($teams as $team) {
	$dest = $team->Captain->Email;
	if (strlen($dest) != 0)  {
		$fh = popen("{$rt}mail -s 'Go League email - Subscription unpaid' $dest", "w");
		fwrite($fh, "$mess\n");
		pclose($fh);
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Messages sent";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Messages sent</h1>
<p>Your message regarding unpaid subscriptions has been sent to the following people:
</p>
<table class="teamsb">
<tr>
	<th>Name</th>
	<th>Full Name</th>
	<th>Captain</th>
</tr>
<?php
foreach ($teams as $team) {
	print <<<EOT
<tr>
<td>{$team->display_name()}</td>
<td>{$team->display_description()}</td>
<td>{$team->display_captain()}</td>
</tr>
EOT;
}
?>
</table>
</div>
</div>
</body>
</html>
