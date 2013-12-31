#!/usr/bin/env perl

use strict;
use warnings;
use File::Copy qw( copy );
use File::Spec;

my $list = 'bms.list';

my @files = `cat $list`;

for my $row ( @files ) {

    chomp $row;
    $row =~ s{\s+}{ }g; 

    next if $row =~ m{^#} || $row =~ m{^$};

    my ( $cmd, $orig, $dest ) = split(' ', $row);

    $orig = File::Spec->rel2abs($orig);
    $dest = File::Spec->rel2abs($dest);
    
    if ( -f $dest || -l $dest || -d $dest ) {
        `rm -rf $dest`;
    }

    if ( $cmd eq 'symlink' ) {
        _symlink($orig, $dest);
    }
    elsif ( $cmd eq 'copy' ) {
        _copy($orig, $dest)
    }
    else {
        warn "not correct command\n";
    }
}

sub _copy {
    my $orig = shift;
    my $dest = shift;

    if ( copy $orig, $dest ) {
        print "copy $dest -> $orig\n";
    }
    else {
        warn "Failed copy $orig : $!";
    } 
}

sub _symlink {
    my $orig = shift;
    my $dest = shift;

    if ( symlink $orig, $dest ) {
        print "symlink $dest -> $orig\n";
    }
    else {
        warn "Failed symlink $orig : $!";
    }
}
