#! /usr/bin/perl

##
## Copyright (c) Xi Software Ltd. 2009.
##
## parselist: created by John M Collins on Fri May  8 2009.
##

use HTML::Parser;
use DBD::mysql;

sub copy {
    my $arr = shift;
    my @result = ();
    for my $a (@$arr) {
	push @result, $a;
    }
    \@result;
}

sub starttag {
    my $tag = shift;
    if ($tag eq 'table') {
	$intable = 1;
    }
    elsif ($tag eq 'tr') {
	$intr = 1;
	@headers = ();
    }
    elsif ($tag eq 'th') {
	$inth = 1;
	@data = ();
    }
    elsif ($tag eq 'td') {
	$intd = 1;
    }
}

sub endtag {
    my $tag = shift;
    if ($tag eq 'table') {
	$intable = 0;
    }
    elsif ($tag eq 'th') {
	$inth = 0;
    }
    elsif ($tag eq 'td') {
	$intd = 0;
    }
    elsif ($tag eq 'tr') {
	$intr = 0;
	if ($#headers >= 0) {
	    push @Headers, copy(\@headers);
	    @headers = ();
	}
	if ($#data >= 0) {
	    push @Data, copy(\@data);
	    @data = ();
	}
    }
}

sub proctext {
    my $text = shift;
    return unless $intable;
    if ($inth) {
	push @headers, $text;
    }
    elsif ($intd) {
	push @data, $text;
    }
}

$file = shift || die "No file given";

$intable = $intr = $inth = $intd = 0;

$pars = HTML::Parser->new(text_h => [ \&proctext, 'text'], start_h => [ \&starttag, "tagname"], end_h => [ \&endtag, 'tagname' ]);
			  
$pars->parse_file($file);

$Largest_header = 0;
for my $h (@Headers) {
    if  ($#$h > $Largest_header)  {
	$Largest_header = $#$h;
	@Titles = @$h;
    }
}

$Database = DBI->connect("DBI:mysql:bgaleague:ruffles") or die "No database";

for my $d (@Data) {
    next unless $#$d == $Largest_header;
    my @t = @Titles;
    my @d = @$d;
    my %ent;
    while (@t) {
	my $n = shift @t;
	my $v = shift @d;
	$ent{$n} = $v;
    }
    my $name = $ent{'Name'};
    my $first;
    my $last = $name;
    if ($name =~ /(.*)\s+(.*)/) {
	$first = $1;
	$last = $2;
    }
    my $grade = $ent{'Grade'};
    my $club = $ent{'Club'};
    write;
    format STDOUT =
 @<<<<<<<<<<<<<< @<<<<<<<<<<<<< @<<<<< @<<<<<<<<<<<<<<<<<<<<
	$first, $last, $grade, $club
.
    $Clubs{$club}++;

    my $qfirst = $Database->quote($first);
    my $qlast = $Database->quote($last);
    my $qclub = $Database->quote($club);
    my $qgr = 0;
    if ($grade =~ /(\d+)k/i) {
	$qgr = - $1;
    }
    elsif ($grade =~ /(\d+)d/i)  {
	$qgr = $1 - 1;
    }
    my $sfh = $Database->prepare("insert into player (first,last,rank,club) values ($qfirst,$qlast,$qgr,$qclub)");
    $sfh->execute;
}

for my $c (sort keys %Clubs) {
    print "$c\t$Clubs{$c}\n";
    my $qc = $Database->quote($c);
    my $sfh = $Database->prepare("insert into club (name) values ($qc)");
    $sfh->execute;
}
