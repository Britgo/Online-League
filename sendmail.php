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

//   No frame-ish stuff - done in new window

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/club.php';

$Connection = opendatabase(true);
try {
	$via = $_GET["via"];
	switch ($via) {
	default:
		$player = new Player();
		$player->fromget();
		$name = $player->display_name(false);
		$hidden = $player->save_hidden();
		break;
	case "club":
		$club = new Club();
		$club->fromget();
		include 'php/opendatabase.php';
		$club->fetchdets();
		$name = $club->display_contact();
		$hidden = $club->save_hidden();
		break;
	}
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}
catch (ClubException $e) {
   wrongentry($e->getMessage());
}
lg_html_header("Send a message to $name");
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
//     	return false;
//      }
		return true;
}
</script>

EOT;
lg_html_nav();
print <<<EOT
<h1>Send a message to $name</h1>
<p>Please use the form below to compose a message to $name.</p>
<p>If you are expecting a reply please put your email address in the
"Reply to" box.</p>
<form name="mailform" action="sendmail2.php" method="post" enctype="application/x-www-form-urlencoded"  onsubmit="javascript:return formvalid();">
<input type="hidden" name="via" value="$via">
$hidden
<p>Subject:<input type="text" name="subject"></p>
<p>Reply to:<input type="text" name="emailrep"></p>
<textarea name="messagetext" rows="10" cols="40"></textarea>
<br clear="all">
<input type="submit" name="submit" value="Submit message">
</form>

EOT;
lg_html_footer();
?>
