<?php
//   Copyright 2009 John Collins

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

class PlayerException extends Exception {}

class Player  {
	public $First;
	public $Last;
	public $Rank;
	public $Club;
	public $Email;
	public $OKemail;
	public $Phone;
	public $KGS;
	public $IGS;
	public $Admin;
	public $Userid;
	public $BGAmemb;
	public $ILdiv;
	public $ILpaid;
	public $Sortrank;
	public $Notes;
	public $Latestcall;
	public $Trivia;
	public $ILsubs;
	private $Gotrecs;
	private $Played;
	private $Won;
	private $Drawn;
	private $Lost;
	private $Playeds;		// This season
	private $Wons;
	private $Drawns;
	private $Losts;

	// Construct a player object, possibly starting from various
	// versions of the name
		
	public function __construct($f = "", $l = "") {
		if (strlen($f) != 0)  {
			if (strlen($l) != 0) {
				$this->First = $f;
				$this->Last = $l;
			}
			elseif (preg_match("/(.*)\s+(.+)/", $f, $matches))  {
				$this->First = $matches[1];
				$this->Last = $matches[2];
			}
			else
				throw new PlayerException("Cannot parse name");
			}
			$Gotrecs = '';
			$this->Admin = 'N';
			$this->Rank = new Rank();
			$this->OKemail = false;
			$this->BGAmemb = false;
			$this->ILdiv = 0;
			$this->ILpaid = false;
			$this->Sortrank = 0;
			$this->Trivia = true;
			$this->ILsubs = 0;
	}

	// Fill in the name of the player from a "get" request
		
	public function fromget($prefix = "", $htd = false) {
		$this->First = $_GET["${prefix}f"];
		$this->Last = $_GET["${prefix}l"];
		if ($htd) {
			$this->First = htmlspecialchars_decode($this->First);
			$this->Last = htmlspecialchars_decode($this->Last);
		}
		if (strlen($this->First) == 0 || strlen($this->Last) == 0)
			throw new PlayerException("Null name field"); 
	}

	// Use me to get the player we are talking about from a hidden field
	// We'll still perhaps need to get the rest
	
	public function frompost($prefix = "") {
		$this->First = $_POST["${prefix}f"];
		$this->Last = $_POST["${prefix}l"];
		if (strlen($this->First) == 0 || strlen($this->Last) == 0)
			throw new PlayerException("Null post name field"); 
	}
	
	// Use me to get details starting from userid
	
	public function fromid($id) {
		$qid = mysql_real_escape_string($id);
		$ret = mysql_query("select first,last,rank,club,email,okmail,trivia,phone,kgs,igs,admin,bgamemb,ildiv,ilpaid,notes,latestcall from player where user='$qid'");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new PlayerException("Unknown player userid $id");
		$row = mysql_fetch_assoc($ret);
		$this->First = $row['first'];
		$this->Last = $row['last'];
		$this->Rank = new Rank($row["rank"]);
		$this->Club = new Club($row["club"]);
		$this->Email = $row["email"];
		$this->Phone = $row["phone"];
		$this->KGS = $row["kgs"];
		$this->IGS = $row["igs"];
		$this->Admin = $row["admin"];
		$this->Userid = $id;
		$this->OKemail = $row["okmail"];
		$this->Trivia = $row["trivia"];
		$this->BGAmemb = $row["bgamemb"];
		$this->ILdiv = $row["ildiv"];
		$this->ILpaid = $row["ilpaid"];
		$this->Notes = $row["notes"];
		$this->Latestcall = $row["latestcall"];
	}

	// Generate a MySQL query from a player object
		
	public function queryof($prefix = "") {
		$qf = mysql_real_escape_string($this->First);
		$ql = mysql_real_escape_string($this->Last);
		return "${prefix}first='$qf' and ${prefix}last='$ql'";
	}

	// For when we just want the MySQL rendering of the First name
		
	public function queryfirst() {
		return mysql_real_escape_string($this->First);
	}

	// Ditto last name
		
	public function querylast() {
		return mysql_real_escape_string($this->Last);
	}

	// For packaging up a name as a search string
		
	public function urlof() {
		$f = urlencode($this->First);
		$l = urlencode($this->Last);
		return "f=$f&l=$l";
	}

