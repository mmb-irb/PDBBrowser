#!/usr/bin/perl
# Connect script for DBI-Mysql
#
use DBI;

1;

sub connectDB {
	my $driver = "mysql";
	my $database = "pdb";
	my $host = "localhost";
	my $user="gelpi"; 
	my $passwd = "jl12gb";
	my $autoCommit = 1;
	my $dsn = "DBI:$driver:database=$database;host=$host;user=$user;password=$passwd";
	return DBI->connect ($dsn, undef, undef, 
		{RaiseError => 1, AutoCommit => $autoCommit});
}

sub disconnectDB {
	my $dbh = shift;
	return $dbh->disconnect;
}
