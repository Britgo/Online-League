<?php

//   Copyright 2010 John Collins

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

// Code to sort teams for promotion and relegation.

$messages = array();

$ml = max_division();
for ($d = 1; $d <= $ml; $d++) {
	$tl = list_teams($d);
	$nteams = count($tl);
	if ($nteams < 3) {
		array_push($messages, "Not enough teams in division $d");
		continue;
	}
	foreach ($tl as $t) {
		$t->get_scores($pars);
	}
	usort($tl, 'score_compare');
	$maxrank = $tl[0]->Sortrank;
	$minrank = $tl[$nteams-1]->Sortrank;
	// This avoids showing prom/releg if they're all the same as with nothing played.
	if ($maxrank == $minrank)  {
		array_push($messages, "Not enough matches played in division $d");
		continue;
	}
	if ($tl[0]->Sortrank == $tl[1]->Sortrank)
		array_push($messages,
			$d == 1? "Need to have playoff for championship":
			"Need to have playoff for promotion from division $d");
	if  ($tl[$nteams-2]->Sortrank == $tl[$nteams-1]->Sortrank)
		array_push($messages,
			$d == $ml? "Need to have playoff for bottom team":
			"Need to have playoff for relegation from division $d");
	$promo[$d] = $tl[0];
	$releg[$d] = $tl[$nteams-1];
}

?>
