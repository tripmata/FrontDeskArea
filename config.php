<?php
/**
 * @package Configuration 
 * @author Amadi Ifeanyi <amadiify.com>
 * 
 * Simple global configuration class
 */
class Configuration
{
    /**
     * @var string $mode 
     * Can either be development or live
     */
    const MODE = 'development';

    /**
     * @var string $host
     */
    const HOST = 'localhost';

    /**
     * @method Configuration url
     * @return object
     * 
     * Returns the default url configuration
     */
    public static function url() : object 
    {
        // @var array $config
        $config = [
            
            // development url configuration
            'development' => [
                'worker'    => 'http://localhost:8888/tripmata-suites/FrontDeskServices/worker.php',
                'page'      => 'http://localhost:8888/tripmata-suites/FrontDeskServices/page.php',
                'storage'   => 'http://localhost:8888/tripmata-suites/Storage/frontdesk/',
                'host'      => 'http://localhost:8888/tripmata-suites/FrontDeskArea/'
            ],

            // live url configuration
            'live' => [
                'worker'    => 'http://services.tripmata.net/FrontDeskServices/worker.php',
                'page'      => 'http://services.tripmata.net/FrontDeskServices/page.php',
                'storage'   => 'http://cdn.tripmata.net/frontdesk/',
                'host'      => 'http://frontdesk.tripmata.net/'
            ]
        ];

        // return config
        return self::getObject(self::getUrlConfigFromRequestHeader($config));
    }

    /**
     * @method Configuration getObject
     * @param array $config
     * @return object
     * 
     * Returns configuration as an object, conditioned to the default MODE
     */
    private static function getObject(array $config) : object 
    {
        // get mode
        $mode = Configuration::MODE;

        // read http_host
        if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], self::HOST) === false) $mode = 'live';

        // return configuration as an object
        return (object) (isset($config[$mode]) ? $config[$mode] : $config['development']);
    }

    /**
     * @method Configuration getUrlConfigFromRequestHeader
     * @param array $config
     * @return array
     * 
     * This method would check for configuration settings in the request header, and update the default configuration with it.
     */
    private static function getUrlConfigFromRequestHeader(array $config) : array
    {
        // get url configuration if sent via the request header
        if (function_exists('getallheaders')) :

            // get all headers
            $headers = getallheaders();

            // check for x-url-config
            if (isset($headers['x-url-config'])) :

                // read json data
                $jsonData = json_decode($headers['x-url-config']);

                // continue if it's an object
                if (is_object($jsonData) && isset($config[Configuration::MODE])) :

                    // get current config
                    $currentConfig = $config[Configuration::MODE];

                    // check for host
                    $currentConfig['worker'] = isset($jsonData->worker) ? $jsonData->worker : $currentConfig['worker'];

                    // check for storage
                    $currentConfig['page'] = isset($jsonData->page) ? $jsonData->page : $currentConfig['page'];

                    // set now
                    $config[Configuration::MODE] = $currentConfig;

                endif;

            endif;

        endif;

        // return array
        return $config;
    }
}