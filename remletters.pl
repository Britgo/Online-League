#! /usr/bin/perl

exit 0 if -e "/var/www/onlineleague/nopayreminder";

use Config::INI::Reader;
use DBD::mysql;
use Time::Local;
use Template;
use Cwd 'abs_path';

$Where = abs_path($0);
@W = split('/', $Where);
pop @W;
$Where = join('/', @W);

# Must remember to change this

$inicont = Config::INI::Reader->read_file('/etc/webdb-credentials');
$ldbc = $inicont->{league};
$Database = DBI->connect("DBI:mysql:$ldbc->{database}", $ldbc->{user}, $ldbc->{password}) or die "Cannot open DB";

# Get first matchdate

$sfh = $Database->prepare("select matchdate from lgmatch order by matchdate limit 1");
$sfh->execute;
@row = $sfh->fetchrow_array;
die "No matches set up yet\n" unless @row;
($YEAR,$m,$d) = $row[0] =~ /(\d+)-(\d+)-(\d+)/;
$Startdate = timelocal(0,0,12,$d,$m-1,$YEAR);
$Now = time;
$Nmonths = int(($Now - $Startdate) / (3600.0*24.0*30.0));
$Nmonths = 2 if $Nmonths > 2;

# Get a list of teams who are playing and haven't paid, together with the team captain names and email address

$sfh = $Database->prepare("select name,captfirst,captlast,email from team,player where paid=0 and playing!=0 and captfirst=first and captlast=last and length(email)!= 0");
$sfh->execute;
while (my @row = $sfh->fetchrow_array) {
	my ($teamname,$captfirst,$captlast,$email) = @row;
	$Teamlist{$teamname} = {CAPTFIRST => $captfirst, CAPTLAST => $captlast, EMAIL => $email};
}

my $qres = $Database->quote('N');
for my $team (keys %Teamlist) {
	my $teamdets = $Teamlist{$team};
	my $qteam = $Database->quote($team);
	$sfh = $Database->prepare("select first,last from player,teammemb where teamname=$qteam and first=tmfirst and last=tmlast and bgamemb=0 order by last,first");
	$sfh->execute;
	while (my @row = $sfh->fetchrow_array)  {
		my ($mfirst,$mlast) = @row;
		push @{$teamdets->{NONBGA}}, {FIRST => $mfirst, LAST => $mlast};
	}

	$sfh = $Database->prepare("select count(*) from game where (wteam=$qteam or bteam=$qteam) and current!=0 and result!=$qres");
	$sfh->execute;
	my @row = $sfh->fetchrow_array;
	my $ng = 0;
	$ng = $row[0] + 0 if @row;
	$teamdets->{GAMES} = $ng;
}

$Tconfig = { INCLUDE_PATH => $Where };

$TT = Template->new($Tconfig);
$tfile = "unpaidletter$Nmonths.tt";

for my $team (sort keys %Teamlist)  {
	my $teamdets = $Teamlist{$team};
	my $nonbga = $teamdets->{NONBGA};
	my $numnon = 1+ $#$nonbga;
	my $price = 15 + 5 * $numnon;
	my @nbga = map { "$_->{FIRST} $_->{LAST}"; } @$nonbga;
	my $Tvars = { CAPTFIRST => $teamdets->{CAPTFIRST}, CAPTLAST => $teamdets->{CAPTLAST}, EMAIL => $teamdets->{EMAIL}, TEAM => $team,
						NUMNON => $numnon, NONBGA => \@nbga , AMOUNT => $price , YEAR => $YEAR , GAMES => $teamdets->{GAMES}};
	open(MF, "|/usr/sbin/sendmail -i -t");
	$TT->process($tfile, $Tvars, \*MF);
	close MF;
}
