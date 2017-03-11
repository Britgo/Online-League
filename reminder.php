<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';
include 'php/params.php';
include 'php/hcp_message.php';

// Remember about player we can't email to bleat at team captain about instead

class aboutplayer {
	public $player;		// Player we are talking about
	public $opp;			// His/her opponent
	public $match;			// Match info
	public $game;			// Game info
	public $reason;		// Reason not emailed
	
	public function __construct($p, $o, $m, $g, $r) {
		$this->player = $p;
		$this->opp = $o;
		$this->match = $m;
		$this->game = $g;
		$this->reason = $r;
	}
}

// Stuff to remember to tell team captains about

class tcrems {

	public $Capt;			// The poor sucker
	public $Unalloc;		// Unallocated matches
	public $Oppunalloc;	// Opponents unallocated
	public $Noemail;		// No emails to players
	
	public function __construct($c) {
		$this->Capt = $c;
		$this->Unalloc = array();
		$this->Oppunalloc = array();
		$this->Noemail = array();
	}
	
	public function adduamatch($m) {
		array_push($this->Unalloc, $m);
	}
	
	public function addoppuamatch($m) {
		array_push($this->Oppunalloc, $m);
	}

	public function addgame($p) {
		array_push($this->Noemail, $p);
	}
		
	public function othercapt($m) {
		if ($this->Capt->is_same($m->Hteam->Captain))
			return $m->Ateam->Captain;
		return $m->Hteam->Captain;
	}
}

// Allocate a new tcrem structure if needed

function gettcrem($tc) {
	global $Captains;
	$cn = $tc->display_name(false);
	if  (!array_key_exists($cn, $Captains))
		$Captains[$cn] = new tcrems($tc);
	return $Captains[$cn];
}

// Generate a mail message to the player about impending match
// or thinking of a reason why not

function mailplayer($play, $opp, $col, $mt, $ot, $m, $g)
{
	global $pars;
	if  (strlen($play->Email) == 0)
		return new aboutplayer($play, $opp, $m, $g, "has given no email address");
	if  (!$play->OKemail)
		return new aboutplayer($play, $opp, $m, $g, "has not agreed to auto-email");
	$oppname = $opp->display_name(false);
	$dest = $play->Email;
	$fh = popen("mail -s 'Online league match reminder' $dest", "w");
	$mess = <<<EOT
Dear {$play->display_name(false)}

PLEASE NOTE that this is an automatically-generated message. Please DO NOT
reply to the apparent sender - thank you!

Please can we remind you that your are due to play in the online league match
playing for {$mt->display_name()} against {$ot->display_name()}.

Your opponent is $oppname {$opp->display_rank()}.

You are playing as $col.

EOT;
	fwrite($fh, $mess);
	
	$onl = $opp->display_online();
	if ($onl == '-')
		$mess = <<<EOT
Sorry, but we have no record of an online name for $oppname.


EOT;
	else
		$mess = <<<EOT
The online name for $oppname is $onl.


EOT;
	fwrite($fh, $mess);
	
	$hcp = hcp_message($g, $pars);
	if ($hcp)
		fwrite($fh, "\nPlease note this game is played with $hcp\n");
	if (strlen($opp->Email) != 0)
		$mess = <<<EOT

$oppname has an email address of {$opp->Email}.

EOT;
	else
		$mess = <<<EOT

Sorry we have no email address for $oppname.

EOT;
	fwrite($fh, $mess);
	$phone = $opp->display_phone(true);
	if  (strlen($phone) != 0)  {
		$mess = <<<EOT

You can reach $oppname on the phone at $phone.

EOT;
		fwrite($fh, $mess);
	}
	$nts = $opp->Notes;
	if (strlen($nts) != 0) {
		$mess = <<<EOT

$oppname has provided the following notes: $nts

EOT;
		fwrite($fh, $mess);
	}
	$capt = $mt->Captain;
	if (!$capt->is_same($play))  {
		$mess = <<<EOT

If you have any questions, please contact your team captain, {$capt->display_name(false)},
whose email address is {$capt->Email}.

EOT;
		fwrite($fh, $mess);
	}
	pclose($fh);
	return false;
}

// Get parameters to include handicap info

$pars = new Params();
$pars->fetchvalues();

// Array of team captains to bleat to

$Captains = array();

