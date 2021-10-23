<?php
//   Copyright 2009-2021 John Collins

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

// Version of loadkgs for where we "believe" the existing match date
// and just want to get the SGF file for an existing game.

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/kgsfetchsgf.php';

$Connection = opendatabase(true);

$g = new Game();
try  {
	$g->fromget();
	$g->fetchdets();
}
catch (GameException $e) {
	wrongentry($e->getMessage());
}

try  {
	$g->set_sgf(kgsfetchsgf($g));
}
catch (GameException $e)  {
	game_not_found($e->getMessage());
}
lg_html_header("Add game SGF complete");
lg_html_nav();
print <<<EOT
<h1>Add Game SGF complete</h1>
<p>Finished adding SGF for Game between
<b>{$g->Wplayer->display_name()}</b>
({$g->Wplayer->display_rank()}) of
{$g->Wteam->display_name()} as White and
<b>{$g->Bplayer->display_name()}</b>
({$g->Bplayer->display_rank()}) of
{$g->Bteam->display_name()} as Black
on {$g->date_played()}.
</p>

EOT;
lg_html_footer();
?>
