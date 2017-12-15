## Installation

### Install H5P repository plugin
Start at your ILIAS root directory 
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://git.studer-raimann.ch/ILIAS/Plugins/H5P H5P
```

### Configure cronjob
H5P may accumulate temporary files.

This files should be cleaned up from time to time.

We recommend you to use an unix cronjob.

Please add the follow line to file `/etc/cron.d/ilias` on your server.

```
*/1 * * * * www-data /usr/bin/php /var/www/ilias/Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/cron.php username password client > /dev/null
```

ILIAS username, password and client need to be customized.

## Contact
studer + raimann ag  
Farbweg 9  
3400 Burgdorf  
Switzerland 

info@studer-raimann.ch  
www.studer-raimann.ch  
