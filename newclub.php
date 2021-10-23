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

$Connection = opendatabase(true);
$club = new Club();
lg_html_header("New Club");
print <<<EOT
<script language="javascript">
function formvalid()
{
      var form = document.clubform;
      if  (!nonblank(form.clubcode.value))  {
         alert("No club code given");
         return false;
      }
      if  (!nonblank(form.clubname.value))  {
      	alert("No club name given");
      	return false;
      }
		return true;
}
</script>

EOT;
lg_html_nav();
print <<<EOT
<h1>Create New Club</h1>
<p>Please set up the details of the club as required using the form below.</p>
<form name="clubform" action="updindclub2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<p>Club Code:<input type="text" name="clubcode" size="3" maxlength="3">
Name:<input type="text" name="clubname"></p>
<p>
Contact:<input type="text" name="contname">
Phone:<input type="text" name="contphone">
Email:<input type="text" name="contemail"></p>
<p>Club website:<input type="text" name="website">
Meeting night:

EOT;
$club->nightopt();
print <<<EOT
</p>
<p>Set this <input type="checkbox" name="schools"> if the club is in BGA schools.</p>
<p><input type="submit" name="subm" value="Add Club"></p>
</form>

EOT;
lg_html_footer();
?>
