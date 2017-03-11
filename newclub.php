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
$club = new Club();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "New Club";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
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
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Create New Club</h1>
<p>Please set up the details of the club as required using the form below.</p>
<form name="clubform" action="updindclub2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<p>
Club Code:
<input type="text" name="clubcode" size="3" maxlength="3">
Name:
<input type="text" name="clubname">
</p>
<p>
Contact:<input type="text" name="contname">
Phone:<input type="text" name="contphone">
Email:<input type="text" name="contemail">
</p>
<p>
Club website:
<input type="text" name="website">
Meeting night:
<?php
$club->nightopt();
?>
</p>
<p>Set this <input type="checkbox" name="schools"> if the club is in BGA schools.
</p>
<p>
<input type="submit" name="subm" value="Add Club">
</p>
</form>
</div>
</div>
</body>
</html>
