<?php
//   Copyright 2011-2021 John Collins

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

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/matchdate.php';
include 'php/rank.php';

$Connection = opendatabase();
lg_html_header("External Matches");
lg_html_nav();

print <<<EOT
<h1>External Matches</h1>

EOT;

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

$ret = $Connection->query('SELECT name,description,matchdate FROM extmatch ORDER BY matchdate,name');
$matches = array();

if ($ret)  {
	while  ($row = $ret->fetch_assoc())  {
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
		$ret = $Connection->query("SELECT first,last,kgs,rank FROM extteam,player WHERE mname='$mname' AND player.first=extteam.efirst AND player.last=extteam.elast ORDER BY rank desc,first,last");
		$players = array();
		if ($ret)  {
			while ($row = $ret->fetch_assoc())  {
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
lg_html_footer();
?>
