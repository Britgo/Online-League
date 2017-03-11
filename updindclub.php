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
try {
	$club = new Club();
	$club->fromget();
	$club->fetchdets();
}
catch (ClubException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update Club {$club->display_name()}";
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
print <<<EOT
<h1>Update Club {$club->display_name()}</h1>
<p>Please update the details of the club as required using the form below.</p>
<p>Alternatively <a href="delclub.php?{$club->urlof()}">Click here</a> to remove
details of the club.</p>

EOT;
?>
<p>You can enter a new club by adjusting the
fields appropriately and pressing the "Add club" button.
</p>
<?php
print <<<EOT
<form name="clubform" action="updindclub2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$club->save_hidden()}
<p>
Club Code:
<input type="text" name="clubcode" value="{$club->display_code()}" size="3" maxlength="3">
Name:
<input type="text" name="clubname" value="{$club->display_name()}">
</p>
<p>
Contact:<input type="text" name="contname" value="{$club->display_contact()}">
Phone:<input type="text" name="contphone" value="{$club->display_contphone()}">
Email:<input type="text" name="contemail" value="{$club->display_contemail_nolink()}">
</p>
<p>
Club website:
<input type="text" name="website" value="{$club->display_website_raw()}">
Meeting night:
EOT;
$club->nightopt();
$schchk = $club->Schools? " checked": "";
print <<<EOT
</p>
<p>Set this <input type="checkbox" name="schools"$schchk> if the club is in BGA schools.</p>
<p>
<input type="submit" name="subm" value="Add Club">
<input type="submit" name="subm" value="Update Club">
</p>
EOT;
?>
</form>
</div>
</div>
</body>
</html>
