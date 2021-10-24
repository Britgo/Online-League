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

$Connection = opendatabase();
lg_html_header("BGA Online league");
lg_html_nav();
print <<<EOT
<h1>BGA Online Leagues</h1>
<img src="images/gogod_shield.medium.jpg" width="359" height="400" alt="Shield picture" align="left" border="0" hspace="20" vspace="5">
<p>Welcome to the BGA online league site. Please use the menus on the left to select a destination.
In particulare, to find out more about the league,please <a href="info.php" title="Give more information">go here</a>,
A full description of playing games is to be found under <a href="playing.php" title="Read description of rules and instructions for playing">rules</a>.
To see the current league standings, please <a href="league.php" title="Show currennt standings">go here</a>.</p>

EOT;
lg_html_footer(true);
?>
