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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
$Title = "Apply for new account";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.playform;
      if (form.turnoff.checked) {
      	alert("You didn't turn off the non-spammer box");
      	return false;
      }
      if (!form.turnon.checked) {
      	alert("You didn't turn on the non-spammer box");
      	return false;
      }
      if  (!nonblank(form.playname.value))  {
         alert("No player name given");
         return false;
      }
      if  (!/^\w+$/.test(form.userid.value))  {
      	alert("No valid userid given");
      	return  false;
      }
      if  (!nonblank(form.email.value))  {
      	alert("No email address given");
      	return  false;
      }
		return true;
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Apply for new account on online league</h1>
<p>Please use the form below to apply for an account on the online leagues.
You will need an account to be included in a team for the team league and to
play at all in the individual league.
</p>
<p><b>Please</b> don't try to create multiple accounts under different names however
bad your playing record is! If you have forgotten your password, select the "remind
password" entry.
</p>
<p>Please note that email addresses and phone numbers are
<b>not</b> published anywhere. The "send email" links are
all indirect.</p>
<form name="playform" action="newacct2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<table cellpadding="2" cellspacing="5" border="0">
<tr><td>Player Name</td>
<td><input type="text" name="playname"></td></tr>
<tr><td>League Userid (initials/KGS name acceptable)</td>
<td><input type="text" name="userid"></td></tr>
<tr><td>Password (leave blank to let system set it)</td>
<td><input type="password" name="passw"></td></tr>
<tr><td>Email (must have)</td>
<td><input type="text" name="email"></td></tr>
<tr><td>Phone number(s)</td>
<td><input type="text" name="phone" size="30"></td></tr>
<tr><td>Latest time to phone</td><td>
<?php
$player = new Player();
$player->latestopt();
?>
</td></tr>
<tr><td>Club (i.e. face-to-face)</td>
<td>
<?php
$player->Club = new Club('NoC');
$player->clubopt();
print <<<EOT
</td></tr>
<tr><td>Rank</td><td>
EOT;
$player->rankopt();
print "</td></tr>\n";
?>
<tr><td>OK to send emails about pending matches</td>
<td><input type="checkbox" name="okem" checked></td></tr>
<tr><td>Include trivial news items in display</td>
<td><input type="checkbox" name="trivia" checked></td></tr>
<tr><td>KGS name</td>
<td><input type="text" name="kgs" size="10" maxlength="10"></td></tr>
<tr><td>IGS name</td>
<td><input type="text" name="igs" size="10" maxlength="10"></td></tr>
<tr><td>I want to play in the individual league</td>
<td><input type="checkbox" name="join"></td></tr>
<tr><td>Notes regarding account</td>
<td><input type="text" name="notes" size="30"></td></tr>
<tr><td colspan=2><input type="checkbox" name="turnoff" checked>
&lt;&lt; Because I'm not a spammer I'm turning this off and this on &gt;&gt;
<input type="checkbox" name="turnon"></td></tr>
</table>
<p>
<input type="submit" name="subm" value="Create Account">
</p>
</form>
</div>
</div>
</body>
</html>
