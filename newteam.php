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
$team = new Team();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "New Team";
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
?>
<h1>Create New Team</h1>
<p>Please set up the details of the team as required using the form below.</p>
<p>You can set up the team members once the team has been created.</p>
<form name="teamform" action="updindteam2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<p>
Team Name:
<input type="text" name="teamname" size=20>
Full Name:
<input type="text" name="teamdescr" size=40>
</p>
<p>
Division:
<?php
$team->divopt();
print "Captain:";
$team->captainopt();
?>
</p>
<p><input type="submit" name="subm" value="Add Team"></p>
</form>
</div>
</div>
</body>
</html>
