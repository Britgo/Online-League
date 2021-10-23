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

$Connection = opendatabase(true);
lg_html_header("Send a message to team captains");
print <<<EOT
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

EOT;
lg_html_nav();
print <<<EOT
<h1>Send a message to team captains</h1>
<p>Please use the form below to compose a message to all team captains.</p>
<p>If you are expecting replies please put your email address in the
"Reply to" box.</p>
<form name="mailform" action="sendtc2.php" method="post" enctype="application/x-www-form-urlencoded"  onsubmit="javascript:return formvalid();">
<p>Subject:<input type="text" name="subject"></p>
<p>Reply to:<input type="text" name="emailrep"></p>
<p>CC to:<input type="text" name="ccto"> (comma or space sep)</p>
<p><input type="checkbox" name="actonly" checked="checked" />Active teams only</p>
<p><input type="radio" name="paid" value="A" checked="checked" />Paid or unpaid
<input type="radio" name="paid" value="U" />Only unpaid
<input type="radio" name="paid" value="P" />Only paid</p>
<p><input type="checkbox" name="admintoo">Mail admins too</p>
<textarea name="messagetext" rows="10" cols="40"></textarea>
<br clear="all">
<input type="submit" name="submit" value="Submit message">
</form>

EOT;
lg_html_footer();
?>
