<?php
/*
Copyright 2002-2012 MarkLogic Corporation.  All Rights Reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
namespace MarkLogic\MLPHP;

use Psr\Log\NullLogger;

/**
 * MLPHP Global State
 *
 * @package MLPHP
 * @author Eric Bloch <eric.bloch@gmail.com>
 * @author Mike Wooldridge <mike.wooldridge@marklogic.com>
 */
class MLPHP
{
    /**
     *  Array of configuration parameters used to create clients and REST APIs.
     *  @var mixed[]
     *  @see MLPHP#__construct
     */
    public $config = array();

    /**
     * Constructor, used to set configuration parameters.
     *
     * @param array config configuration settings, including
     * <pre> <br/>
     * host - defaults to '127.0.0.1'<br/>
     * port - defaults to 7009<br/>
     * managePort - default to 8002<br/>
     * api - name of REST API, defaults to 'mlphp-rest-api'
     * username - defaults to 'admin'<br/>
     * password - defaults 'admin'<br/>
     * auth - 'digest' or 'basic', defaults to 'digest'<br/>
     * path - defaults to ''<br/>
     * version - defaults to 'v1'<br/>
     * logger - defaults to Psr\Log\NullLogger
     * </pre>
     *
     * Additional array members are ignored.
     *
     */
    public function __construct($config)
    {
        $this->config = array_merge(array(
            'host' => '127.0.0.1',
            'port' => 7009,
            'managePort' => 8002,
            'api' => 'mlphp-rest-api',
            'db' => 'mlphp-db',
            'user' => 'admin',
            'password' => 'admin',
            'path' => '',
            'managePath' => 'manage',
            'version' => 'v1',
            'manageVersion' => 'v2',
            'auth' => 'digest',
            'logger' => new NullLogger()
        ), $config);

    }

    /**
     * Merge the passed in config parameters.  Only affects future clients.
     *
     * @param config
     */
    public function mergeConfig($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Return a REST client based on current configuration.
     *
     * @return RESTClient
     */
    public function newClient()
    {
        return new MLPHP\RESTClient(
            $this->config['host'],
            $this->config['port'],
            $this->config['path'],
            $this->config['version'],
            $this->config['username'],
            $this->config['password'],
            $this->config['auth'],
            $this->config['logger']
        );
    }

    /**
     * Return a REST client to the management API.
     *
     * @return RESTClient
     */
    public function newManageClient()
    {
        return new MLPHP\RESTClient(
            $this->config['host'],
            $this->config['managePort'],
            $this->config['managePath'],
            $this->config['manageVersion'],
            $this->config['username'],
            $this->config['password'],
            $this->config['auth'],
            $this->config['logger']
        );
    }

    /**
     * Create a REST API based on current configuration.
     */
    public function newAPI()
    {
        $client = new MLPHP\RESTClient(
            $this->config['host'],
            $this->config['managePort'],
            $this->config['path'],
            $this->config['version'],
            $this->config['username'],
            $this->config['password'],
            $this->config['auth'],
            $this->config['logger']
        );

        $api = new MLPHP\RESTAPI(
            $this->config['api'],
            $this->config['host'],
            $this->config['db'],
            $this->config['port'],
            $this->config['username'],
            $this->config['password']
        );

        $api->post($client);
    }

    /**
     * PSR-0 autoloader.
     *
     * Do NOT use if you are using Composer to autoload dependencies.
     *
     * @param $className
     */
    public static function autoload($className)
    {
        $thisClass = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);

        $baseDir = __DIR__;

        if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
            $baseDir = substr($baseDir, 0, -strlen($thisClass));
        }

        $className = ltrim($className, '\\');
           // echo "class " . $className;
           // echo '<br/>';
        $fileName  = (dirname($baseDir)) . DIRECTORY_SEPARATOR;
                // echo "file " . $fileName;
                // echo '<br/>';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
               // echo "ns " . $namespace;
               // echo '<br/>';
            $className = substr($className, $lastNsPos + 1);
               // echo "class " . $className;
               // echo '<br/>';
            $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
               // echo "file " . $fileName;
               // echo '<br/>';
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

           // echo "FILE: " . $fileName;

        if (file_exists($fileName)) {
            require $fileName;
        }
    }

    /**
     * Register PSR-0 autoloader.
     *
     * Do NOT use if you are using Composer to
     * autoload dependencies.
     */
    public static function registerAutoloader()
    {
        $n = __NAMESPACE__; // workaround for bug in phpdoctor
        spl_autoload_register($n . '\\MLPHP::autoload');
    }
}