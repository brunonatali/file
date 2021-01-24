# File

Use this library to manipulate and interact with the file system.  
Note. Some functions or classes cannot interact with the Windows system, so use it with caution.
  
  
**WARNING**: The [OnFileChange](#on-file-change) class needs [reactphp/event-loop](https://github.com/reactphp/event-loop) to work, but it was not added as required in the composer configuration so as not to force the average user to include a library that they will never use, so don't forget to require it when installing your program, if you are going to use this class.

**Table of Contents**
* [OnFileChange](#on-file-change) 
    * [Polling example](#polling-example)
    * [Inotify example](#inotify-example)
    * [Configuration](#ofl-class-config)
    * [start()](#ofc-start)
    * [stop()](#ofc-stop)
    * [setPollingTime()](#ofc-set-polling)
    * [static isFileChanged()](#ofc-is-file-changed)
* [JsonFile](#json-file)
    * [readAsArray()](#json-read)
    * [saveArray()](#json-save)
* [FileHandler](#file-handler)
    * [Not documented yet](#fh-not-doc)
* [Install](#install)
* [License](#license)

## OnFileChange
OnFileChange is a little help for you to monitor when a file has been modified and take some action from it.  

**Read the following notes**

This class supports debug by including [brunonatali/tools](https://github.com/brunonatali/tools) in your composer project.   
  
**ATENTION**: The [OnFileChange](#on-file-change) class needs [reactphp/event-loop](https://github.com/reactphp/event-loop) to work, include it by hand in your project!

**PERFORMANCE**: For performance purpouses install the [inotify](https://pecl.php.net/package/inotify) PECL extension and include [brunonatali/inotify](https://github.com/brunonatali/inotify) in your project.

### Polling example
```php
use BrunoNatali\File\OnFileChange;

use React\EventLoop\Factory as LoopFactory;

$loop = LoopFactory::create();

$myFuncToCall = function () {
    echo "File changed!";
};

try {
    $onFileChange = new OnFileChange(
        '/my/path/to.file', 
        $loop, 
        $myFuncToCall,
        /**
         * You can pass an configuration array to force polling or configure polling time
         * but generally system will use polling if brunonatali/inotify was not found and
         * a 1 sec polling time as default
        */
        [
            'force_ppolling' => true,
            'polling_time' => 1.0 // check for file changes every 1 sec
        ]
    );
} catch ($e \Exception) {
    /**
     * Exception codes:
     * ERROR_FILE_NAME_ABSENT -> file name not provided 
     * ERROR_FILE_CALL_ABSENT -> no callable function
     * ERROR_FILE_LOOP_ABSENT -> unknown LoopInterface
     * ERROR_FILE_NOT_EXIST -> non existent file
    */
}
```

### Inotify example
Install [brunonatali/inotify](https://github.com/brunonatali/inotify) in your project by typing:
```shell
composer require brunonatali/inotify
```
  
```php
// Inotify is automatically included if available
use BrunoNatali\File\OnFileChange;

use React\EventLoop\Factory as LoopFactory;

$loop = LoopFactory::create();

$myFuncToCall = function () {
    echo "File changed!";
};

try {
    $onFileChange = new OnFileChange('/my/path/to.file', $loop, $myFuncToCall);
} catch ($e \Exception) {
    /**
     * Exception codes:
     * ERROR_FILE_NAME_ABSENT -> file name not provided 
     * ERROR_FILE_CALL_ABSENT -> no callable function
     * ERROR_FILE_LOOP_ABSENT -> unknown LoopInterface
     * ERROR_FILE_NOT_EXIST -> non existent file
    */
}
```

### Configuration
Configuations are passed in array format on 4th OnFileChange() arg as follows:
```php
$config = [
    'client_name' => 'FILE-X', // Desired app name when included and using debug mode []
    'auto_start' => true, // Tell if file monitoring will start ASAP as initialized. If false, start() is necessary
    'force_ppolling' => false, // Force polling mothod instead inotify
    'polling_time' => 1.0, // Check file change period when rining on polling method
    /**
     * Provide an Inotify constant to filter specific attr change
     *   NOTE 1. To specify more than one constant place each one in array (see below). 
     *   NOTE 2. 'specific_attr' has no effect when running on polling method
    */
    'specific_attr' => false // Pasing a single flag as simple like this => IN_MODIFY
];

// https://www.php.net/manual/en/inotify.constants.php
$inotifyFilters = [
    IN_ACCESS,
    IN_MODIFY 
];
```

### start()
Used to starts watching file change. Only takes effect when stoped or initialized with 'auto_start' => false.  
This function dos not return nothing.

### stop()
Stops file change verification. This function dos not return nothing.

### setPollingTime()
Configure polling time when using this method.
```php
/**
 * Set file verification for every 10 sec.
 * Returns FALSE if is not configured to use polling method or can`t stops running timer
*/
$onFileChange->setPollingTime(10.0);
```

### static isFileChanged()
You can manually check file changes by calling isFileChanged(). This function is provided statically to could be called by hand
```php
$file = '/my/path/to.file';
$lastModifiedDate = null;

while (true) {
    if (OnFileChange::isFileChanged($file, $lastModifiedDate))
        echo "File checked manually & was changed!";
    sleep(5);
}
```

## JsonFile
This class is available with static functions for easy interaction, with the objective of easy manipulation / creation of JSON files.

### readAsArray()
Reads entire json file as array. This function is meant to be a function to simplify use of native PHP functions file_get_contents() and json_decode(), adding some validations.  
Simple call:
```php
$jsonArray = \BrunoNatali\File\Json\readAsArray('\my\path\to\file.json');

/**
 * You can add paths to search desired file.
 * Than file will be searched in every provided paths and than readed
*/
$anotherJsonArray = \BrunoNatali\File\Json\readAsArray('file.json', '\my\path\one', '\my\path\two');
}
```

### saveArray()
Save provided array to a JSON file, returning a boolean  success result.  
You can provide [PHP JSON flags](https://www.php.net/manual/en/json.constants.php).
```php
$dataArray = [
    'simple-obj' => [
        1,2,3,4,5
    ]
];
/**
 * Comom use
*/
$jsonArray = \BrunoNatali\File\Json\saveArray('\my\path\to\file.json', $dataArray);

/**
 * Overwrite destiny if exists
*/
$jsonArray = \BrunoNatali\File\Json\saveArray('\my\path\to\file.json', $dataArray, true);

/**
 * Adding paths to search desired write file
*/
$jsonArray = \BrunoNatali\File\Json\saveArray('file.json', $dataArray, true, '\my\path\one', '\my\path\two');

/**
 * Adding flags to PHP JSON function
*/
$jsonArray = \BrunoNatali\File\Json\saveArray('\my\path\to\file.json', $dataArray, JSON_PRETTY_PRINT | JSON_HEX_TAG);
```

## FileHandler
File handler was designed to be an stream file reader, but was not reviewed and not documented yet. 

## Install

The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This project follows [SemVer](https://semver.org/).
This will install the latest supported version:

```bash
$ composer require brunonatali/file:^0.1
```

This project aims to run on Linux and require other components and [inotify PHP extension](https://pecl.php.net/package/inotify), to work properly, follow each section instructions to get what you need.  
If you find a bug, please report.


## License

MIT, see [LICENSE file](LICENSE).
