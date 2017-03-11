#! /usr/bin/perl

$playing = 1;
$paid = 0;

while ($ARGV[0] =~ /^-([APBpu]+)/) {
	my $lets = $1;
	while ($lets =~ /(.)/g)  {
		my $let = $1;
		if ($let eq 'A')  {
			$playing = 0;
		}
		elsif ($let eq 'P') {
			$playing = 1;
		}
		elsif ($let eq 'B') {
			$paid = 0;
		}
		elsif ($let eq 'p') {
			$paid = 1;
		}
		elsif ($let eq 'u') {
			$paid = -1;
		}
	}
}

@Conds = ('team.captfirst=player.first', 'team.captlast=player.last', 'length(player.email)>0');
push @Conds, 'team.playing!=0' if $playing;
if  ($paid != 0)  {
	if ($paid < 0) {
		push @Conds, 'team.paid=0';
	}
	else {
		push @Conds, 'team.paid!=0';
	}
}

use Config::INI::Reader;
$inicont = Config::INI::Reader->read_file('/etc/webdb-credentials');
$ldbc = $inicont->{league};
use DBD::mysql;
$Database = DBI->connect("DBI:mysql:$ldbc->{database}", $ldbc->{user}, $ldbc->{password}) or die "Cannot open DB";
$sfh = $Database->prepare("SELECT player.email FROM player,team WHERE " . join(' AND ', @Conds));
$sfh->execute;

while (@row = $sfh->fetchrow_array) {
	$Emails{$row[0]} = 1;
}

$sfh = $Database->prepare("select email from player where admin!='N' and length(email) > 0");
$sfh->execute;
while (@row = $sfh->fetchrow_array) {
	$Emails{$row[0]} = 1;
}

$tmpfile = "/tmp/splurge$$";

open(MOUT, ">$tmpfile");
while (<STDIN>) {
		$_ = "To: " . join(", ", keys %Emails) . "\n" if /^To:\s+/i;
	print MOUT "$_";
	last if /^\s*\r?\n?$/;
}

while (<STDIN>) {
	print MOUT $_;
}
close MOUT;
system("/usr/sbin/exim -t -oi <$tmpfile");
unlink $tmpfile;
