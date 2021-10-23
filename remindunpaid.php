<?php
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';

$Connection = opendatabase(false, false);

$ret = $Connection->query("SELECT name FROM team WHERE paid=0");
if ($ret && $ret->num_rows > 0)  {
	while ($row = $ret->fetch_array())  {
		$team = new Team($row[0]);
		$team->fetchdets();
		$dest = $team->Captain->Email;
		$fh = popen("mail -s 'BGA Online team subs update reminder' $dest", "w");
		$mess = <<<EOT
Dear {$team->display_captain()}:

Please can we respectfully remind you that your BGA Online league
team fee has not been recorded as paid.

This should be 10 GBP per team plus an additional 5 GBP for
each non BGA member.

If you have paid and this has not been recorded please let us know.

Thank you.

The Online League Management.

EOT;
		fwrite($fh, $mess);
		pclose($fh);
	}
}
?>
