#! /usr/bin/perl

exit 0 if -e "/var/www/onlineleague/nopayreminder";

use Config::INI::Reader;
use DBD::mysql;
$inicont = Config::INI::Reader->read_file('/etc/webdb-credentials');
$ldbc = $inicont->{league};
$Database = DBI->connect("DBI:mysql:$ldbc->{database}", $ldbc->{user}, $ldbc->{password}) or die "Cannot open DB";

open(MAILOUT, "|REPLYTO=jmc\@toad.me.uk mail -s 'Online league payments' treasurer\@britgo.org jmc\@toad.me.uk") or die "Cannot open Mail";
select MAILOUT;

$sfh = $Database->prepare("SELECT league,descr1,descr2,paywhen,amount,paypal FROM paycompl WHERE paywhen>DATE_SUB(CURRENT_TIMESTAMP(),INTERVAL 1 MONTH) ORDER BY paywhen");
$sfh->execute;

print "Payments received for online league during past month\n\n";

while (@row = $sfh->fetchrow_array) {
	my ($league, $descr1, $descr2, $when, $amt, $pp) = @row;
	$when =~ s;(\d+)-(\d+)-(\d+)\s+.*;$3/$2/$1;;
	print "$when\t$amt\t";
	if ($league eq 'T') {
		print "Team League payment for $descr1 (";
	}
	else {
		print "Individual league payment for $descr1 $descr2 (";
	}
	if ($pp)  {
		print "Paypal)\n";
	}
	else  {
		print "Cheque)\n";
	}
}

$sfh = $Database->prepare("SELECT first,last,email FROM player WHERE length(email) > 0");
$sfh->execute;
while (@row = $sfh->fetchrow_array) {
	$Emails{"$row[0] $row[1]"} = $row[2];
}

print "\nTeams who have not paid yet (+ Captain name and email)\n\n";
$sfh = $Database->prepare("SELECT name,captfirst,captlast FROM team WHERE playing!=0 AND paid=0 ORDER BY name");
$sfh->execute;
while (@row = $sfh->fetchrow_array) {
	my ($nam,$cf,$cl) = @row;
	my $em = '(none)';
	$em = $Emails{"$cf $cl"} if defined $Emails{"$cf $cl"};
	print "$nam\t$cf $cl\t$em\n";
}

#print "\nIndividual league players not paid yet + email\n\n";

#$sfh = $Database->prepare("SELECT first,last FROM player WHERE ildiv>0 AND ilpaid=0 ORDER BY last,first");
#$sfh->execute;
#while (@row = $sfh->fetchrow_array)  {
#	my ($f,$l) = @row;
#	my $em = '(none)';
#	$em = $Emails{"$f $l"} if defined $Emails{"$f $l"};
#	print "$f $l\t$em\n";
#}
select STDOUT;
