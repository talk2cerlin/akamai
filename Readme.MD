## Netstorage API library package for Akamai

#### NOTE: This package is based on [Akamai plugin by Raben](https://github.com/raben/Akamai/). So credits goes to him. I have just packaged it so it will be easy for the developers to make API calls.

### Sample `.env.akamai`

    AKAMAI_HOST="Your Netstorage Host"
    AKAMAI_KEY="Your Netstorage Key"
    AKAMAI_KEYNAME="Your Netstorage Key Name"

### Explanation:
This package has two facade classes

##### 1) Akamai\Facades\Akamai

        Methods:
            1. Akamai::upload($ftp_url, $body); // To upload the data to server
            2. Akamai::dir($ftp_url); // To list the directories and files in a given ftp path
            3. Akamai::download($ftp_url); // To download the file contents from a given ftp path
            4. Akamai::delete($ftp_url); // To delete a file from a given ftp path
            5. Akamai::rmdir($ftp_url); // To remove directory from a given ftp path. [CAUTION: Directory must be empty else the call will fail]
        
##### 2) Akamai\Facades\Config
       
        Configuration loader internally uses a dov env parser to parse the credentials.
        By default this package searches for .env.akamai file in root directory of your project.
        This config class has two public methods

        /**
            Used to override the settings with different env file. Shoule be called before calling any methods from Akamai facade
            $path - path where the .env file is located. Defaults to package root directory
            $name - can be used if the file name is different than .env [like .akamai]. Defaults to ".env" name
        **/
        Config::loadFromENV($path, $name);
        
### Usage sample

    <?php

    require_once __DIR__ . "/vendor/autoload.php";

    use Akamai\Facades\Config;
    use Akamai\Facades\Akamai;

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

    } catch (Exception $e) {
        echo $e->getMessage();
    }