## Netstorage API library package for Akamai

This package follows [facade design pattern](https://en.wikipedia.org/wiki/Facade_pattern) (Click on the link to read more on this). 

### Features:

1. Upload a file to Akamai Netstorage
2. List the files and directories of a given directory
3. Download a file
4. Delete a file
5. Remove a directory (Directory must be empty to remove)
6. Generate Video Auth token for playback

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

##### Methods:
###### 1) Akamai::upload($directory_path, $body);

This method is used to upload a file to Akamai.

    /*
        @param: $akamai_path - File location with file name and extension to which the file should be uploaded. ex: /cpcode/folder/path/file.mp4
        @param: $file_path - Path to the file to be written.
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 2) Akamai::dir($directory_path);

This method is used to list the files in a particular directory.

    /*
        @param: $akamai_path - Directory path to be listed. ex: /cpcode/folder/path
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 3) Akamai::download($file_path);

This method is to download a file

    /*
        @param: $akamai_path - File location with file name and extension. ex: /cpcode/folder/path/file.mp4.
        @return: Array - ["data" => API response (contents of file on success), "code" => API response status code]
    */

###### 4) Akamai::delete($file_path);

This method is to delete a file

    /*
        @param: $akamai_path - File location with file name and extension. ex: /cpcode/folder/path/file.mp4.
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 5) Akamai::rmdir($directory_path);

This method is to delete a directory. [CAUTION: Directory has to be empty. (ie, list and delete all files individually before performing this API call)].

    /*
        @param: $akamai_path - Directory path to be deleted. ex: /cpcode/folder/path
        @return: Array - ["data" => API response, "code" => API response status code]
    */

###### 6) Akamai::generateToken($duration, $type);
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
        $xml = Akamai::dir('/cpcode/directory/to/list');
        var_dump($xml);
        /**
            <?xml version="1.0" encoding="ISO-8859-1"?>
                <stat directory="/cpcode/directory/path">
                    <file type="file" name="filename.mp4" mtime="1234567890" size="1024" md5="f0qcf8xkl6y7p9mo5zror7bpmtiq392d"/>
                </stat>
        **/

        $uploadResponse = Akamai::upload('/cpcode/path/to/file/testing.mp4', file_get_contents('./testing.mp4'));
        var_dump($uploadResponse);
        /**
            ['data' => "could be string or xml",'code' => 200];
        **/
        
        $filedata = Akamai::download('/cpcode/path/to/file/testing.mp4');
        file_put_contents('./testing.mp4', $filedata);

        $deleteResponse = Akamai::delete('/cpcode/path/to/file/testing.mp4');
        var_dump($deleteResponse);
        /**
            ['data' => "could be string or xml",'code' => 200];
        **/

        $rmdirResponse = Akamai::rmdir('/cpcode/path/to/file/');
        var_dump($rmdirResponse);
        /**
            ['data' => "could be string or xml",'code' => 200];
        **/

        $token = Token::generateVideoToken(100, "hdnts");
        var_dump($token);
        /**
            hdnts=st=1470130755~exp=1470130855~acl=*~hmac=99665898830y435t37487889hl672055956t9p7455e14g765c056i413u433125;
        **/

    } catch (Exception $e) {
        echo $e->getMessage();
    }


###Change Log:
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