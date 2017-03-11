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

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/matchdate.php';
include 'php/rank.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "External Matches";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>External Matches</h1>
<?php
class Extmatch {
	public $Name;
	public $Description;
	public $Date;
	
	public function __construct() {
		$this->Name = "";
		$this->Description = "";
		$this->Date = new Matchdate();
	}
	
	public function fromrow($row) {
		$this->Name = $row["name"];
		$this->Description = $row["description"];
		$this->Date->enctime($row["matchdate"]);
	}
}

class Extteam {
	public $Mname;
	public $First;
	public $Last;
	public $KGSname;
	public $Rank;
	
	public function __construct($mn) {
		$this->Mname = $mn;
		$this->First = "";
		$this->Last = "";
		$this->KGSname = "";
		$this->Rank = new Rank();
	}
	
	public function fromrow($row) {
		$this->First = $row['first'];
		$this->Last = $row['last'];
		$this->KGSname = $row['kgs'];
		$this->Rank = new Rank($row['rank']);
	}
}

$ret = mysql_query('select name,description,matchdate from extmatch order by matchdate,name');
$matches = array();

if ($ret)  {
	while  ($row = mysql_fetch_assoc($ret))  {
		$m = new Extmatch();
		$m->fromrow($row);
		array_push($matches, $m);
	}
}

if (count($matches) == 0)  {
	print <<<EOT
<p>
No matches are currently set up.</p>

EOT;
}
else  {
	foreach ($matches as $mtch) {
		$mname = $mtch->Name;
		print <<<EOT
<h2>$mname - {$mtch->Description}</h2>
<p>Team for this match on {$mtch->Date->display()} is as follows:
</p>
<table cellpadding="2" cellspacing="3" border="0">
<tr><th>Player</th><th>KGS name</th><th>Rank</th></tr>

EOT;
		$ret = mysql_query("select first,last,kgs,rank from extteam,player where mname='$mname' and player.first=extteam.efirst and player.last=extteam.elast order by rank desc,first,last");
		$players = array();
		if ($ret)  {
			while ($row = mysql_fetch_assoc($ret))  {
				$p = new Extteam($mname);
				$p->fromrow($row);
				array_push($players, $p);			
			}
		}
		foreach ($players as $p)  {
			print <<<EOT
<tr>
<td>{$p->First} {$p->Last}</td>
<td>{$p->KGSname}</td>
<td>{$p->Rank->display()}</td>
</tr>

EOT;
		}
		$np = count($players);
		print <<<EOT
</table>
<p>$np players in total.</p>

EOT;
	}
}
?>
</div>
</div>
</body>
</html>
