#!/usr/bin/bash

PWD=$(pwd)

echo "Current Working Directory: " $PWD

echo "Enter Tag Name:"
read tagname

#
# DEFAULT VARIABLES
#
gitrepo="https://github.com/Will-Smelser/SimpleSeo.git"
ftpserver="mediocredeveloper.com"
ftpuser="willsmelser"
ftppass="x5J1480!"
ftpdir="simple-seo-api"
gitname="SimpleSeo"
phpdeploy="deploy.php"
phpdeployrun="http://${ftpserver}/${ftpdir}/${phpdeploy}"

#
# temporary files
#
tarball="deploy-${tagname}.zip"
tempdir="temp-deploy-${tagname}"
timestamp=$(date '+%s')

#
# CLEANUP PREVIOUS DEPLOY
#
if [ -d $tempdir ]; then
	echo "Deleting temp directory"
	rm -Rf $tempdir
fi

echo "Creating temp Directory"
mkdir $tempdir
cd $tempdir

#
# GET PROJECT FROM GIT and
# USE TAG VERSION
#
echo -e "\nCloning..."

git init
git clone $gitrepo

echo "Checking Out Tag"
echo "cd ${gitrepo}"
cd $gitname
output=$(git tag | grep $tagname)

#verify that the tag exists
if [ ! "${tagname}" == "${output}" ]; then
	echo -e "\nFAIL: Tag ${tagname} does not exist. Aborting..."
	exit
fi

git checkout tags/$tagname > /dev/null

#
# PHPDOC
#
base="../"
$(cd site/protected/extensions/seo/api && phpdoc.bat -d . -f ${base}/config.php,${base}/class/ServerInfo.php,${base}/api/header.php,${base}/class/Node.php,${base}/class/GoogleInfo.php,${base}/class/helpers/ApiResponse.php,${base}/class/helpers/ClientHash.php  -t ../../../../api-docs --template simpleseo)


#
# JSDOC
#
$(cd site && jsdoc themes/reports/js/api/SeoApi.js themes/reports/js/api/base.js -d ./api-docs-js)

#
# Delete Deploy Directory
#
echo -e "Deleting deploy directory..."
rm -Rf deploy

echo -e "\nMake tarball of release..."
echo "zip -r ${tarball} *"
zip -r $tarball * > /dev/null

#check the file exists
echo -e  "\nChecking tarball is good"
if [ ! -f $tarball ]; then
	echo -e "\nFAIL: tar command failed.  Aborting..."
	exit;
else
	echo "File looks good. Continuing..."
fi

#check the file has content
actualsize=$(du -b "${tarball}" | cut -f 1)
if [ $actualsize -le 1500 ]; then
	echo -e "\nFAIL: Tarball was created, but had no content.  Aborting..."
	exit;
fi

echo -e "\nMaking deploy script..."
echo "
<?php 
system('unzip deploy-${tagname}.zip');
unlink('${tarball}');
echo 'Done Unzipping'; 

echo 'Delete Deploy File';
unlink(__FILE__);

?>
" >> $phpdeploy



echo "Begin FTP..."

ftp -n <<EOF
open $ftpserver
user $ftpuser $ftppass
passive
prompt
rename ${ftpdir}-temp ${ftpdir}-temp-${timestamp} 
mkdir ${ftpdir}-temp
cd ${ftpdir}-temp
mput $tarball $phpdeploy
cd ..
rename ${ftpdir} ${ftpdir}-${timestamp}
rename ${ftpdir}-temp ${ftpdir}
EOF

echo -e "\nFTP Complete..."

echo -e "\nRun ${phpdeployrun} script"
curloutput=$(curl -i ${phpdeployrun} | grep HTTP/1)

if [ "${curloutput}" != "HTTP/1.1 200 OK" ]; then
	echo -e "*** FAIL ***: deploy script did not run"
	echo $curloutput
	exit;
fi 

#echo -e "\nCheck things deployed..."

echo -e "\nDeploy Complete"
