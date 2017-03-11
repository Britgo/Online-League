#! /usr/bin/perl

use Config::INI::Reader;
use DBD::mysql;

$inicont = Config::INI::Reader->read_file('/etc/webdb-credentials');
$ldbc = $inicont->{league};
$Database = DBI->connect("DBI:mysql:$ldbc->{database}", $ldbc->{user}, $ldbc->{password}) or die "Cannot open DB";

die "Cannot open git" unless open(LG, "git log --no-color -n 1|");

while (<LG>)  {
	chop;
   $lastline = $_;
   if (/^Author:\s*.*<(.*)@.*>/) {
		$auth = $1;
		next;
   }
}

$lastline =~ s/^\s*(.*)\s*$/$1/;
exit 0 unless length($lastline) != 0;
$lastline = "Software update: $lastline";
$auth = "ADMINS" unless $auth;
$qlog = $Database->quote($lastline);
$quser = $Database->quote($auth);
$sfh = $Database->prepare("insert into news (ndate,user,item,trivial) values (current_date,$quser,$qlog,1)");
$sfh->execute();
exit 0;
