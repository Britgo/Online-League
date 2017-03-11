#! /usr/bin/perl
##
##
## Copyright (c) John M Collins, Xi Software Ltd 2009.
##
## kgsfetchsgf.pl: created on Fri Nov 13 2009.
##----------------------------------------------------------------------
##

$White = shift;
$Black = shift;
$Dat = shift;
$Res = shift;

die "Invalid date format" unless $Dat =~ /(\d\d\d\d)-(\d\d?)-(\d\d?)/;

($year, $month, $day) = ($1,$2,$3);

# Clear any leading zeroes off month and day because KGS doesn't use them

$month += 0;
$day += 0;

# Fetch catalogue of games of that user for the given month

$targ = "http://www.gokgs.com/gameArchives.jsp?user=$White\&year=$year\&month=$month";

open(WF, "wget -q -O - '$targ' 2>/dev/null|") or die "Cannot open KGS index";

while (<WF>) {
    chomp;
    while (m|"(http://files.gokgs.com/games/$year/$month/$day/$White-$Black(-\d+)?\.sgf)".*?([WB]\+[^<]*)|ig) {
	push @glist, $1;
	push @rlist, $3;
    }
}

close WF;

# We might have no games - stop
# We might have more than one game - try to distinguish it using the result
# Otherwise we've got it.

if ($#glist < 0) {
    print STDERR "No games found\n";
    exit 10;
}
if ($#glist == 0) {
    $Thegame = $glist[0];
}
else {
    unless ($Res) {
	print STDERR $#glist + 1, " games found - please give result to select\n";
	for my $r (@rlist) {
	    print STDERR "$r\n";
	}
	exit 11;
    }
    if ($Res =~ /([BW])\+R/i) {
	$Res = "$1+Res.";
    }
    elsif ($Res =~ /([BW])\+T/i) {
	$Res = "$1+Time";
    }
    elsif ($Res =~ /([BW])\+H/i) {
	$huge = 1;
    }
  gotit: {
      my @rl = @rlist;
      for my $g (@glist) {
	  my $r = shift @rl;
	  if ($r eq $Res) {
	      $Thegame = $g;
	      last gotit;
	  }
	  elsif ($r =~ /[BW]+(\d+)\.5/i && $1 >= 50 && $huge)  {
	      $Thegame = $g;
	      last gotit;
	  }
      }
      print STDERR $#glist + 1, " games found which didn't match result\n";
      for my $r (@rlist) {
	  print STDERR "$r\n";
      }
      exit 12;
    }
}

$sgf = `wget -q -O - $Thegame`;
if (length($sgf) == 0) {
    print STDERR "Couldn't fetch game\n";
    exit 13;
}

print $sgf;
