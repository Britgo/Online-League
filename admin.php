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

lg_html_header("Admin Page");
lg_html_nav();
print <<<EOT
<h1>Administration</h1>
<p>This menu is only intended for administration purposes.
Team captains can assign teams and update results on the matches selection.</p>
<table class="admin">
<tr>
<th>Clubs</th>
<th>Players</th>
<th>Teams</th>
<th>Maintenance</th>
</tr>
<tr>
<td><a href="newclub.php" class="memb" title="Add a new club to the list">New club</a></td>
<td><a href="newplayer.php" class="memb" title="Add a new player">New player</a></td>
<td><a href="newteam.php" class="memb" title="Add a new team">New team</a></td>
<td><a href="getosplayer.php" class="memb" title="View outstanding games in team league for player">O/S matches</a></td>
</tr>
<tr>
<td><a href="clubupd.php" class="memb" title="Update club details">Update clubs</a></td>
<td><a href="playupd.php" class="memb" title="Update player details">Update players</a></td>
<td><a href="teamsupd.php" class="memb" title="Update teams">Update teams</a></td>
<td><a href="matchupd.php" class="memb" title="Update matches">Update matches</a></td>
</tr>
<tr>
<td></td>
<td><a href="rempw.php" class="memb" title="Remind user of password">Remind password</a></td>
<td><a href="sendtc.php" class="memb" title="Send message to team captains">Message team captains</a></td>
<td><a href="fixres.php" class="memb" title="Edit/amend result">Amend result</a></td>
</tr>
<tr>
<td></td>
<td><a href="sendilp.php" class="memb" title="Send message to individual league players">Message IL players</a></td>
<td><a href="unpaidteams.php" class="memb" title="Set message about teams being unpaid">Unpaid message</a></td>
<td><a href="addsgf.php" class="memb" title="Add SGF to game result">Add sgf</a></td>
</tr>
<tr>
<td></td>
<td><a href="ilarchive.php" class="memb" title="Archive Individual League at end of season">Archive Ind League</a></td>
<td><a href="manualpay.php" class="memb" title="Record cheque payment">Manual payment</a></td>
<td><a href="adjparam.php" class="memb" title="Adjust system parameters">Adjust parameters</a></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
<td><a href="cronadj.php" class="memb" title="Cron settings">Adjust cron settings</a></td>
</tr>
</table>

EOT;
lg_html_footer();
?>
