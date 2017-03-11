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

// Run the kgsfetchsgf program to get the SGF file for a game

function kgsfetchsgf($g) {

	if  ($g->Result == 'N')
		throw new GameException("Game is not played yet");

	$wkgs = $g->Wplayer->KGS;
	$bkgs = $g->Bplayer->KGS;
	
	if  (strlen($wkgs) == 0)
		throw new GameException("No KGS name for white player");
	if  (strlen($bkgs) == 0)
		throw new GameException("No KGS name for black player");
	
	$dat = $g->Date->queryof();
	$res = $g->Resultdet;
	
	//  OK do the biz
	
	$prog = $_SERVER["DOCUMENT_ROOT"] . '/league/kgsfetchsgf.pl';
	$fh = popen("$prog $wkgs $bkgs $dat $res", "r");
	if (!$fh)
		throw new GameException("Unable to run kgsfetch");
		
	//  Actually read the thing
	
	$sgfdata = "";
	while ($part = fread($fh, 200))
		$sgfdata .= $part;
		
	//  Get code and diagnose problems
	
	$code = pclose($fh);
	if ($code != 0)  {
		switch ($code) {
		default:
			throw new GameException("I cannot tell why code was $code (prog $prog)");
		case 10:
			throw new GameException("Could not find games on {$g->Date->display()}");
		case 11:
			throw new GameException("Confused by which game was meant");
		case 12:
			throw new GameException("Found some games but they did not match result");
		case 13:
			throw new GameException("Unable to fetch game SGF");
		}
	}
	
	if  (strlen($sgfdata) == 0)
		throw new GameException("KGS read gave zero length SGF");
	
	return $sgfdata;
} 
?>
