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

// Calculate the handicap from the difference in rank taking into account what
// division we are in.
// Return false if no handicap otherwise as a message

function hcp_message($g, $pars) {
	if ($g->Result != 'N' || $g->Division < $pars->Hdiv)
		return false;
	$wr = $g->Wplayer->Rank->Rankvalue;
	$br = $g->Bplayer->Rank->Rankvalue;
	$hstones = ($wr - $br) - $pars->Hreduct;
	if ($hstones <= 0)
		return false;
	if ($hstones == 1)
		return  "No Komi";
	if ($hstones <= 9)
		return "$hstones stones handicap";
	$rkomi = ($hstones - 9) * 10;
	return "9 stones handicap plus $rkomi reverse komi";
}
?>
