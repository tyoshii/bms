#!/usr/bin/env perl

use strict;
use warnings;
use File::Basename;
use File::Copy qw( copy );
use File::Spec;
use File::Path;

my $list = 'bms.list';

my @files = `cat $list`;

my $hash;
for my $row ( @files ) {

    chomp $row;
    $row =~ s{\s+}{ }g; 

    next if $row =~ m{^#} || $row =~ m{^$};

    my ( $cmd, $orig, $dest ) = split(' ', $row);

    $orig = File::Spec->rel2abs($orig) if $orig;
    $dest = File::Spec->rel2abs($dest) if $dest;

    if ( ! $hash->{$cmd} ) {
        $hash->{$cmd} = [];
    }

    push @{ $hash->{$cmd} }, {
        orig => $orig,
        dest => $dest,
    };
}

for my $hash_dir ( @{$hash->{'dir'}} ) {
    my $dir = $hash_dir->{'orig'};

    if ( -e $dir && -f $dir ) {
        warn "already exists file: $dir";
    }
    elsif ( ! -d $dir ) {
        mkdir $dir or die "Failed mkdir $dir : $!";
    }
}
    
for my $hash_sym ( @{$hash->{'symlink'}} ) {

    my $dest = $hash_sym->{'dest'};
    my $orig = $hash_sym->{'orig'};

    if ( -f $dest || -l $dest || -d $dest ) {
        `rm -irf $dest`;
    }

    if ( -d $orig ) {

        # mkdir
        mkdir $dest;
        chdir $dest;

        my @orig_lists = `find $orig -type f`;
        for my $f ( @orig_lists ) {
            chomp $f;
            next if $f =~ m{\.git};

            my $dest_path = $f; 
            $dest_path =~ s{$orig}{};
            $dest_path =~ s{^/}{};
            my $orig_path = File::Spec->catfile( $orig, $dest_path);

            if ( ! -d dirname($dest_path) ) {
                mkpath dirname($dest_path);
            }

            _symlink($orig_path, $dest_path);
        }
    }
    else {
        _symlink($orig, $dest);
    }
}
    
for my $hash_copy ( @{$hash->{'copy'}} ) {

    my $dest = $hash_copy->{'dest'};
    my $orig = $hash_copy->{'orig'};

    if ( -f $dest || -l $dest || -d $dest ) {
        `rm -irf $dest`;
    }

    _copy($orig, $dest)
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
