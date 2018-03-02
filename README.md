## Installation

### Install H5P repository plugin
Start at your ILIAS root directory 
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone git@git.studer-raimann.ch:ILIAS/Plugins/H5P.git H5P
```

### Configure cronjob
H5P may accumulate temporary files.

This files should be cleaned up from time to time.

We recommend you to use an unix cronjob.

Please add the follow line to file `/etc/cron.d/ilias` on your server

```
*0 0 * * * www-data /usr/bin/php /var/www/ilias/Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/cron.php username password client > /dev/null
```

or run it manually

```
sudo -u www-data /usr/bin/php /var/www/ilias/Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/cron.php username password client
```

ILIAS username, password, client and interval need to be customized.

### Contact
info@studer-raimann.ch  
https://studer-raimann.ch  