	// For packaging up a name in a selection field
		
	public function selof() {
		$f = $this->First;
		$l = $this->Last;
		return "$f:$l";
	}

	// For undoing the above
		
	public function fromsel($pl) {
		if  (!preg_match("/(.*):(.*)/", $pl, $matches))
			throw new PlayerException("Invalid player selection");
		$this->First = $matches[1];
		$this->Last = $matches[2];
	}

	// Get the rest of the details having got the name
		
	public function fetchdets() {
		$q = $this->queryof();
		$ret = mysql_query("select rank,club,email,okmail,trivia,phone,kgs,igs,admin,user,bgamemb,ildiv,ilpaid,notes,latestcall from player where $q");
		if (!$ret)
			throw new PlayerException("Cannot read database for player $q");
		if (mysql_num_rows($ret) == 0)
			throw new PlayerException("Cannot find player");
		$row = mysql_fetch_assoc($ret);
		$this->Rank = new Rank($row["rank"]);
		$this->Club = new Club($row["club"]);
		$this->Email = $row["email"];
		$this->Phone = $row["phone"];
		$this->KGS = $row["kgs"];
		$this->IGS = $row["igs"];
		$this->Admin = $row["admin"];
		$this->Userid = $row["user"];
		$this->OKemail = $row["okmail"];
		$this->Trivia = $row["trivia"];
		$this->BGAmemb = $row["bgamemb"];
		$this->ILdiv = $row["ildiv"];
		$this->ILpaid = $row["ilpaid"];
		$this->Notes = $row["notes"];
		$this->Latestcall = $row["latestcall"];
	}

	// Get more info about the club
		
	public function fetchclub() {
		try {
			$this->Club->fetchdets();
		}
		catch (ClubException $e) {
			// If unknown club set to No club
			$this->Club = new Club('xxx');
			$this->Club->fetchdets();
		}
	}

	// Are we talking about same player
		
	public function is_same($pl) {
		return $this->First == $pl->First && $this->Last == $pl->Last;
	}

	// Prepare first name for display
		
	public function display_first() {
		return htmlspecialchars($this->First);
	}

	// Prepare last name for display
	
	public function display_last() {
		return htmlspecialchars($this->Last);
	}

	// Display whole name
		
	public function display_name($displink = true) {
		$f = $this->First;
		$l = $this->Last;
		$ret = htmlspecialchars("$f $l");
		if ($displink)
				$ret = "<a href=\"playgames.php?{$this->urlof()}\" class=\"name\" title="Show details">$ret</a>";
		return $ret;
	}
	
	// Display initials
	
	public function display_initials($displink = false) {
		$ret = substr($this->First, 0, 1) . substr($this->Last, 0, 1);
		if ($displink)
				$ret = "<a href=\"playgames.php?{$this->urlof()}\" class=\"name\">$ret</a>";
		return $ret;		
	}

	// Display rank in standard format
		
	public function display_rank() {
		return $this->Rank->display();
	}

	// Get initial letter of last name
		
	public function get_initial() {
		return strtoupper(substr($this->Last, 0, 1));
	}

	// Get initial letter of club name
		
	public function get_club_initial() {
		return strtoupper(substr($this->Club->Name, 0, 1));
	}

	// Get KGS handle
		
	public function display_kgs() {
		return htmlspecialchars($this->KGS);
	}

	// Get IGS handlie
	
	public function display_igs() {
		return htmlspecialchars($this->IGS);
	}

	// Display KGS and IGS handles hopefully optimally
	// We now display KGS online names if they exist without a prefix
	// IGS is only displayed if it exists and is different or there's no KGS
	
	public function display_online() {
		$k = $this->KGS;
		$i = $this->IGS;
		if (strlen($k) != 0) {
			if (strlen($i) != 0 && strcasecmp($k, $i) != 0)
				$online = "KGS:$k IGS:$i";
			else
				$online = $k;
		}
		elseif (strlen($i) != 0)
			$online = "IGS:$i";
		else
			$online = "-";	
		return htmlspecialchars($online);
	}

	// Display user id
		
	public function display_userid($wminus=1) {
		if ($wminus && strlen($this->Userid) == 0)
			return "-";
		return htmlspecialchars($this->Userid);
	}
	
