#! /usr/bin/perl

##
## Copyright (c) Xi Software Ltd. 2009.
##
## convclub.pl: created by John M Collins on Fri May  8 2009.
##

use DBD::mysql;

$file = shift || die "No file given";
open(DF, $file) || die "Cannot open $file\n";

$Database = DBI->connect("DBI:mysql:bgaleague:ruffles") or die "No database";

while (<DF>) {
    chomp;
    unless (/(\w+)\s+=\s+(.*)/)  {
	print "Didnt match $_\n";
	next;
    }
    my $code = $1;
    my $name = $2;
    my $qcode = $Database->quote($code);
    my $qname = $Database->quote($name);
    my $sfh = $Database->prepare("select name from club where name=$qname");
    $sfh->execute;
    if ($sfh->rows == 0)  {
	$sfh = $Database->prepare("insert into club (name,code) values ($qname,$qcode)");
	$sfh->execute;
    }
    else {
	$sfh = $Database->prepare("update club set code=$qcode where name=$qname");
	$sfh->execute;
	$sfh = $Database->prepare("update player set club=$qcode where club=$qname");
	$sfh->execute;
    }
}
