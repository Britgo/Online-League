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
try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
	$player->fetchclub();
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update Player {$player->display_name(false)}";
include 'php/head.php';
?>
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
      if  (!nonblank(form.userid.value))  {
         alert("No userid given");
         return false;
      }
		return true;
}
</script>
<?php
$showadmmenu = true;
include 'php/nav.php';
print <<<EOT
<h1>Update Player {$player->display_name(false)}</h1>
<p>Please update the details of the player as required using the form below.</p>

EOT;
if ($player->played_games() == 0 && $player->count_teams() == 0 && $player->count_hist_teams() == 0)  {
	print <<<EOT
<p>Alternatively <a href="delplayer.php?{$player->urlof()}">Click here</a> to remove
details of the player.</p>

EOT;
}
?>
<p>To enter a new player, you can adjust the fields appropriately
and press the "Add player" button or you can select the "New Player" menu entry on the left.
</p>
<?php
print <<<EOT
<form name="playform" action="updindplayer2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$player->save_hidden()}
<table cellpadding="2" cellspacing="5" border="0">
<tr><td>Player Name</td>
<td><input type="text" name="playname" value="{$player->display_name(false)}"></td></tr>
<tr><td>Club</td><td>

EOT;
$player->clubopt();
?>
</td></tr>
<tr><td>Rank</td><td><?php $player->rankopt(); ?></td></tr>
<?php
// Try to avoid Firefox guessing userid based on the last thing we typed if not there.
$du = $player->display_userid(0);
$dp = $player->disp_passwd();
if (strlen($du) != 0)
	$du = " value=\"" . $du . "\"";
if (strlen($dp) != 0)
	$dp = " value=\"" . $dp . "\"";
$okemch = $player->OKemail?" checked": "";
$bgamemb = $player->BGAmemb?" checked": "";
$ilpaid = $player->ILpaid?" checked": "";
$npil = $player->ILdiv == 0? " selected": "";
print <<<EOT
<tr><td>Userid</td><td><input type="text" name="userid"$du></td></tr>
<tr><td>Password</td><td><input type="password" name="passw"$dp></td></tr>
<tr><td>Email</td>
<td><input type="text" name="email" value="{$player->display_email_nolink()}"></td></tr>
<tr><td colspan="2"><input type="checkbox" name="okem"$okemch>
Check if player has agreed to accept automatic emails</td></tr>
<tr><td>Phone</td>
<td><input type="text" name="phone" size=30 value="{$player->display_phone()}"></td></tr>
<tr><td>Latest time to phone</td><td>

EOT;
$player->latestopt();
print <<<EOT
</td></tr>
<tr><td>Notes</td>
<td><input type="text" name="notes" value="{$player->display_notes()}" size="40"></td></tr>
<tr><td>KGS</td>
<td><input type="text" name="kgs" value="{$player->display_kgs()}" size="10" maxlength="10"></td></tr>
<tr><td>IGS</td>
<td><input type="text" name="igs" value="{$player->display_igs()}" size="10" maxlength="10"></td></tr>
<tr><td colspan="2"><input type="checkbox" name="bgamemb"$bgamemb>
Player is BGA member.
</td></tr>
<tr><td>Individual league division</td>
<td><select name="ildiv" size="0">
<option value="0"$npil>Not playing</option>

EOT;
$maxdivs = max_ildivision() + 1;
for ($d = 1;  $d <= $maxdivs;  $d++)  {
	$ild = $player->ILdiv == $d? " selected": "";
	print <<<EOT
<option value="$d"$ild>$d</option>

EOT;
}	
print <<<EOT
</select></td></tr>
<tr><td>Paid I.L subs</td><td><input type="checkbox" name="ilpaid"$ilpaid></td></tr>
<tr><td>Admin Privs</td>
<td>

EOT;
$player->adminopt();
?>
</td></tr>
<tr><td><input type="submit" name="subm" value="Add Player"></td>
<td><input type="submit" name="subm" value="Update Player"></td></tr>
</table>
</form>
</div>
</div>
</body>
</html>
