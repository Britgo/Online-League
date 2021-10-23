<?php

function mail_player($board, $pl, $pt, $pc, $col, $opp, $ot, $hcp)
{
	if (strlen($pl->Email) == 0)
		return "email for {$pl->display_name(false)} is not known";
	if (!($pl->OKemail || $pl->is_same($pc)))
		return "{$pl->display_name(false)} does not want email";

	$oppemail = $opp->Email;
	if (strlen($oppemail) != 0)
		$rt = "REPLYTO='$oppemail' ";
	else
		$rt = "REPLYTO='DO_NOT_REPLY_TO_THIS_ADDRESS@britgo.org' ";

	$fhh = popen("{$rt}mail -s 'Go League match assignment' {$pl->Email}", "w");
	$oppname = $opp->display_name(false);
	$mess = <<<EOT
Dear {$pl->display_name(false)}

Please note that you have been assigned to board $board in the online league match
playing for {$pt->display_name()} against {$ot->display_name()}.

Your opponent is $oppname {$opp->display_rank()}.

You are playing as $col.

EOT;
	fwrite($fhh, $mess);
	$onl = $opp->display_online();
	if ($onl == '-')
		$mess = <<<EOT
Sorry, but we have no record of an online name for $oppname.


EOT;
	else
		$mess = <<<EOT
The online name for $oppname is $onl.


EOT;
	fwrite($fhh, $mess);

	if ($hcp)
		fwrite($fhh, "\nThis game is played with $hcp\n");
	if (strlen($oppemail) != 0)
		fwrite($fhh, "\nThe email address for {$opp->display_name(false)} is {$opp->display_email_nolink()}\n");
	if (strlen($opp->Phone) != 0)
		fwrite($fhh, "You can also reach him/her by phone - {$opp->display_phone(true)}.\n");
	if (strlen($opp->Notes) != 0)
		fwrite($fhh, "Notes for player: {$opp->Notes}\n");
	fwrite($fhh, "\nIf you have any questions please contact your team captain {$pc->display_name(false)} email {$pc->display_email_nolink()}\n");
	pclose($fhh);
	return "";
}

// Deal with allocation of team to matches
// parameter is for admin so that messages can be rephrased

function mail_allocated($mtch, $pars, $byadmin = false) {

	// We shouldn't get here without it being allocated but return if we are

	if (!$mtch->is_allocated())
			return;

	// Get hold of the first and second teams and their captains

	$ht = $mtch->Hteam;
	$at = $mtch->Ateam;
	$hc = $ht->Captain;
	$ac = $at->Captain;

	$games = $mtch->Games;
	$mailgames = array();		//  Games it is OK to mail players about

	if ($byadmin) {
		$suam = "amended";
		$stm = "Amended";
	}
	else {
		$suam = "set up";
		$stm = "Completed";
	}

	$fh = popen("mail -s 'Go League match $suam' online-league@britgo.org {$hc->Email} {$ac->Email}", "w");
	$mess = <<<EOT
$stm allocation of players to match in division {$mtch->Division} for {$mtch->Date->display_month()} between
{$ht->display_name()} ({$ht->display_description()}) and {$at->display_name()} ({$at->display_description()}).

Allocation is:
EOT;
	fwrite($fh, "$mess\n");

	$board = 1;
	foreach ($games as $g) {
		$wp = $g->Wplayer;
		$bp = $g->Bplayer;
		$wt = $g->Wteam;
		$bt = $g->Bteam;
		if ($wt->is_same($ht)) {
			$hp = $wp;
			$ap = $bp;
			$hcol = "White";
			$acol = "Black";
		}
		else {
			$hp = $bp;
			$ap = $wp;
			$acol = "White";
			$hcol = "Black";
		}
		$mess = <<<EOT
Board $board:
	White: {$wp->display_name(false)} {$wp->display_rank()} of {$wt->display_name()}
	Black: {$bp->display_name(false)} {$bp->display_rank()} of {$bt->display_name()}
EOT;
		fwrite($fh, "$mess\n");
		if ($g->Result != 'N')
			fwrite($fh, "This game has already been played.\n");
		else {
			$hcp = hcp_message($g, $pars);
			if ($hcp)
				fwrite($fh, "Game is played with $hcp\n");
			$hreason = mail_player($board, $hp, $ht, $hc, $hcol, $ap, $at, $hcp);
			$areason = mail_player($board, $ap, $at, $ac, $acol, $hp, $ht, $hcp);
			if (strlen($hreason) != 0)  {
				$mess = <<<EOT
	{$ht->display_captain()} please contact as $hreason

EOT;
				fwrite($fh, $mess);
			}
			if (strlen($areason) != 0)  {
				$mess = <<<EOT
	{$at->display_captain()} please contact as $areason

EOT;
				fwrite($fh, $mess);
			}
		}
		$board++;
	}
	$mess = <<<EOT

Team Captains are:

For {$ht->display_name()}: {$ht->display_captain()}, {$hc->display_email_nolink()}
For {$at->display_name()}: {$at->display_captain()}, {$ac->display_email_nolink()}

EOT;
		fwrite($fh, "$mess");
	if (strlen($hc->Phone) != 0)
		fwrite($fh, "You can reach {$ht->display_captain()} on {$hc->display_phone(true)}.\n");
	if (strlen($ac->Phone) != 0)
		fwrite($fh, "You can reach {$at->display_captain()} on {$ac->display_phone(true)}.\n");
	pclose($fh);
}
?>
