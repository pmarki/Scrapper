Scrapper - simply webscrapper class for getting links and metadata from wordpress posts
##Requirements
PHP 5.4+
##Instalation
Download all files to your server folder, run composer to install 
all dependencies (/path/to/your/composer.phar install) and import Scrapper.php into your project.
```
require_once 'Scrapper.php'; 
```
##How to use
Initialization
```
$scrapper = new Scrapper('your-site-url', 'your-category');
```
Getting data
```
echo $scrapper->json();
```
Take a look at example.php for working example.
##Credits
Goutte library [Goutte](https://github.com/FriendsOfPHP/Goutte) - MIT licence
