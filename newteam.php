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
include 'php/team.php';

$Connection = opendatabase(true);
$team = new Team();
lg_html_header("New Team");
print <<<EOT
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

EOT;
lg_html_nav();
print <<<EOT
<h1>Create New Team</h1>
<p>Please set up the details of the team as required using the form below.</p>
<p>You can set up the team members once the team has been created.</p>
<form name="teamform" action="updindteam2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<p>Team Name:<input type="text" name="teamname" size=20>
Full Name:<input type="text" name="teamdescr" size=40></p>
<p>Division:

EOT;
$team->divopt();
print "Captain:";
$team->captainopt();
print <<<EOT
</p>
<p><input type="submit" name="subm" value="Add Team"></p>
</form>

EOT;
lg_html_footer();
?>
