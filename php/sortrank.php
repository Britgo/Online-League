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

//  Sort an array of players by descending rank - stably
//  Use bubble sort - May 2010 add "fuzz"

function swapiflessthan(&$a, &$b, $fuzz) {
	if  ($a->Rank->Rankvalue + $fuzz < $b->Rank->Rankvalue)  {
		$t = $a;
		$a = $b;
		$b = $t;
	}
}

function sortrank(&$arr, $fuzz = 0) {
	swapiflessthan($arr[0], $arr[1], $fuzz);
	swapiflessthan($arr[1], $arr[2], $fuzz);
	swapiflessthan($arr[0], $arr[1], $fuzz);
}

?>
