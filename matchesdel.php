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
$div = $_GET["div"];
if (strlen($div) == 0)
	wrongentry("Expected to be invoked via GET");

$Connection->query("DELETE FROM lgmatch WHERE divnum=$div");
$Connection->query("DELETE FROM game WHERE divnum=$div and result='N'");
lg_html_header("Delete matches complete");
lg_html_nav();
print <<<EOT
<h1>Delete Matches Completed</h1>
<p>Finished deleting matches for Division $div</p>
<p>Click <a href="matchupd.php">here</a> to return to the match editing page.</p>

EOT;
lg_html_footer();
?>
