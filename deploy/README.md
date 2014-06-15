deploy files
============

# need module

```
$ cpanm File::Spec
$ cpanm File::Path
```

# execute

```
# production
$ perl deploy.pl bms.list

# staging
$ perl deploy.pl bms_staging.list
```

# option

```
$ perl deploy.pl bms.list force debug
```

* force is no confirm mode when replace file
* debug is debug mode
