<?php

//   Copyright 2009-2017 John Collins

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

// This include belongs to a separate system library routine in somewhere
// like /usr/local/lib/php

include 'credentials.php';

// First argument determines if we insist on being logged in.
// Second argument skips trying to log in (for when we're actually logging in from web page).

function  opendatabase($mustbeloggedin = false, $loginifposs = true)  {
	try  {
		$dbcred = getcredentials('league');
	}
	catch (Credentials_error $e)  {
		report_credentials_error($e->getMessage());
	}

	try  {
		$conn = new Connection($dbcred->Databasename, $dbcred->Username, $dbcred->Password);
		if ($loginifposs)  {
			$conn->get_cookie();
			$conn->get_login($mustbeloggedin);
		}
	}
	catch (Connection_error $e)  {
		report_connection_error($e->getMessage());
	}

	return  $conn;
}
?>
