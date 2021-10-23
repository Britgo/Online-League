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

ini_set("session.gc_maxlifetime", "604800");
$phpsessiondir = $_SERVER["DOCUMENT_ROOT"] . "/league/phpsessions";
if (is_dir($phpsessiondir))
	session_save_path($phpsessiondir);
session_set_cookie_params(604800);
session_start();

if (isset($_SESSION['user_id'])) {
	$userid = $_SESSION['user_id'];
	$username = $_SESSION['user_name'];
	$userpriv = $_SESSION['user_priv'];
	$logged_in = strlen($userid) != 0;
	$admin = $logged_in && ($userpriv == 'A' || $userpriv == 'SA');
}
else {
	$userid = "";
	$username = "";
	$userpriv = "N";
	$admin = false;
	$logged_in = false;
}

function num_unread_msgs() {
	global $userid;
	if (strlen($userid) == 0)
		return 0;
	$quser = $Connection->real_escape_string($userid);
	$ret = $Connection->query("SELECT COUNT(*) FROM message WHERE touser='$quser' and hasread=0");
	if (!$ret || $ret->num_rows == 0)
		return 0;
	$row = $ret->fetch_array();
	return $row[0] + 0;
}
?>
