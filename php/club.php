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

class ClubException extends Exception {}

class Club {
	public $Code;				// 3-letter code
	public $Name;				// Name of club
	public $Contactfirst;	// First name of contact (not player object as contact mightn't be
	public $Contactlast;		// Last name of contact
	public $Contactemail;	// Email of contact
	public $Contactphone;	// Phone number
	public $Website;			// Club website if any
	public $Night;				// Usual night for playing Sun=0 .. Sat=6
	public $Region;			// Region (not used at present)
	public $Schools;			// BGA Schools (members treated as BGA memb for fee calc)

	public function __construct($n="") {
		if (strlen($n) != 0)
			$this->Code = $n;
		$this->Night = -1;
		$this->Schools = false;
	}

	// Get club code from a get request ?cl=xyz

	public function fromget() {
		$this->Code = $_GET["cl"];
		if (strlen($this->Code) == 0)
			throw new ClubException("Null club code field");
	}

	// Get club code from a hidden field in a form

	public function frompost($prefix = "") {
		$this->Code = $_POST["${prefix}cl"];
		if (strlen($this->Code) == 0)
			throw new ClubException("Null post name field");
	}

	// Assemble MySQL query string from club

	public function queryof() {
		global $Connection;
		$qc = $Connection->real_escape_string($this->Code);
		return "code='$qc'";
	}

	// Assemble Get request

	public function urlof() {
		$c = urlencode($this->Code);
		return "cl=$c";
	}

	// Fetch other details of club from database

	public function fetchdets() {
		global $Connection;
		$q = $this->queryof();
		$ret = $Connection->query("SELECT name,contactfirst,contactlast,contactemail,contactphone,website,meetnight,region,bgaschools FROM club WHERE $q");
		if (!$ret)
			throw new ClubException("Cannot read database for club");
		if ($ret->num_rows == 0)
			throw new ClubException("Cannot find club");
		$row = $ret->fetch_assoc();
		$this->Name = $row["name"];
		$this->Contactfirst = $row["contactfirst"];
		$this->Contactlast = $row["contactlast"];
		$this->Contactemail = $row["contactemail"];
		$this->Contactphone = $row["contactphone"];
		$this->Website = $row["website"];
		$this->Night = $row["meetnight"];
		$this->Region = $row["region"];
		$this->Schools = $row["bgaschools"];
	}

	// These functions put together fields for display on a web page

	public function display_contact() {
		$f = $this->Contactfirst;
		$l = $this->Contactlast;
		return htmlspecialchars("$f $l");
	}

	public function display_code() {
		return htmlspecialchars($this->Code);
	}

	public function display_name() {
		return htmlspecialchars($this->Name);
	}

	// Display email as "send email" so that the mail is sent by the server
	// and the email address is not displayed anywhere

	public function display_contemail($pe = true) {
		if (!$pe || strlen($this->Contactemail) == 0)
			return "-";
		return "<a href=\"sendmail.php?via=club&{$this->urlof()}\" target=\"_blank\">Send email</a>";
	}

	// Display email address only for updating it

	public function display_contemail_nolink() {
		return htmlspecialchars($this->Contactemail);
	}

	public function display_contphone() {
		if (strlen($this->Contactphone) == 0)
			return "-";
		return htmlspecialchars($this->Contactphone);
	}

	// Display website address as lickable clink

	public function display_website() {
		$w = $this->Website;
		if (strlen($w) == 0)
			return "-";
		return "<a href=\"http://$w\" target=\"blank\">$w</a>";
	}

	// Display website non-lickable

	public function display_website_raw() {
		return htmlspecialchars($this->Website);
	}

	// Display meeting night

	public function display_night() {
		if ($this->Night < 0 || $this->Night > 6)
			return "None";
		$nights = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
		return $nights[$this->Night];
	}

	// Save club code as hidden field in form

	public function save_hidden($prefix = "") {
		$c = $this->Code;
		return "<input type=\"hidden\" name=\"${prefix}cl\" value=\"$c\">";
	}

	// Generate selection option for night

	public function nightopt()
	{
		$nights = array("None", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
		print "<select name=\"night\">\n";
		for ($i = -1;  $i <= 6; $i++) {
			if ($i == $this->Night)
				print "<option value=$i selected>";
			else
				print "<option value=$i>";
			print $nights[$i+1];
			print "</option>\n";
		}
		print "</select>\n";
	}

	// Create club

	public function create() {
		global $Connection;
		$qcode = $Connection->real_escape_string($this->Code);
		$qname = $Connection->real_escape_string($this->Name);
		$qcfirst = $Connection->real_escape_string($this->Contactfirst);
		$qclast = $Connection->real_escape_string($this->Contactlast);
		$qemail = $Connection->real_escape_string($this->Contactemail);
		$qphone = $Connection->real_escape_string($this->Contactphone);
		$qweb = $Connection->real_escape_string($this->Website);
		$qreg = $Connection->real_escape_string($this->Region);
		$n = $this->Night;
		$s = $this->Schools? 1: 0;
		$Connection->query("iINSERT INTO club (code,name,contactfirst,contactlast,contactemail,contactphone,website,meetnight,region,bgaschools) VALUES ('$qcode','$qname','$qcfirst','$qclast','$qemail','$qphone','$qweb',$n,'$qreg',$s)");
	}

	// Update club

	public function update() {
		global $Connection;
		$qname = $Connection->real_escape_string($this->Name);
		$qcfirst = $Connection->real_escape_string($this->Contactfirst);
		$qclast = $Connection->real_escape_string($this->Contactlast);
		$qemail = $Connection->real_escape_string($this->Contactemail);
		$qphone = $Connection->real_escape_string($this->Contactphone);
		$qweb = $Connection->real_escape_string($this->Website);
		$qreg = $Connection->real_escape_string($this->Region);
		$n = $this->Night;
		$s = $this->Schools? 1: 0;
		$Connection->query("UPDATE club SET name='$qname',contactfirst='$qcfirst',contactlast='$qclast',contactemail='$qemail',contactphone='$qphone',website='$qweb',meetnight=$n,region='$qreg',bgaschools=$s WHERE {$this->queryof()}");
	}
}

function listclubs() {
	global $Connection;
	$result = array();
	$ret = $Connection->query("SELECT code,name FROM club ORDER BY name");
	if ($ret) {
		while ($row = $ret->fetch_assoc()) {
			$cl = new Club($row["code"]);
			$cl->Name = $row["name"];
			array_push($result, $cl);
		}
	}
	return $result;
}

function list_club_initials() {
	global $Connection;
	$ret = $Connection->query("SELECT name FROM club ORDER BY name");
	$result = array();
	if  ($ret)  {
		$li = "none";
		while ($row = $ret->fetch_assoc()) {
			$ni = strtoupper(substr($row["name"], 0, 1));
			if ($ni != $li) {
				array_push($result, $ni);
				$li = $ni;
			}
		}
	}
	return $result;
}
?>
