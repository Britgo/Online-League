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
include 'php/params.php';
$p = $_POST["p"];
if (strlen($p) == 0) {
    include 'php/wrongentry.php';
    exit(0);
}
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Adjustment of Parameters Complete";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Adjusting parameters Complete</h1>
<p>Finished adjusting parameters.</p>
<p><a href="league.php" title="View the league table to see what the changes did">Click here</a>
to see what the league looks like now.</p>
</div>
</div>
</body>
</html>
