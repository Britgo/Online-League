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

class Matchdate {
	private $timestamp;

	public function __construct($t = null) {
		if ($t)
			$this->timestamp = $t->timestamp;
		else  {
			$dat = getdate();
			$this->timestamp = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
		}
	}

	public function enctime($ds) {
		if (preg_match('/(\d+).(\d+).(\d+)/', $ds, $rm)) {
			$yr = $rm[1];
			$mn = $rm[2];
			$dy = $rm[3];
			$this->timestamp = mktime(12,0,0,$mn,$dy,$yr);
		}
	}

	public function fromget()  {
		$this->enctime($_GET["md"]);
	}

	public function frompost()  {
		$yr = $_POST["year"];
		$mn = $_POST["month"];
		$dy = $_POST["day"];
		$this->timestamp = mktime(12,0,0,$mn,$dy,$yr);
	}

	public function fromhidden($prefix = "") {
		$this->enctime($_POST["{$prefix}md"]);
	}

	public function fromtabrow($mysqlrow, $col = "matchdate") {
		$this->enctime($mysqlrow[$col]);
	}

	public function display() {
		return date("D j M Y", $this->timestamp);
	}

	public function disp_abbrev() {
		return date("d/m/y", $this->timestamp);
	}

	public function display_month() {
		return date("F Y", $this->timestamp);
	}

	public function urlof() {
		$u = date("Y-m-d", $this->timestamp);
		return "md=$u";
	}

	public function queryof() {
		return date("Y-m-d", $this->timestamp);
	}

	public function unequal($d) {
		return $this->timestamp != $d->timestamp;
	}

	public function is_past() {
		$dat = getdate();
		$now = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
		return $now >= $this->timestamp;
	}

	public function is_future() {
		$dat = getdate();
		$now = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
		return $now < $this->timestamp;
	}

	public function set_season($startmon = -1) {
		$times = getdate($this->timestamp);
		$yr = $times["year"];
		$mon = $times["mon"];
		if ($mon < $startmon)
			$mon = $startmon;
		$this->timestamp = mktime(12,0,0,$mon, 1, $yr);
	}

	public function next_month($mint = "m", $mintnum = 1) {
		$times = getdate($this->timestamp);
		$yr = $times["year"];
		$mon = $times["mon"];
		$day = $times["mday"];
		switch ($mint) {
		default:
			$mon += $mintnum;
			if ($mon > 12) {
				$mon = 1;
				$yr++;
			}
			break;
		case 'd':
			$day += $mintnum;
			break;
		case 'w':
			$day += $mintnum * 7;
			break;
		}
		$this->timestamp = mktime(12,0,0,$mon, $day, $yr);
	}

	public function next_day($ndays = 1) {
		$times = getdate($this->timestamp);
		$day = $times["mday"];
		$this->timestamp = mktime(12,0,0,$times["mon"], $day+$ndays,$times["year"]);
	}

	public function season($startmon = 9)  {
		$times = getdate($this->timestamp);
		$y = $times["year"];
		$sstart = mktime(12, 0, 0, $startmon, 1, $y);
		if  ($sstart <= $this->timestamp)
			$y++;
		return  $y;
	}

	public function monthstart() {
		$times = getdate($this->timestamp);
		return  mktime(1, 0, 0, $times["mon"], 1, $times["year"]);
	}

	public function yropt() {
		$dat = getdate($this->timestamp);
		$yrsel = $dat["year"];
		print "<select name=\"year\">\n";
		for ($i = 2009;  $i <= 2030;  $i++) {
			if ($i == $yrsel)
				print "<option selected>$i</option>\n";
			else
				print "<option>$i</option>\n";
		}
		print "</select>\n";
	}

	public function monopt()
	{
		$dat = getdate($this->timestamp);
		$monsel = $dat["mon"];
		$Mnames = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		print "<select name=\"month\">\n";
		for ($i = 1;  $i <= 12; $i++) {
			if ($i == $monsel)
				print "<option value=$i selected>";
			else
				print "<option value=$i>";
			print $Mnames[$i-1];
			print "</option>\n";
		}
		print "</select>\n";
	}

	public function dayopt()
	{
		$dat = getdate($this->timestamp);
		$daysel = $dat["mday"];
		print "<select name=\"day\">\n";
		for ($i = 1;  $i <= 31; $i++) {
			if ($i == $daysel)
				print "<option selected>$i</option>\n";
			else
				print "<option>$i</option>\n";
		}
		print "</select>\n";
	}

	public function dateopt($msg = "Date")
	{
		if (strlen($msg) != 0)
			print "$msg:";
		$this->dayopt();
		$this->monopt();
		$this->yropt();
	}

	public function disphidden($prefix = "")
	{
		$hd = date("Y-m-d", $this->timestamp);
		return  "<input type=\"hidden\" name=\"${prefix}md\" value=\"$hd\">\n";
	}

	public function hidden()
	{
		print $this->disphidden();
	}

	public function haschanged($omd)
	{
		return $this->timestamp != $omd->timestamp;
	}

	public function sortby($omd)
	{
		return $this->timestamp - $omd->timestamp;
	}
}

?>