$ret = mysql_query("select ind from lgmatch where result!='H' and result!='A' and matchdate < date_add(current_date(),interval 30 day) order by divnum,matchdate,result");
if ($ret && mysql_num_rows($ret) > 0)  {
	while ($row = mysql_fetch_array($ret))  {
		$ind = $row[0];
		$mtch = new Match($ind);
		$mtch->fetchdets();
		try {
			$mtch->fetchteams();
			$mtch->fetchgames();
		}
		catch (MatchException $e) {
			continue;
		}
		$ht = $mtch->Hteam;
		$at = $mtch->Ateam;
		$hc = $ht->Captain;
		$ac = $at->Captain;
		
		if ($mtch->is_allocated())  {
			
			// Match is allocated, find each unplayed game

			foreach ($mtch->Games as $g)  {
				if ($g->Result != 'N')
					continue;
				if ($g->Wteam->is_same($ht)) {
					$hp = $g->Wplayer;
					$ap = $g->Bplayer;
					$hcol = "White";
					$acol = "Black";
				}
				else  {
					$hp = $g->Bplayer;
					$ap = $g->Wplayer;
					$hcol = "Black";
					$acol = "White";
				}
				$r = mailplayer($hp, $ap, $hcol, $ht, $at, $mtch, $g);
				if  ($r)
					gettcrem($hc)->addgame($r);
				$r = mailplayer($ap, $hp, $acol, $at, $ht, $mtch, $g);
				if  ($r)
					gettcrem($ac)->addgame($r);
			}
		}
		else {
			if ($mtch->team_allocated($ht))
				gettcrem($hc)->addoppuamatch($mtch);
			else
				gettcrem($hc)->adduamatch($mtch);
			if ($mtch->team_allocated($at))
				gettcrem($ac)->addoppuamatch($mtch);
			else
				gettcrem($ac)->adduamatch($mtch);
		}
	}
}

foreach ($Captains as $capt)  {
	$dest = $capt->Capt->Email;
	$fh = popen("mail -s 'Go League match reminder' $dest", "w");
	$mess = <<<EOT

Dear {$capt->Capt->display_name(false)},

This is an auto-reminder from the BGA online league software.
Please do not reply to this!

EOT;
	fwrite($fh, $mess);

	if (count($capt->Unalloc) != 0)  {
		$mess = <<<EOT

Please can you complete the allocation of teams to the following matches.


EOT;
		fwrite($fh, $mess);
		foreach ($capt->Unalloc as $m) {
			$oth = $capt->othercapt($m);
			$mess = <<<EOT
Date: {$m->Date->display_month()}
Between: {$m->Hteam->display_name()} -v- {$m->Ateam->display_name()}
Other team captain: ({$oth->display_name(false)} {$oth->Email} {$oth->Phone})


EOT;
			fwrite($fh, $mess);
		}
	}
	if (count($capt->Oppunalloc) != 0)  {
		$mess = <<<EOT

Please can you chase the opposing team captain to complete his/her allocation
of teams to the following matches.


EOT;
		fwrite($fh, $mess);
		foreach ($capt->Oppunalloc as $m) {
			$oth = $capt->othercapt($m);
			$mess = <<<EOT
Date: {$m->Date->display_month()}
Between: {$m->Hteam->display_name()} -v- {$m->Ateam->display_name()}
Other team captain: ({$oth->display_name(false)} {$oth->Email} {$oth->Phone})


EOT;
			fwrite($fh, $mess);
		}
	}

	if (count($capt->Noemail) != 0)  {
		$mess = <<<EOT

Please can you encourage your team members to play the following games,
I couldn't email them directly for the reasons given.


EOT;
		fwrite($fh, $mess);
		foreach ($capt->Noemail as $noem) {
			$m = $noem->match;
			$pl = $noem->player;
			$opp = $noem->opp;
				
			$mess = <<<EOT
Date: {$m->Date->display_month()}
Match: {$m->Hteam->display_name()} -v- {$m->Ateam->display_name()}
Game: {$pl->display_name(false)} -v- {$opp->display_name(false)}
Reason: Player {$noem->reason}


EOT;
			fwrite($fh, $mess);
		}
	}
		
	$mess = <<<EOT

If you have allocated teams/played matches listed above please enter them
on the website.

Thanks - league auto-reminder service.

EOT;
	fwrite($fh, $mess);
	pclose($fh);
}
?>
