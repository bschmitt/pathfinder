Pathfinder
==========

Converts TYPO3 query strings into human readable paths and the other way around.

*It's a light and fast alternative to RealURL and CoolURI with advanced features like:*

* Easy automatic path generation
* History paths which get redirected by 301 to the current path
* Incremental path updates after moving or coping page trees including 301 redirects
* Path preview for hidden pages
* Fixed paths: Converts defined GET-Params to path parts
* 404 Logging
* Admin path search

*Why another URL-Path extension?*

Honestly we hard some difficulties with other extensions on our heavily used web page. E.g. duplicate paths, missing paths (404s) and no 301 redirects to obtain Google ranking, unequal paths in different languages, not updated (sub-)paths on page-move, ...

*What Pathfinder is not*

Main focus was performance simplicity and stablicity. It was built to do one thing: Generating paths therefore it is not so customizeabiliy as RealURL and CoolURI. 

It generates paths as follows:

```
/[langCode]/[title]/[title]/...
```

## Setup

Install Pathfinder with the Extension Manager.

![Install](http://i.imgur.com/0shm3km.png)

Update *settings.php* to your needs.

```php
return array(
	'rootPage'            => 1 ,
	'defaultLanguageCode' => 'en' ,
	//'languageMapping'   => array('de' => 2, 'fr' => 3)
);
```

## Usage

You will find on the left below *Admin Tools* a link called *Pathfinder*. This area is for admin tasks and not relevant for daily work.

The path of a page and all depending pages is generated on the first preview, so please open the root page in view mode. After that you will find in *List-Mode* a section called *URL Path* and if so *URL Path History* too.

![List](http://i.imgur.com/BDikkCM.png)


