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

$p = $_POST["p"];
if (strlen($p) == 0)
	wrongentry();

$Connection = opendatabase(true);

$pars = new Params();
$pars->fetchvalues();
$pars->Played = $_POST["p"] + 0.0;
$pars->Won = $_POST["w"] + 0.0;
$pars->Drawn = $_POST["d"] + 0.0;
$pars->Lost = $_POST["l"] + 0.0;
$pars->Forg = $_POST["f"] + 0.0;
$pars->Drawng = $_POST["j"] + 0.0;
$pars->Againstg = $_POST["a"] + 0.0;
$pars->Hdiv = $_POST["hdiv"] + 0;
$pars->Hreduct = $_POST["hred"] + 0;
$pars->Rankfuzz = $_POST["rfuzz"] + 0;
$pars->putvalues();

lg_html_header("Adjustment of parameters complete");
lg_html_nav();
print <<<EOT
<h1>Adjusting parameters Complete</h1>
<p>Finished adjusting parameters.</p>
<p><a href="league.php" title="View the league table to see what the changes did">Click here</a>
to see what the league looks like now.</p>

EOT;
lg_html_footer();
?>
