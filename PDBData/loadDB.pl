#!/usr/bin/perl
use DBI;
use strict;
#
require "bdconn.pl";
#
my $dbh=connectDB();

# First Clean Tables !!
foreach my $tab ('entry', 'source', 'sequence', 'author', 'author_has_entry', 'expType', 'comptype', 'entry_has_source', 'expClasse') {
   print "Cleaning $tab\n";
   $dbh->do ("DELETE FROM $tab");
}
# Set Auto-increment PKs to 1
foreach my $tab ('source','author','expType','comptype','expClasse') {
   $dbh->do ("ALTER TABLE $tab AUTO_INCREMENT=1");
}
#
# Temporarily stop checking of FKs. Otherwise loadign should be done in order
$dbh->do("SET FOREIGN_KEY_CHECKS=0");
#
print "Authors...";
my %AUTHORS;
my %author_has_entry;
# In Perl DBI SQL commands can be "prepared" before use to ease the syntax later, ? represent parameters
my $sthAuthor=$dbh->prepare ("INSERT INTO author (author) VALUES (?)");
my $sthEntryAuthor=$dbh->prepare("INSERT INTO author_has_entry VALUES (?,?)");
open AUTS, "author.idx";
while (<AUTS>) {
   next if !/;/;
   chomp;
   my ($idCode, $author) = split / *; */;
   next if (!$author);
   if ($author && !$AUTHORS{$author}) {
   	$sthAuthor->execute ($author);
# last_insert_id recovers the last inserted value when auto_increment PK is in place
	$AUTHORS{$author}=$dbh->last_insert_id('','','Author','idAuthor');
   }
   if (!$author_has_entry{"$AUTHORS{$author}-$idCode"}) {
      $sthEntryAuthor->execute ($AUTHORS{$author},$idCode);
      $author_has_entry{"$AUTHORS{$author}-$idCode"}=1;
   }
}
close AUTS;
print "ok\n";
#
#
print "Sources...";
my %SOURCES;
my %Entry_has_source;
my $sthSource =$dbh->prepare("INSERT INTO source (source) VALUES (?)");
my $sthEntrySource = $dbh->prepare("INSERT INTO entry_has_source (idCode,idSource) VALUES (?,?)");
open SOUR, "source.idx";
while (<SOUR>) {
   chomp;
   my ($idCode,$source) = split ' ', $_, 2;
   next if (!$source) || (length($idCode) != 4);
   foreach my $s (split /; */, $source) {
      if (!$SOURCES{$s}) {
         $sthSource->execute ($s);
	 $SOURCES{$s}=$dbh->last_insert_id('','','source','idSource');
      }
      $sthEntrySource->execute($idCode, $SOURCES{$s});
   }
}
close SOUR;
print "ok\n";
#
#
print "Entries...";
open ENTR, "entries.idx";
my $sthEntry = $dbh->prepare ("INSERT INTO entry (idCode, header, ascessionDate, compound, resolution) VALUES (?,?,?,?,?)");
my %ExpTypes;
my $sthExpType = $dbh->prepare ("INSERT INTO expType (ExpType) VALUES (?)");
my $sthEntryExpType = $dbh->prepare ("UPDATE entry SET idExpType=? WHERE idCode=?");
my %expTypesbyCode;
while (<ENTR>) {
   chomp;
   my ($idCode, $header, $ascDate, $compound, $source, $authorList, $resol, $expType) = split /\t/;
   next if (length($idCode) != 4);
   if ($resol !~ /(0-9)/) {
        $resol =0
}
        $sthEntry-> execute ($idCode,$header, $ascDate, substr($compound,0,255), $resol);   	
        # if the $expType is new, add to the ExpType table first to get the idExpType
        if (!$ExpTypes{$expType }) {
            $sthExpType->execute($expType);
            $ExpTypes{$expType}=$dbh->last_insert_id('','','ExpType','idExpType');
        }
	$sthEntryExpType->execute($ExpTypes{$expType},$idCode);
	$expTypesbyCode{$idCode}=$expType;
}
close ENTR;
print "ok\n";
#
#
open EXPCL , 'pdb_entry_type.txt';
my %expClasses;
my %compTypes;
my $sthExpClasse=$dbh->prepare('INSERT INTO expClasse (expClasse) VALUES (?)');
my $sthcompType=$dbh->prepare('INSERT INTO comptype (type) VALUES (?)');
my $entryUpdate= $dbh->prepare('UPDATE entry SET idCompType=? WHERE idCode=?');
my $expTypeUpdate=$dbh->prepare('UPDATE expType SET idExpClasse=? where ExpType=?');

while (<EXPCL>) {
   chomp;
	my ($idCode, $compType, $expClass) = split ' ';
	$idCode =~ tr/a-z/A-Z/;
        # if $expClass is new, add first to expClasses table
	if (!$expClasses{$expClass}) {
		$sthExpClasse->execute($expClass);
		$expClasses{$expClass}=$dbh->last_insert_id('','','expClasses','idExpClass');
	}
        # if $compType is new, add first to compType table
	if (!$compTypes{$compType}) {
		$sthcompType->execute($compType);
		$compTypes{$compType}=$dbh->last_insert_id('','','compType','idCompType');
	}
	$expTypeUpdate->execute($expClasses{$expClass}, $expTypesbyCode{$idCode});
	$entryUpdate->execute($compTypes{$compType},$idCode);
}	
#Sequence
print "Sequences...";
my $codes=[];
my %CODES;
my $sthInsert= $dbh->prepare ("INSERT INTO sequence (idCode,chain,sequence,header) VALUES (?,?,?,?)");
open "SEQS", "pdb_seqres.txt";
my $seq;
my $idPdb;
my $chain;
my $header;
while (<SEQS>) {
   chomp;
   if (/^>/) {
	if ($seq) {
		$seq =~ s/\n//g;
		$idPdb =~ tr/a-z/A-Z/;
		$chain =~ s/ //;
   		$sthInsert->execute ($idPdb,$chain,$seq, $header) || die $DBI::errstr;
	        $seq="";
	}
	/^>([^_]*)_(.*)mol:(\S*) length:(\S*) (.*)/;
	($idPdb,$chain,$header)=($1,$2,"$1 $2 $3 $4 $5");
   }
   else {$seq .= $_};
};
print "ok\n";
# Recover the FK checking
$dbh->do("SET FOREIGN_KEY_CHECKS=1");
disconnectDB($dbh);
