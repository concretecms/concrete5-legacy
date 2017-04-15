#!/bin/bash

# Do we need to add URL_REWRITING_ALL to config/site.php?
if [ -f "/home/vagrant/app/web/config/site.php" ]; then
	if grep -i --quiet url_rewriting /home/vagrant/app/web/config/site.php; then
	    echo "URL_REWRITING_ALL constant already defined";
	else
	    echo -e "\ndefine('URL_REWRITING_ALL', true);" >> /home/vagrant/app/web/config/site.php;
	    echo "Added URL_REWRITING_ALL constant to site config";
	fi
else
	echo "Attempted to configure PrettyURLs; config/site.php does not exist! Was your database empty on install?"
fi