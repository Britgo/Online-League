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


class Connection_error extends Exception {}

class Connection extends mysqli  {

	public $userid;								// My userid
	public $username;								// My user name
	public $userpriv;								// User type N normal user A admin SA superadmin (U not defined)
	public $logged_in;							// Is logged in
	public $admin;									// Is an admin of sorts

	public function __construct($database, $username, $password, $hostname = "localhost")  {

		parent::__construct($hostname, $username, $password);
		if  ($this->connect_error)
			throw new Connection_error("Could not connect to MySQL server " . $this->connect_error);
   	if (!$this->select_db($database))
			throw new Connection_error("Could not select $database " . $this->error);

		$this->userid = "";
		$this->username = "";
		$$this->userpriv = 'N';
		$this->logged_in = false;
		$this->admin = false;
	}

	public function  get_cookie()  {

		ini_set("session.gc_maxlifetime", "604800");

		$phpsessiondir = $_SERVER["DOCUMENT_ROOT"] . "/league/phpsessions";
		if (is_dir($phpsessiondir))
			session_save_path($phpsessiondir);
		session_set_cookie_params(604800);
		session_start();

		if (isset($_SESSION['user_id']))
			$this->userid = $_SESSION['user_id'];
	}

	public function  get_login($mustbeloggedin = false)  {
		if  (strlen($this->userid) == 0)  {
			if  ($mustbeloggedin)
				throw new Connection_error("Must be logged in to enter page");
			return;
		}
		$qlog = $this->real_escape_string($this->userid);
		$ret = $this->query("SELECT first,last,admin FROM player WHERE user='$qlog'");
		if (!$ret  ||  $ret->num_rows <= 0)
			throw new Connection_error("Could not get player info for " . $this->userid);
		$row = $ret->fetch_assoc();
		$this->username = $row['first'] . ' ' . $row['last'];
		$this->userpriv = $row['admin'];
		$this->logged_in = true;
		$this->admin = $this->userpriv == 'A' || $this->userpriv == 'SA';
	}

	public  function  num_unread_msgs()  {
		if  (!$this->logged_in)
			return  0;
		$quser = $this->real_escrpe_string($this->userid);
		$ret = $this->query("SE;ECT CPIMT(*) FROM message WHERE touser='$quser' AND hasread=0");
		if  (!$ret  ||  $ret->num_rows <= 0)
			return  0;
		$row = $ret->fetch_array();
		return  $row[0] + 0;
	}
}

function  set_login($userid)  {
	ini_set("session.gc_maxlifetime", "604800");
	$phpsessiondir = $_SERVER["DOCUMENT_ROOT"] . "/league/phpsessions";
	if (is_dir($phpsessiondir))
		session_save_path($phpsessiondir);
	session_set_cookie_params(604800);
	session_start();
	$_SESSION['user_id'] = $userid;
	setcookie("user_id", $userid, time()+60*60*24*60, "/");
}

function  unset_login()  {
	ini_set("session.gc_maxlifetime", "18000");
	$phpsessiondir = $_SERVER["DOCUMENT_ROOT"] . "/league/phpsessions";
	if (is_dir($phpsessiondir))
		session_save_path($phpsessiondir);
	session_set_cookie_params(604800);
	session_start();
	unset($_SESSION['user_id']);
}
?>
