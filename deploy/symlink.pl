#!/usr/bin/env perl

use strict;
use warnings;
use File::Spec;

my $list = 'bms.list';

my @files = `cat $list`;

for my $row ( @files ) {

    chomp $row;
    $row =~ s{\s+}{ }g; 

    next if $row =~ m{^#} || $row =~ m{^$};

    my ( $orig, $dest ) = split(' ', $row);

    $orig = File::Spec->rel2abs($orig);
    $dest = File::Spec->rel2abs($dest);

    if ( -f $dest || -l $dest ) {
        `rm $dest`;
    }

    if ( symlink $orig, $dest ) {
        print "symlink $dest -> $orig\n";
    }
    else {
        warn "Failed symlink $orig : $!";
    }
}
