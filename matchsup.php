<?php
//   Copyright 2009 John Collins

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
include 'php/matchdate.php';
$div = $_GET["div"];
if (strlen($div) == 0) {
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Initialise Matches for Division $div";
include 'php/head.php';
$md = new Matchdate();
$md->set_season();
print <<<EOT
<body>
<h1>Initialise Matches for division $div</h1>
<form action="matchinit.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="div" value="$div">
<p>
EOT;
$md->dateopt('Starting date');
?>
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
<p>
<input type="submit" value="Generate Matches">
</p>
</form>
<p>Click <a href="javascript:self.close()">here</a> to close this window.</p>
</body>
</html>
