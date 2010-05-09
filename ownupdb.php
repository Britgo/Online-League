<?php
//   Copyright 2009 John Collins

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

session_start();
$userid = $_SESSION['user_id'];
$username = $_SESSION['user_name'];
$userpriv = $_SESSION['user_priv'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
try {
	$player = new Player();
	$player->fromid($userid);
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$Title = "Update Player Details";
include 'php/head.php';
print <<<EOT
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.playform;
      if  (!nonblank(form.playname.value))  {
         alert("No player name given");
         return false;
      }
		return true;
}
</script>
<h1>Update Details userid {$player->display_userid(0)}</h1>
<p>Please update your details as required using the form below.</p>
EOT;
?>
<p>Please note that email addresses are <b>not</b> published anywhere. The "send email" links are
all indirect.</p>
<?php
print <<<EOT
<form name="playform" action="ownupdb2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$player->save_hidden()}
<table cellpadding="2" cellspacing="5" border="0">
<tr><td>Player Name</td>
<td><input type="text" name="playname" value="{$player->display_name(false)}"></td></tr>
<tr><td>Club</td>
<td>
EOT;
$player->clubopt();
print <<<EOT
</td></tr>
<tr><td>Rank</td><td>
EOT;
$player->rankopt();
print "</td></tr>\n";
$dp = $player->disp_passwd();
if (strlen($dp) != 0)
	$dp = " value=\"" . $dp . "\"";
$okemch = $player->OKemail?" checked": "";
print <<<EOT
<tr><td>Email</td>
<td><input type="text" name="email" value="{$player->display_email_nolink()}"></td></tr>
<tr><td>OK to send emails about pending matches</td>
<td><input type="checkbox" name="okem"$okemch></td></tr>
<tr><td>Phone</td>
<td><input type="text" name="phone" value="{$player->display_phone()}" size="30"></td></tr>
<tr><td>Password</td><td><input type="password" name="passw"$dp></td></tr>
<tr><td>KGS</td>
<td><input type="text" name="kgs" value="{$player->display_kgs()}" size="10" maxlength="10"></td></tr>
<tr><td>IGS</td>
<td><input type="text" name="igs" value="{$player->display_igs()}" size="10" maxlength="10"></td></tr>
EOT;
?>
</table>
<p>
<input type="submit" name="subm" value="Update Details">
</p>
</form>
</body>
</html>
