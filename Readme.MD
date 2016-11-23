## Netstorage API library package for Akamai

This package follows [facade design pattern](https://en.wikipedia.org/wiki/Facade_pattern) (Click on the link to read more on this). 

### Features:

1. Upload a file to Akamai Netstorage
2. List the files and directories of a given directory
3. Download a file
4. Delete a file
5. Rename a file
6. Remove a directory (Directory must be empty to remove)
7. Create a new directory
8. Generate Video Auth token for playback

### Sample `.env.akamai`

    AKAMAI_HOST="Your Netstorage Host"
    AKAMAI_KEY="Your Netstorage Key"
    AKAMAI_KEYNAME="Your Netstorage Key Name"
    AKAMAI_VIDEO_TOKEN="Your Video Token"

### Explanation:
This package has three facade classes

#### 1) Akamai\Facades\Config
##### Introduction:
`Config` facade is used to load/set the credentials which are used for API requests

##### Methods:
###### 1) Config::loadFromENV($path, $name);

This method is used to load the credentials from an `env` file. This method will try to load all four env variables which are mentioned in the sample `.env.akamai` file.

    /*
        @param: $path - Path of the env file. Default is current working directory.
        @param: $name - Name of the env file to load. Default is `.env.akamai`.
        @return: Array - Loaded configuration.
        @throws: Akamai\Exceptions\DotEnvException.
    */

###### 2) Config::setAkamaiConfig($config)

This method is to set the credentials in runtime. Will be usefull when the credentials are stored in database or any other mode of storage.
 
    /*
        @param: $config - Configuration to be updated.
        @return: Array - Loaded configuration.
        @throws: Akamai\Exceptions\InvalidDataTypeFoundException;
    */

###### 3) Config::getAkamaiConfig()
This method is to get the credentials in runtime.

    /*
        @return: Array - Loaded configuration.
    */

#### 2) Akamai\Facades\Akamai
##### Introduction:
`Akamai` facade is used to do the basic operations like upload, download etc.
All API operations return responses as an array with fields for "data" and "code".

    /*
        [
            "data" => API response (simple string or xml string),
            "code" => API response status code
        ]
    */

For a list of all possible status codes, see https://control.akamai.com/dl/customers/NS/NS_http_api_FS.pdf

##### Methods:
###### 1) Akamai::upload($akamai_path, $file_path);

This method is used to upload a file to Akamai.

    /*
        @param: $akamai_path - File location with file name and extension to which the file should be uploaded. ex: /cpcode/folder/path/file.mp4
        @param: $file_path - Path to the file to be written.
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 2) Akamai::dir($akamai_path);

This method is used to list the files in a particular directory.

    /*
        @param: $akamai_path - Directory path to be listed. ex: /cpcode/folder/path
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 3) Akamai::download($akamai_path);

This method is to download a file

    /*
        @param: $akamai_path - File location with file name and extension. ex: /cpcode/folder/path/file.mp4
        @return: Array - ["data" => API response (contents of file on success), "code" => API response status code]
    */

###### 4) Akamai::delete($akamai_path);

This method is to delete a file

    /*
        @param: $akamai_path - File location with file name and extension. ex: /cpcode/folder/path/file.mp4
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 5) Akamai::rename($old_akamai_path, $new_akamai_path);

This method is to rename a file

    /*
        @param: $old_akamai_path - Existing file location with file name and extension. ex: /cpcode/folder/path/file.mp4
        @param: $new_akamai_path - Desired file location with file name and extension. ex: /cpcode/new_folder/new_path/new_file.mp4
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 6) Akamai::rmdir($akamai_path);

This method is to delete a directory. [CAUTION: Directory has to be empty. (ie, list and delete all files individually before performing this API call)].

    /*
        @param: $akamai_path - Directory path to be deleted. ex: /cpcode/folder/path
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 7) Akamai::mkdir($akamai_path);

This method is to create a directory.

    /*
        @param: $akamai_path - Directory path to be added. ex: /cpcode/folder/path
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 8) Akamai::generateToken($duration, $type);
[DEPRECATED] Please use Token facade to generate video tokens

This method is to generate video authentication token.

    /*
        @param: $duration - Time to live (ttl) of that token. (In seconds)
        @param: $type - It should be `hdnts` or `hdnea`.
        @return: String - Final token which has to be appended with the m3u8 request.
    */

#### 3) Akamai\Facades\Token
##### Introduction:
`Token` facade is used to generate video auth tokens.

##### Methods:
###### 1) Token::generateVideoToken($duration, $type);

This method is to generate video authentication token.

    /*
        @param: $duration - Time to live (ttl) of that token. (In seconds)
        @param: $type - It should be `hdnts` or `hdnea`.
        @return: String - Final token which has to be appended with the m3u8 request.
    */

<hr />

        
### Usage sample
```php
<?php

require_once __DIR__ . "/vendor/autoload.php";

use Akamai\Facades\Config;
use Akamai\Facades\Akamai;
use Akamai\Facades\Token;

// For loading config info from different env file. Can be used to change credentials on run time. This method overwrites the existing config with latest one.
Config::loadFromENV('./', '.akamai');

// Getting the akamai config in runtime
$config = Config::getAkamaiConfig();

try {
    /**
     * @var array
     * Example: [
     *              "data" =>
     *                  "<?xml version="1.0" encoding="ISO-8859-1"?>
     *                      <stat directory="/cpcode/directory/path">
     *                          <file type="file" name="filename.mp4" mtime="1234567890" size="1024" md5="f0qcf8xkl6y7p9mo5zror7bpmtiq392d"/>
     *                      </stat>"
     *               "code" => 200
     *           ]
     */
    $dirResponse = Akamai::dir('/cpcode/directory/to/list');

    /** @var array */
    $uploadResponse = Akamai::upload('/cpcode/path/to/file/testing.mp4', './testing.mp4');
    /** @var string */
    $uploadResponseData = $uploadResponse["data"];
    /** @var int */
    $uploadResponseCode = $uploadResponse["code"];

    /** @var array */
    $downloadResponse = Akamai::download('/cpcode/path/to/file/testing.mp4');
    if ($downloadReponse["code"] === 200) {
        file_put_contents('./testing.mp4', $downloadResponse["data"]);
    } else {
        // perform some error handling based on status code
    }

    /** @var array - ['data' => "could be simple string or xml string",'code' => 200] */
    $renameResponse = Akamai::rename('/cpcode/path/to/file/testing.mp4', '/cpcode/new_path/to/file/new_testing.mp4');

    /** @var array - ['data' => "could be simple string or xml string",'code' => 200] */
    $deleteResponse = Akamai::delete('/cpcode/newpath/to/file/new_testing.mp4');

    /** @var array - ['data' => "could be simple string or xml string",'code' => 200] */
    $mkdirResponse = Akamai::mkdir('/cpcode/new_dir');

    /** @var array - ['data' => "could be simple string or xml string",'code' => 200] */
    $rmdirResponse = Akamai::rmdir('/cpcode/new_dir');

    $token = Token::generateVideoToken(100, "hdnts");
    var_dump($token);
    /**
     * hdnts=st=1470130755~exp=1470130855~acl=*~hmac=99665898830y435t37487889hl672055956t9p7455e14g765c056i413u433125;
     */

} catch (Exception $e) {
    echo $e->getMessage();
}
```

###Change Log:

####v3.1.0 Thanks to [Julian Griggs](https://github.com/JulianGriggs)
1. New mkdir and rename functions

####v3.0.0 Thanks to [Julian Griggs](https://github.com/JulianGriggs)
1. Standardization of API
2. Refactor
3. Document update
4. Removed unwanted test

####v2.0.1 Thanks to [Julian Griggs](https://github.com/JulianGriggs)
1. Added missing return statement for `delete` method.

####v2.0.0
1. New `Token` facade
2. Code refactor
3. Bug fixes
4. Deprecated `Akamai::generateToken($duration, $type)`

####v1.1.1
1. Issue fix
2. Document update

####v1.1.0
1. Added video token api for generating tokens for video based on duration.

####v1.0.3
1. Code optimization

####v1.0.2
1. Issue fixes

####v1.0.1
1. Issue fixes

####v1.0.0
1. Initial version with support for upload, list directory, download, remove directory and delete

#### NOTE: This package is based on [Akamai plugin by Raben](https://github.com/raben/Akamai/). So credits goes to him. I have added Video auth token with this and packaged it.