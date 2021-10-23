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

print <<<EOT
<tr><td>Paying for</td>
<td><select name="actselect" size="0" onchange="fillinvals();">

EOT;
$total = 0;
$hadm = false;
foreach ($unpaid_teams as $team) {
	$seld = "";
	if (!$hadm)  {
		if ($team->Captain->is_same($player))  {
			$seld = " selected";
			$hadm = true;
			$total = $team->Subs;
		}
	}
	print <<<EOT
<option$seld value="T:{$team->display_name()}:{$team->Nonbga}:{$team->Subs}">
Team: {$team->display_name()}</option>

EOT;
}
foreach ($unpaid_il as $pl) {
	$seld = "";
	$nbgan = $pl->BGAmemb? 0: 1;
	if (!$hadm)  {
		if ($pl->is_same($player)) {
			$seld = " selected";
			$hadm = true;
			$total = $pl->ILsubs;
		}
	}
	print <<<EOT
<option$seld value="I:{$pl->First}:{$pl->Last}:$nbgan:{$pl->ILsubs}">
Individual: {$pl->display_name(false)}</option>

EOT;
}
print <<<EOT
</select></td></tr>
<tr><td>League</td><td>None</td></tr>
<tr><td>For</td><td>None</td></tr>
<tr><td>Surcharge</td><td>None</td></tr>
<tr><td>Total</td><td>$total</td></tr>

EOT;
?>
