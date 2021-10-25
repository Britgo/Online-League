<?php
//   Copyright 2012-2021 John Collins

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

try {
	$player = new Player();
	$player->fromid($Connection->userid);
}
catch (PlayerException $e) {
	wrongentry($e->getMessage();
}

// Grab ourselves a list of pending payments so we don't get mixed up with someone else
// trying to pay the same thing.
// However we delete payments more than 15 minutes old first.

$ret = $Connection->query("DELETE FROM pendpay WHERE paywhen < date_sub(current_timestamp, interval 15 minute)");
if (!$ret)
	database_error($Connection->error);

// Get ourselves an array of pending teams and pending individuals

$pend_teams = array();
$pend_indiv = array();

$ret = $Connection->query("SELECT ind,league,descr1,descr2 FROM pendpay");
if ($ret and $ret->num_rows > 0)  {
	while ($row = $ret->fetch_assoc())  {
		switch ($row["league"])  {
		case "T":
			$pend_teams[$row["descr1"]] = $row["ind"];
			break;
		case "I":
			$f = $row["descr1"];
			$l = $row["descr2"];
			$pend_indiv["$f $l"] = $row["ind"];
			break;
		}
	}
}

// First get ourselves a list of unpaid teams

$unpaid_teams = array();
$ret = $Connection->query("SELECT name FROM team WHERE paid=0 and playing!=0 ORDER BY name");
try {
	if ($ret and $ret->num_rows > 0)  {
		while ($row = $ret->fetch_array())  {
			$name = $row[0];
			if (isset($pend_teams[$name]))		// Cream out "pending" teams
				continue;
			$team = new Team($name);
			$team->fetchdets();
			array_push($unpaid_teams, $team);
		}
	}
}
catch (TeamException $e)  {
	database_error($e->getMessage());
}

// Go over each team and calculate subs for each

foreach ($unpaid_teams as $team)  {
	$team->Subs = 15;
	$membs = $team->list_members();

	// Add £5 for each non BGA member

	foreach ($membs as $memb) {
		$memb->fetchdets();
		if (!$memb->BGAmemb)  {
			$memb->fetchclub();
			if  (!$memb->Club->Schools)  {
				$team->Nonbga += 1;
				$team->Subs += 5;
			}
		}
	}
}

// Likewise get list of unpaid indiv league players

$unpaid_il = array();
//$ret = $Connection->query("SELECT first,last FROM player WHERE ildiv!=0 AND ilpaid=0 ORDER BY last,first");
//if ($ret) {
//	while ($row = $ret->fetch_array())  {
//		$f = $row[0];
//		$l = $row[1];
//		if (isset($pend_indiv["$f $l"]))		// Cream out "pending" players
//			continue;
//		$pl = new Player($f, $l);
//		$pl->fetchdets();
//		$pl->ILsubs = 10;
//		if (!$pl->BGAmemb)  {
//			$pl->fetchclub();
//			if (!$pl->Club->Schools)
//				$pl->ILsubs = 15;
//		}
//		array_push($unpaid_il, $pl);
//	}
//}

?>
