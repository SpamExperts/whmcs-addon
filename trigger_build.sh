#!/bin/bash

BASE_VERSION="2.0"
SVN_USERNAME=`python -c "import ConfigParser;c=ConfigParser.ConfigParser();c.read('/etc/spamexperts/build.conf');print c.get('frontend', 'svn_user')"`
SVN_PASSWORD=`python -c "import ConfigParser;c=ConfigParser.ConfigParser();c.read('/etc/spamexperts/build.conf');print c.get('frontend', 'svn_password')"`

REVISION=`svn info --no-auth-cache --password ${SVN_PASSWORD} --username ${SVN_USERNAME} | fgrep Revision | awk {'print $2'}`
if [ -z $REVISION ]; then
    echo "Could not retrieve current revision, unable to proceed."
    exit 1
fi

if [ -f ./latest.tar.gz ]; then
    rm ./latest.tar.gz
fi

tar czf ./latest.tar.gz * --exclude=build.sh --exclude=trigger_build.sh --exclude=.svn --exclude=*.tar.gz

mv --force ./latest.tar.gz /var/www/download/integration/files/whmcs/latest.tar.gz
echo "${BASE_VERSION}.${REVISION}" > /var/www/download/integration/files/whmcs/latest_version.html

bash /home/spamexperts/trunk/utils/sync_download.sh integration 1>&2 > /dev/null

printf "\n\e[31m[!] Don't forget to push this version to GitHub (https://github.com/SpamExperts/whmcs-addon)\e[0m\n\n"

exit 0
