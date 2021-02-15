# MU Plugin Loader

This plugin is an alternative to [lkwdwrd/wp-muplugin-loader](https://github.com/lkwdwrd/wp-muplugin-loader), which uses a transient to try and avoid checking the filesystem repeatedly but can cause new MU plugins to be missing for if the transient has not yet expired.  Instead, mine skips all that and just looks at the filesystem every time.
