## Installation

### Install H5P repository plugin
Start at your ILIAS root directory 
```bash
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
cd Customizing/global/plugins/Services/Repository/RepositoryObject
git clone https://github.com/studer-raimann/H5P.git H5P
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

```bash
sudo -u www-data /usr/bin/php /var/www/ilias/Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/cron.php username password client
```

ILIAS username, password, client and interval need to be customized.

### Documentation
Click [here](./doc/Documentation.pdf) for a more detailed documentation.

### Dependencies
* [h5p/h5p-core](https://packagist.org/packages/h5p/h5p-core)
* [h5p/h5p-editor](https://packagist.org/packages/h5p/h5p-editor)
* [srag/activerecordconfig](https://packagist.org/packages/srag/activerecordconfig)
* [srag/dic](https://packagist.org/packages/srag/dic)
* [srag/removeplugindataconfirm](https://packagist.org/packages/srag/removeplugindataconfirm)

Please use it for further development!

### Contact
info@studer-raimann.ch  
https://studer-raimann.ch  
