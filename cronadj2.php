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

function credelfile($postarg, $filename) {
	if (isset($_POST[$postarg]))  {
		$fh = fopen($filename, "w");
		fwrite($fh, "Set\n");
		fclose($fh);
	}
	else  {
		unlink($filename);
	}
}

credelfile("nomatchrem", "nomatchreminder");
credelfile("nopay", "nopayreminder");
credelfile("norss", "norssrun");
header("Location: http://league.britgo.org/cronadj.php");
?>
