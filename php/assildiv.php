<?php
//   Copyright 2010-2021 John Collins

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

function ail_getrow($cols, $sel, $ord = "")  {
	global $Connection;
	$q = "SELECT $cols FROM player WHERE ildiv!=0 and $sel";
	if (strlen($ord) != 0)
		$q .= " ORDER BY $ord";
	$q .= " limit 1";
	$ret = $Connection->query($q);
	if  (!$ret || $ret->num_rows == 0)
		return Null;
	return $ret->fetch_assoc();
}

//  Assign an appropriate Individual League division according to the
//  specified rank

function assign_ildiv($rankval) {

	//  If there is only one division, then just return that

	$maxdiv = max_ildivision();
	if  ($maxdiv <= 1)
		return  1;

	// If there is someone else with the same rank return their division

	if  ($row = ail_getrow("ildiv", "rank=$rankval"))
		return $row["ildiv"];

	// Get the division of the next higher rank person, if none,
	// select first division

	$hirow = ail_getrow("ildiv,rank", "rank>$rankval", "rank,ildiv desc");
	if  (!$hirow)
		return  1;

	// Likewise the division of the next lower rank person, if none,
	// select bottom division

	$lorow = ail_getrow("ildiv,rank", "rank<$rankval", "rank desc,ildiv");
	if  (!$lorow)
		return  $maxdiv;

	// Get average rank and select higher division if better than average,
	// otherwise lower

	if  ($rankval >= ($hirow["rank"] + $lorow["rank"]) / 2)
		return  $hirow["ildiv"];
	else
		return  $lorow["ildiv"];
}
?>
