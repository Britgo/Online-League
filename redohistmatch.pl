#! /usr/bin/perl

use Config::INI::Reader;
use DBD::mysql;

%Params = (p => 0.01, w => 100, d => 50, l => 0, f => 1, a => 0, j => 0.5, hd => 2, hr => 1, fz => 2);

$inicont = Config::INI::Reader->read_file('/etc/webdb-credentials');
$ldbc = $inicont->{league};
$Database = DBI->connect("DBI:mysql:$ldbc->{database}", $ldbc->{user}, $ldbc->{password}) or die "Cannot open DB";

# Reset params

$sfh = $Database->prepare("SELECT sc,val FROM params");
$sfh->execute;
while (my @row = $sfh->fetchrow_array)  {
	$Params{$row[0]} = $row[1] + 0;
}

# Build up table of matches by ind

$sfh = $Database->prepare("SELECT ind,seasind,hteam,ateam FROM histmatch");
$sfh->execute;

while (my @row = $sfh->fetchrow_array)  {
	my ($ind,$seasind,$hteam,$ateam) = @row;
	$ind += 0;
	$seasind += 0;
	$histmatches{$ind} = {SEASIND => $seasind, HTEAM => $hteam, ATEAM => $ateam, HWIN => 0, AWIN => 0, DRAW => 0};	
}

for my $mtchind (keys %histmatches)  {
	my $mtch = $histmatches{$mtchind};
	my $hteam = $mtch->{HTEAM};
	my $ateam = $mtch->{ATEAM};
	my $sfh = $Database->prepare("SELECT ind,wteam,bteam,result FROM game WHERE matchind=$mtchind");
	$sfh->execute;
	while (my @row = $sfh->fetchrow_array)  {
		my ($ind,$wteam,$bteam,$result) = @row;
		if ($hteam eq $wteam)  {
			unless  ($ateam eq $bteam)  {
				print "Confused about game $ind, HT=$hteam At=$ateam WT=$wteam BT=$bteam\n";
				continue;
			}
			if ($result eq 'W')  {
				$mtch->{HWIN}++;
			}
			elsif ($result eq 'B')  {
				$mtch->{AWIN}++;
			}
			elsif ($result eq 'J')  {
				$mtch->{DRAW}++;
			}
			else {
				print "Unexpected historic game $ind, result=$result\n";
				continue;
			}
		}
		elsif ($hteam eq $bteam) {
			unless  ($ateam eq $wteam)  {
				print "Confused about game $ind, HT=$hteam At=$ateam WT=$wteam BT=$bteam\n";
				continue;
			}
			if ($result eq 'B')  {
				$mtch->{HWIN}++;
			}
			elsif ($result eq 'W')  {
				$mtch->{AWIN}++;
			}
			elsif ($result eq 'J')  {
				$mtch->{DRAW}++;
			}
			else {
				print "Unexpected historic game $ind, result=$result\n";
				continue;
			}
		}
		else  {
			print "Confused about game $ind, HT=$hteam At=$ateam WT=$wteam BT=$bteam\n";
		}
	}
}

for my $mtchind (keys %histmatches)  {
	my $mtch = $histmatches{$mtchind};
	my $hwins = $mtch->{HWIN};
	my $awins = $mtch->{AWIN};
	my $draws = $mtch->{DRAW};
	my $sfh = $Database->prepare("UPDATE histmatch SET hwins=$hwins,awins=$awins,draws=$draws WHERE ind=$mtchind");
	$sfh->execute;
}
