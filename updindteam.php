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
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update Team {$team->display_name()}";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.teamform;
      if  (!nonblank(form.teamname.value))  {
         alert("No team name given");
         return false;
      }
      if  (!nonblank(form.teamdescr.value))  {
         alert("No team description given");
         return false;
      }
		return true;
}
</script>
<?php
$showadmmenu = true;
include 'php/nav.php';
print <<<EOT
<h1>Update Team {$team->display_name()}</h1>
<p>Please update the details of the team as required using the form below.</p>
<p>Alternatively <a href="delteam.php?{$team->urlof()}">Click here</a> to remove
details of the team.</p>
<p>To update the team members, <a href="updtmemb.php?{$team->urlof()}">Click here</a>.</p>

EOT;
?>
<p>You can enter a new team here by adjusting the fields appropriately and
pressing the "Add team" button.</p>
<?php
if ($team->Playing)
	print "<p>The team is marked as playing in this season.\n";
else
	print "<p><b>The team is marked as not playing in this season.</b>\n";
print <<<EOT
<a href="updplaying.php?{$team->urlof()}">Change this</a>.</p>

EOT;

if ($team->Paid)
	print "<p>The team is marked as having paid.\n";
else
	print "<p><b>The team is marked as not having paid.</b>\n";
print <<<EOT
<a href="updpaid.php?{$team->urlof()}">Change this</a>.</p>
<form name="teamform" action="updindteam2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$team->save_hidden()}
<p>
Team Name:
<input type="text" name="teamname" value="{$team->display_name()}" size=20>
Full Name:
<input type="text" name="teamdescr" value="{$team->display_description()}" size=40>
</p><p>
Division:
EOT;
$team->divopt();
print "Captain:";
$team->captainopt();
?>
</p>
<p>
<input type="submit" name="subm" value="Add Team">
<input type="submit" name="subm" value="Update Team">
</p>
</form>
</div>
</div>
</body>
</html>
