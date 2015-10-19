#!/bin/bash

# Make sure everything is up-to-date before triggering the actual build
# @see https://trac.spamexperts.com/ticket/24852
SVN_USERNAME=`python -c "import ConfigParser;c=ConfigParser.ConfigParser();c.read('/etc/spamexperts/build.conf');print c.get('frontend', 'svn_user')"`
SVN_PASSWORD=`python -c "import ConfigParser;c=ConfigParser.ConfigParser();c.read('/etc/spamexperts/build.conf');print c.get('frontend', 'svn_password')"`
svn up --force --no-auth-cache --password ${SVN_PASSWORD} --username ${SVN_USERNAME}
if [ $? -ne 0 ]; then
    echo "svn up failed!"
    exit 1
fi

./trigger_build.sh
