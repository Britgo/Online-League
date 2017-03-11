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
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/matchdate.php';
include 'php/season.php';
include 'php/news.php';

$Sname = $_POST["seasname"];
if (strlen($Sname) == 0)  {
	include 'php/wrongentry.php';
	exit(0);
}

// Create the Season
// Set the name and dates

$Seas = new Season(0, 'I');
$Seas->Name = $Sname;
$earliest = new Matchdate();
$latest = new Matchdate();
$ret = mysql_query("select matchdate from game where league='I' order by matchdate limit 1");
if ($ret && mysql_num_rows($ret) > 0)  {
	$row = mysql_fetch_array($ret);
	if ($row)
			$earliest->enctime($row[0]);	
}
$ret = mysql_query("select matchdate from game order by matchdate desc limit 1");
if ($ret && mysql_num_rows($ret) > 0)  {
	$row = mysql_fetch_array($ret);
	if ($row)
		$latest->enctime($row[0]);	
}
$Seas->Startdate = $earliest;
$Seas->Enddate = $latest;
$Seasind = $Seas->create();

//  OK so all we have to do is convert individual league games to historic ones
//  by turning off "current" and setting the season number

$ret = mysql_query("update game set current=0,seasind=$Seasind where current!=0 and league='I'");
if (!$ret)  {
	$mess = mysql_error();
	include 'php/dataerror.php';
	exit(0);
}
$Ngames = mysql_affected_rows();

// I think that just about does it. Create a news item unless we didn't do anything.

if  ($Ngames > 0)  {
	$nws = new News('ADMINS', "Individual League season now closed and archived as $Sname.", true, "ileague.php");
	$nws->addnews();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "End of individual league season complete";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>End of individual league season complete</h1>
<?php
$Sname = htmlspecialchars($Sname);
print <<<EOT
<p>Cleared and archived the individual league season as $Sname.</p>
<p>Archived a total of $Ngames games.</p>

EOT;
?>
</div>
</div>
</body>
</html>
