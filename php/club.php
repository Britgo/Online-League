<?php

class ClubException extends Exception {}

class Club {
	public $Code;
	public $Name;
	public $Contactfirst;
	public $Contactlast;
	public $Contactemail;
	public $Contactphone;
	public $Website;
	public $Night;
	public $Region;
	
	public function __construct($n="") {
		if (strlen($n) != 0)
			$this->Code = $n;
		$this->Night = -1;
	}
	
	public function fromget() {
		$this->Code = $_GET["cl"];
		if (strlen($this->Code) == 0)
			throw new ClubException("Null club code field"); 
	}
	
	public function frompost($prefix = "") {
		$this->Code = $_POST["${prefix}cl"];
		if (strlen($this->Code) == 0)
			throw new ClubException("Null post name field"); 
	}				

	public function queryof() {
		$qc = mysql_real_escape_string($this->Code);
		return "code='$qc'";
	}
	
	public function urlof() {
		$c = urlencode($this->Code);
		return "cl=$c";
	}
	
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select name,contactfirst,contactlast,contactemail,contactphone,website,meetnight,region from club where $q");
		if (!$ret)
			throw new ClubException("Cannot read database for club");
		if (mysql_num_rows($ret) == 0)
			throw new ClubException("Cannot find club");
		$row = mysql_fetch_assoc($ret);
		$this->Name = $row["name"];
		$this->Contactfirst = $row["contactfirst"];
		$this->Contactlast = $row["contactlast"];
		$this->Contactemail = $row["contactemail"];
		$this->Contactphone = $row["contactphone"];
		$this->Website = $row["website"];
		$this->Night = $row["meetnight"];
		$this->Region = $row["region"];
	}
	
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
	
	public function display_contemail() {
		if (strlen($this->Contactemail) == 0)
			return "-";
		return "<a href=\"sendmail.php?via=club&{$this->urlof()}\" target=\"_blank\">Send email</a>";
	}
	
	public function display_contemail_nolink() {
		return htmlspecialchars($this->Contactemail);
	}
	
	public function display_contphone() {
		if (strlen($this->Contactphone) == 0)
			return "-";
		return htmlspecialchars($this->Contactphone);
	}
	
	public function display_website() {
		$w = $this->Website;
		if (strlen($w) == 0)
			return "-";
		return "<a href=\"http://$w\" target=\"blank\">$w</a>";
	}
	
	public function display_website_raw() {
		return htmlspecialchars($this->Website);
	}
	
	public function display_night() {
		if ($this->Night < 0 || $this->Night > 6)
			return "None";
		$nights = array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
		return $nights[$this->Night];
	}
	
	public function save_hidden($prefix = "") {
		$c = $this->Code;
		return "<input type=\"hidden\" name=\"${prefix}cl\" value=\"$c\">";
	}
	
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
	
	public function create() {
		$qcode = mysql_real_escape_string($this->Code);
		$qname = mysql_real_escape_string($this->Name);
		$qcfirst = mysql_real_escape_string($this->Contactfirst);
		$qclast = mysql_real_escape_string($this->Contactlast);
		$qemail = mysql_real_escape_string($this->Contactemail);
		$qphone = mysql_real_escape_string($this->Contactphone);
		$qweb = mysql_real_escape_string($this->Website);
		$qreg = mysql_real_escape_string($this->Region);
		$n = $this->Night;
		mysql_query("insert into club (code,name,contactfirst,contactlast,contactemail,contactphone,website,meetnight,region) values ('$qcode','$qname','$qcfirst','$qclast','$qemail','$qphone','$qweb',$n,'$qreg')");
	}
	
	public function update() {
		$qname = mysql_real_escape_string($this->Name);
		$qcfirst = mysql_real_escape_string($this->Contactfirst);
		$qclast = mysql_real_escape_string($this->Contactlast);
		$qemail = mysql_real_escape_string($this->Contactemail);
		$qphone = mysql_real_escape_string($this->Contactphone);
		$qweb = mysql_real_escape_string($this->Website);
		$qreg = mysql_real_escape_string($this->Region);
		$n = $this->Night;
		mysql_query("update club set name='$qname',contactfirst='$qcfirst',contactlast='$qclast',contactemail='$qemail',contactphone='$qphone',website='$qweb',meetnight=$n,region='$qreg' where {$this->queryof()}");			
	}
}

function listclubs() {
	$result = array();
	$ret = mysql_query("select code,name from club order by name");
	if ($ret) {
		while ($row = mysql_fetch_assoc($ret)) {
			$cl = new Club($row["code"]);
			$cl->Name = $row["name"];
			array_push($result, $cl);
		}
	}
	return $result;
}

function list_club_initials() {
	$ret = mysql_query("select name from club order by name");
	$result = array();
	if  ($ret)  {
		$li = "none";
		while ($row = mysql_fetch_assoc($ret)) {
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
