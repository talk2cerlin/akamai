## Netstorage API library package for Akamai

#### NOTE: This package is based on [Akamai plugin by Raben](https://github.com/raben/Akamai/). So credits goes to him. I have just packaged it so it will be easy for the developers to make API calls.

### Explanation:
This package has two facade classes

1) Akamai\Facades\Akamai

        Methods:
            1. Akamai::upload($url, $body);
            2. Akamai::dir($url);
            3. Akamai::download($url);
            4. Akamai::delete($url);
            5. Akamai::rmdir($url);
        
2) Akamai\Facades\Config
       
       Configuration loader which internally uses a dov env parser to parse the credentials.
        By default this package searches for .env.akamai files in root directory of this package for credentials.
        This config class is provided to tell the system to load a different environment file.
        It has two public methods

        /**
            $path - path where the .env file is located. Defaults to package root directory
            $name - can be used if the file name is different than .env [like .akamai]. Defaults to ".env" name
        **/
        Config::loadFromENV($path, $name);



