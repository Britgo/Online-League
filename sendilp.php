<?php
//   Copyright 2012 John Collins

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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Send a message to individual league players";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.mailform;
      if  (!nonblank(form.subject.value))  {
         alert("No subject given");
         return false;
      }
//      if  (!nonblank(form.emailrep.value))  {
//      	alert("No email given");
//      	return false;
//      }
		return true;
}
</script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Send a message to individual league players</h1>
<p>Please use the form below to compose a message to individual league players.</p>
<p>If you are expecting replies please put your email address in the
"Reply to" box.</p>
<form name="mailform" action="sendilp2.php" method="post" enctype="application/x-www-form-urlencoded"  onsubmit="javascript:return formvalid();">
<p>Subject:<input type="text" name="subject"></p>
<p>Reply to:<input type="text" name="emailrep"></p>
<p>CC to:<input type="text" name="ccto"> (comma or space sep)</p>
<p><input type="radio" name="active" value="A" checked="checked" />All players
<input type="radio" name="active" value="P" />Active players
<input type="radio" name="active" value="I" />Inactive players</p>
<p><input type="radio" name="paid" value="A" checked="checked" />Paid or unpaid
<input type="radio" name="paid" value="U" />Only unpaid
<input type="radio" name="paid" value="P" />Only paid</p>
<p><input type="checkbox" name="admintoo">Mail admins too</p>
<textarea name="messagetext" rows="10" cols="40"></textarea>
<br clear="all">
<input type="submit" name="submit" value="Submit message">
</form>
</div>
</div>
</body>
</html>
