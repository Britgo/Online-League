<?php
//   Copyright 2009-2021 John Collins

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
include 'php/matchdate.php';

$Connection = opendatabase(true);
$div = $_GET["div"];
if (strlen($div) == 0)
	wrongentry("Expected GET parameter");

$md = new Matchdate();
$md->set_season();

lg_html_header("Initialise Matches for Division $div");
lg_html_nav();
print <<<EOT
<h1>Initialise Matches for division $div</h1>
<form action="matchinit.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="div" value="$div">
<p>

EOT;
$md->dateopt('Starting date');
print <<<EOT
</p>
<p>Allocate matches every
<select name="mintnum" size="0">
<option value="1" selected>1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
</select>
<select name="mint" size="0">
<option value="d">days</option>
<option value="w">weeks</option>
<option value="m" selected>months</option>
</select>
</p>
<p><input type="submit" value="Generate Matches"></p>
</form>
<p>Click <a href="javascript:self.close()">here</a> to close this window.</p>

EOT;
lg_html_footer();
?>