	public function userid_url() {
		if (strlen($this->Userid) == 0)
			return "";
		return urlencode($this->Userid);
	}

	// Get password
		
	public function get_passwd() {
		$ret = mysql_query("select password from player where {$this->queryof()}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return  "";
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}

	// Get password for "display"
		
	public function disp_passwd() {
		return htmlspecialchars($this->get_passwd());
	}

	// Set password
		
	public function set_passwd($pw)  {
		$qpw = mysql_real_escape_string($pw);
		mysql_query("update player set password='$qpw' where {$this->queryof()}");
	}
	
	// Display link to send email
	
	public function display_email() {
		if (strlen($this->Email) == 0)
			return "-";
		return "<a href=\"sendmail.php?{$this->urlof()}\" target=\"_blank\">Send email</a>";
	}
	
	// Display email address
	
	public function display_email_link() {
		if (strlen($this->Email) == 0)
			return "";
		$m = htmlspecialchars($this->Email);
		return "<a href=\"mailto:$m\">$m</a>";
	}
		
	public function display_email_nolink() {
		return htmlspecialchars($this->Email);
	}

	public function display_phone($lc = false) {
		$ret = htmlspecialchars($this->Phone);
		if ($lc && strlen($ret) != 0 && strlen($this->Latestcall) != 0)
			$ret .= " not after " . $this->Latestcall;
		return  $ret;
	}
	
	public function display_notes() {
		return htmlspecialchars($this->Notes);
	}
	// Identify player as hidden item in a form
		
	public function save_hidden($prefix = "") {
		$f = $this->First;
		$l = $this->Last;
		return "<input type=\"hidden\" name=\"${prefix}f\" value=\"$f\"><input type=\"hidden\" name=\"${prefix}l\" value=\"$l\">";
	}

	// Display club as a selection
		
	public function clubopt() {
		$clubs = listclubs();
		print "<select name=\"club\">\n";
		foreach ($clubs as $club) {
			$code = $club->Code;
			$name = $club->Name;
			if ($code == $this->Club->Code)
				print "<option value=\"$code\" selected>$name</option>\n";
			else
				print "<option value=\"$code\">$name</option>\n";
		}
		print "</select>\n";
	}

	// Display rank as a selection
		
	public function rankopt($suff="") {
		$this->Rank->rankopt($suff);
	}

	// Display admin priv as a selection
		
	public function adminopt() {
		print "<select name=\"admin\">\n";
		$poss = array('N', 'A', 'SA');
		foreach ($poss as $pa) {
			if ($this->Admin == $pa)
				print "<option selected>$pa</option>\n";
			else
				print "<option>$pa</option>\n";
		}
		print "</select>\n";	
	}
	
	public function latestopt() {
		print "<select name=\"latesttime\">\n";
		if  (strlen($this->Latestcall) == 0)
			print "<option selected>None</option>\n";
		else
			print "<option>None</option>\n";
		for ($t = 19; $t < 24; $t++)  {
			$v = $t . ':00';
			if ($this->Latestcall == $v)
				print "<option selected>$v</option>\n";
			else
				print "<option>$v</option>\n";
			$v = $t . ':30';
			if ($this->Latestcall == $v)
				print "<option selected>$v</option>\n";
			else
				print "<option>$v</option>\n";
		}
		print "</select>\n";
	}

	// Add player record to database
		
	public function create() {
		$qfirst = mysql_real_escape_string($this->First);
		$qlast = mysql_real_escape_string($this->Last);
		$qclub = mysql_real_escape_string($this->Club->Code);
		$quser = mysql_real_escape_string($this->Userid);
		$qadmin = mysql_real_escape_string($this->Admin);
		$qemail = mysql_real_escape_string($this->Email);
		$qphone = mysql_real_escape_string($this->Phone);
		$qkgs = mysql_real_escape_string($this->KGS);
		$qigs = mysql_real_escape_string($this->IGS);
		$qnotes = mysql_real_escape_string($this->Notes);
		$qcall = mysql_real_escape_string($this->Latestcall);
		$qokemail = $this->OKemail? 1: 0;
		$qtrivia = $this->Trivia? 1: 0;
		$qbgamemb = $this->BGAmemb? 1: 0;
		$r = $this->Rank->Rankvalue;
		mysql_query("insert into player (first,last,rank,club,user,kgs,igs,email,okmail,trivia,phone,admin,bgamemb,ildiv,notes,latestcall) values ('$qfirst','$qlast',$r,'$qclub','$quser','$qkgs','$qigs','$qemail',$qokemail,$qtrivia,'$qphone','$qadmin',$qbgamemb,{$this->ILdiv},'$qnotes','$qcall')");
	}

	// Update player record of name
		
	public function updatename($newp) {
		$qfirst = mysql_real_escape_string($newp->First);
		$qlast = mysql_real_escape_string($newp->Last);
		mysql_query("update player set first='$qfirst',last='$qlast' where {$this->queryof()}");
		// Update any club which this player is the contact name for
		mysql_query("update club set contactfirst='$qfirst',contactlast='$qlast' where {$this->queryof('contact')}");
		// Update any team which this player is the captain of
		mysql_query("update team set captfirst='$qfirst',captlast='$qlast' where {$this->queryof('capt')}");
		// Likewise any team memberships
		mysql_query("update teammemb set tmfirst='$qfirst',tmlast='$qlast' where {$this->queryof('tm')}");
		// Likewise any historic team memberships
		mysql_query("update histteammemb set tmfirst='$qfirst',tmlast='$qlast' where {$this->queryof('tm')}");
		// Any games as White
		mysql_query("update game set wfirst='$qfirst',wlast='$qlast' where {$this->queryof('w')}");
		// And as black
		mysql_query("update game set bfirst='$qfirst',blast='$qlast' where {$this->queryof('b')}");
		$this->First = $newp->First;
		$this->Last = $newp->Last;
	}
	
	// Update player record
	
	public function update() {
		$qclub = mysql_real_escape_string($this->Club->Code);
		$quser = mysql_real_escape_string($this->Userid);
		$qadmin = mysql_real_escape_string($this->Admin);
		$qemail = mysql_real_escape_string($this->Email);
		$qokemail = $this->OKemail? 1: 0;
		$qtrivia = $this->Trivia? 1: 0;
		$qphone = mysql_real_escape_string($this->Phone);
		$qkgs = mysql_real_escape_string($this->KGS);
		$qigs = mysql_real_escape_string($this->IGS);
		$qnotes = mysql_real_escape_string($this->Notes);
		$qcall = mysql_real_escape_string($this->Latestcall);
		$r = $this->Rank->Rankvalue;
		$qbgamemb = $this->BGAmemb? 1: 0;
		mysql_query("update player set club='$qclub',user='$quser',admin='$qadmin',email='$qemail',okmail=$qokemail,trivia=$qtrivia,phone='$qphone',kgs='$qkgs',igs='$qigs',rank=$r,bgamemb=$qbgamemb,ildiv={$this->ILdiv},notes='$qnotes',latestcall='$qcall' where {$this->queryof()}");
		// Fix rank in teams that this player is a member of
		mysql_query("update teammemb set rank=$r where {$this->queryof('tm')}");
		// Fix rank in unplayed games where this player is black
		mysql_query("update game set brank=$r where result='N' and {$this->queryof('b')}");
		// Ditto for where this player is white
		mysql_query("update game set wrank=$r where result='N' and {$this->queryof('w')}");
	}

	//  Paid relates to individual league
		
	public function setpaid($v = true) {
		$vv = $v? 1: 0;
		mysql_query("update player set ilpaid=$vv where {$this->queryof()}");
	}
	
	public function updrank($r) {
		$this->Rank->Rankvalue = $r;
		mysql_query("update player set rank=$r where {$this->queryof()}");
		// Fix rank in teams that this player is a member of
		mysql_query("update teammemb set rank=$r where {$this->queryof('tm')}");
		// Fix rank in unplayed games where this player is black
		mysql_query("update game set brank=$r where result='N' and {$this->queryof('b')}");
		// Ditto for where this player is white
		mysql_query("update game set wrank=$r where result='N' and {$this->queryof('w')}");
	}	

	// MySQL juggling to get Played/Won/Drawn/Lost	
	private function get_grec($query) {
		$ret = mysql_query("select count(*) from game where $query");
		if (!$ret)
			throw new PlayerException(mysql_error());
		if  (mysql_num_rows($ret) == 0)
			return  0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	// Get Played/Won/Drawn/Lost
	private function get_grecs($l)  {
		if  ($this->Gotrecs == $l)
			return;
		$this->Gotrecs = $l;
		$lg = $l == 'N'? '': "league='$l' and ";
		// Get SQL to do all the work
		$qw = $this->queryof('w');
		$qb = $this->queryof('b');
		$rw = "result='W'";
		$rb = "result='B'";
		$this->Played = $this->get_grec("result!='N' and ($qw or $qb)");
		$this->Won = $this->get_grec("($qw and $rw) or ($qb and $rb)");
		$this->Drawn = $this->get_grec("result='J' and ($qw or $qb)");
		$this->Lost = $this->get_grec("($qw and $rb) or ($qb and $rw)");
		$this->Playeds = $this->get_grec("${lg}current=1 and result!='N' and ($qw or $qb)");
		$this->Wons = $this->get_grec("${lg}current=1 and (($qw and $rw) or ($qb and $rb))");
		$this->Drawns = $this->get_grec("${lg}current=1 and result='J' and ($qw or $qb)");
		$this->Losts = $this->get_grec("${lg}current=1 and (($qw and $rb) or ($qb and $rw))");
	}
	
	public function won_games($curr=false, $l='N') {
		$this->get_grecs($l);
		return  $curr? $this->Wons: $this->Won;
	}
	
	public function lost_games($curr=false, $l='N') {
		$this->get_grecs($l);
		return  $curr? $this->Losts: $this->Lost;
	}

	public function drawn_games($curr=false, $l='N') {
		$this->get_grecs($l);
		return  $curr? $this->Drawns: $this->Drawn;
	}
	
	public function played_games($curr=false, $l='N') {
		$this->get_grecs($l);
		return  $curr? $this->Playeds: $this->Played;
	}
	
	// For getting previous season statistics in various leagues usually individual
	
	private function histgrec($si, $l, $q) {
		return $this->get_grec("seasind=$si and league='$l' and $q");
	}
	
	public function histwon($si, $l = 'I') {
		return $this->histgrec($si, $l, "((result='W' and {$this->queryof('w')}) or (result='B' and {$this->queryof('b')}))");
	}
	
	public function histlost($si, $l = 'I') {
		return $this->histgrec($si, $l, "((result='B' and {$this->queryof('w')}) or (result='W' and {$this->queryof('b')}))");
	}
	
	public function histdrawn($si, $l = 'I') {
		return $this->histgrec($si, $l, "result='J' and (({$this->queryof('w')}) or ({$this->queryof('b')}))");
	}
	
	public function histplayed($si, $l = 'I') {
		return $this->histgrec($si, $l, "result!='N' and (({$this->queryof('w')}) or ({$this->queryof('b')}))");
	}
	
	public function get_scores($p = Null, $si = 0, $lg = 'I') {
		if  ($si == 0)  {		//  Current season
			$pl = $this->played_games(true, $lg);
			$w = $this->won_games(true, $lg);
			$d = $this->drawn_games(true, $lg);
			$l = $this->lost_games(true, $lg);
		}
		else  {
			$pl = $this->histplayed($si, $lg);
			$w = $this->histwon($si, $lg);
			$d = $this->histdrawn($si, $lg);
			$l = $this->histlost($si, $lg);
		}
		if ($p)
			$this->Sortrank = $pl * $p->Played + $w * $p->Won + $d * $p->Drawn + $l * $p->Lost;
	}
	
	// Count teams this player is a member of
	
	public function count_teams() {
		$ret = mysql_query("select count(*) from teammemb where {$this->queryof('tm')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}
	
	// Count historic teams this player is a member of
	
	public function count_hist_teams() {
		$ret = mysql_query("select count(*) from histteammemb where {$this->queryof('tm')}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return 0;
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}	

	private function getcount($seasind, $q) {
		$ret = mysql_query("select count(*) from game where league='I' and seasind=$seasind and $q");
		if (!$ret || mysql_num_rows($ret) == 0)
			return  0;
		$row = mysql_fetch_array($ret);
		return $row[0];
	}
	
	public function record_against($opp, $seasind = 0) {
		$res = new itrecord();
		if ($this->is_same($opp))
			$res->Isself = true;
		else  {
			$tpb = $this->queryof('b');
			$tpw = $this->queryof('w');
			$opb = $opp->queryof('b');
			$opw = $opp->queryof('w');
			$res->Won = $this->getcount($seasind, "((result='B' and $tpb and $opw) or (result='W' and $tpw and $opb))");
			$res->Drawn = $this->getcount($seasind, "result='J' and (($tpb and $opw) or ($tpw and $opb))");
			$res->Lost = $this->getcount($seasind, "((result='W' and $tpb and $opw) or (result='B' and $tpw and $opb))");
		}
		return $res;
	}
}

// List all players in specified order

function list_players($order = "last,first,rank desc") {
	$ret = mysql_query("select first,last from player order by $order");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_assoc($ret))
			array_push($result, new player($row['first'], $row['last']));
	}
	return $result;
}

function list_admins() {
	$ret = mysql_query("select first,last from player where admin!='N'");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_assoc($ret))
			array_push($result, new player($row['first'], $row['last']));
	}
	foreach ($result as $p)
		$p->fetchdets();
	return $result;
}

// Get a list of initials

function list_player_initials() {
	$ret = mysql_query("select last from player order by last");
	$result = array();
	if  ($ret)  {
		$li = "none";
		while ($row = mysql_fetch_array($ret)) {
			$ni = strtoupper(substr($row[0], 0, 1));
			if ($ni != $li) {
				array_push($result, $ni);
				$li = $ni;
			}
		}
	}
	return $result;
}

// List of all ranks people are

function list_player_ranks() {
	$ret = mysql_query("select rank from player group by rank order by rank desc");
	$result = array();
	if  ($ret)  {
		while ($row = mysql_fetch_array($ret)) {
			array_push($result, $row[0]);
		}
	}
	return $result;
}

function max_ildivision() {
	$result = 1;
	$ret = mysql_query("select max(ildiv) from player");
	if ($ret && mysql_num_rows($ret) > 0) {
		$row = mysql_fetch_array($ret);
		if ($row[0] > $result)
			$result = $row[0];
	}
	$ret = mysql_query("select max(divnum) from game where league='I'");
	if ($ret && mysql_num_rows($ret) > 0) {
		$row = mysql_fetch_array($ret);
		if ($row[0] > $result)
			$result = $row[0];
	}
	return $result;	
}

function list_players_ildiv($div, $order = "last,first,rank desc") {
	$ret = mysql_query("select first,last from player where ildiv=$div order by $order");
	$result = array();
	if ($ret) {
		while ($row = mysql_fetch_assoc($ret))
			array_push($result, new player($row['first'], $row['last']));
	}
	return $result;
}

function list_hist_players_ildiv($div, $seas) {
	$resultk = array();
	$ret = mysql_query("select wfirst,wlast,bfirst,blast from game where league='I' and seasind={$seas->Ind} and divnum=$div");
	if ($ret)  {
		while ($row = mysql_fetch_assoc($ret))  {
			$f = $row['wfirst'];
			$l = $row['wlast'];
			$name = "$f $l";
			if (!array_key_exists($name, $resultk))
				$resultk[$name] = new Player($f, $l);
			$f = $row['bfirst'];
			$l = $row['blast'];
			$name = "$f $l";
			if (!array_key_exists($name, $resultk))
				$resultk[$name] = new Player($f, $l);
		}
	}
	return array_values($resultk);
}

function ilscore_compare($pla, $plb) {
	// Decide ordering when compiling PWDL then fall back on name order.
	if ($pla->Sortrank != $plb->Sortrank)
		return $pla->Sortrank > $plb->Sortrank? -1: 1;
	if ($pla->Rank->Rankvalue != $plb->Rank->Rankvalue)
		return $plb->Rank->Rankvalue - $pla->Rank->Rankvalue;
	if ($pla->Last != $plb->Last)
		return strcasecmp($pla->Last, $plb->Last);
	return strcasecmp($pla->First, $plb->First);
}
	
?>
