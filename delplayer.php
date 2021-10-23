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

$Connection = opendatabase(true);

try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
}
catch (PlayerException $e)  {
	wrongentry($e->getMessage());
}

$ret = $Connection->query("DELETE FROM player WHERE {$player->queryof()}");
if (!$ret)
	database_error("Cannot delete player");

$nrows = $Connection->affected_rows;
if ($nrows == 0)
	database_error("No player deleted");

lg_html_header("Deletion of {$player->display_name(false)} complete");
lg_html_nav();
print <<<EOT
<h1>Deletion of {$player->display_name(false)} complete</h1>
<p>
Deletion of player {$player->display_name(false)} was successful.</p>
<p>Click <a href="playupd.php" title="Go back to editing players">here</a> to return to the player update menu.</p>

EOT;
lg_html_footer();
?>
