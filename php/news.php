<?php
//   Copyright 2010 John Collins

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

class News {
	public $Date;
	public $User;
	public $Item;
	public $Rssable;
	public $Link;
	
	public function __construct($u = "", $it = "", $rss = false, $lnk = "") {
		$this->Date = new Matchdate();
		$this->User = $u;
		$this->Item = $it;
		$this->Rssable = $rss;
		$this->Link = $lnk;
	}
	
	public function fromrow($r) {
		$this->Date->fromtabrow($r, 'ndate');
		$this->User = $r['user'];
		$this->Item = $r['item'];
		$this->Link = $r['link'];
	}
	
	public function display_date() {
		return $this->Date->disp_abbrev();
	}
	
	public function display_user() {
		return htmlspecialchars($this->User);
	}
	
	public function display_item() {
		return htmlspecialchars($this->Item);
	}
	
	public function display_link() {
		$lnk = $this->Link;
		if (strlen($lnk) != 0)
			$lnk = " - <a href=\"$lnk\">link</a>";
		return $lnk;
	}#			
	
	public function addnews() {
		$qdate = $this->Date->queryof();
		$quser = mysql_real_escape_string($this->User);
		$qitem = mysql_real_escape_string($this->Item);
		$qlink = mysql_real_escape_string($this->Link);
		$qr = $this->Rssable? 1: 0;
		mysql_query("insert into news (ndate,user,item,rss,link) values ('$qdate','$quser','$qitem',$qr,'$qlink')");
	}
}
?>
