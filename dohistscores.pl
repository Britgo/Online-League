#! /usr/bin/perl

use Config::INI::Reader;
use DBD::mysql;

$inicont = Config::INI::Reader->read_file('/etc/webdb-credentials');
$ldbc = $inicont->{league};
$Database = DBI->connect("DBI:mysql:$ldbc->{database}", $ldbc->{user}, $ldbc->{password}) or die "Cannot open DB";

# Build up table of matches by ind

$sfh = $Database->prepare("SELECT seasind,hteam,ateam,hwins,awins,draws,result FROM histmatch");
$sfh->execute;

while (my @row = $sfh->fetchrow_array)  {
	my ($seasind,$hteam,$ateam,$hwins,$awins,$draws,$result) = @row;
	$seasind += 0;
	$totals{$hteam}->{$seasind}->{WONG} += $hwins;
	$totals{$hteam}->{$seasind}->{DRAWNG} += $draws;
	$totals{$hteam}->{$seasind}->{LOSTG} += $awins;
	$totals{$ateam}->{$seasind}->{WONG} += $awins;
	$totals{$ateam}->{$seasind}->{DRAWNG} += $draws;
	$totals{$ateam}->{$seasind}->{LOSTG} += $hwins;

	if ($result eq 'H')  {
		$totals{$hteam}->{$seasind}->{PLAYED}++;
		$totals{$hteam}->{$seasind}->{WONM}++;
		$totals{$ateam}->{$seasind}->{PLAYED}++;
		$totals{$ateam}->{$seasind}->{LOSTM}++;
	}
	elsif ($result eq 'A')  {
		$totals{$hteam}->{$seasind}->{PLAYED}++;
		$totals{$ateam}->{$seasind}->{WONM}++;
		$totals{$ateam}->{$seasind}->{PLAYED}++;
		$totals{$hteam}->{$seasind}->{LOSTM}++;
	}
	elsif ($result eq 'D') {
		$totals{$hteam}->{$seasind}->{PLAYED}++;
		$totals{$ateam}->{$seasind}->{PLAYED}++;
		$totals{$hteam}->{$seasind}->{DRAWNM}++;
		$totals{$ateam}->{$seasind}->{DRAWNM}++;
	}
}

for my $team (keys %totals)  {
	my $qteam = $Database->quote($team);
	my $tres = $totals{$team};
	for my $season (keys %$tres) {
		my $si = $tres->{$season};
		my $played = $si->{PLAYED} + 0;
		my $wonm = $si->{WONM} + 0;
		my $drawnm = $si->{DRAWNM} + 0;
		my $lostm = $si->{LOSTM} + 0;
		my $wong = $si->{WONG} + 0;
		my $drawng = $si->{DRAWNG} + 0;
		my $lostg = $si->{LOSTG} + 0;
		my $sfh = $Database->prepare("UPDATE histteam SET playedm=$played,wonm=$wonm,drawnm=$drawnm,lostm=$lostm,wong=$wong,drawng=$drawng,lostg=$lostg WHERE name=$qteam AND seasind=$season");
		$sfh->execute;
	} 
}
