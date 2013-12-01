<?php
/**
 * Response Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Response;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Resources Data Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResponseInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param  array $option
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Http\\Response';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the ServiceHandlerInterface
     * Retrieve a list of Interface dependencies and return the data ot the controller.
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $this->reflection = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        if (isset($this->options['version'])) {
            $version = '1.0';
        } else {
            $version = $this->options['version'];
        }

        if (isset($this->options['charset'])) {
            $charset = 'utf-8';
        } else {
            $charset = $this->options['charset'];
        }

        if (isset($this->options['status_code'])) {
            $status_code = 200;
        } else {
            $status_code = $this->options['status_code'];
        }

        if (isset($this->options['content_type'])) {
            $content_type = 'text/html';
        } else {
            $content_type = $this->options['content_type'];
        }

        if (isset($this->options['expires_date'])) {
            $expires_date = 'Fri, 14 Sep 2012 01:52:00 GMT';
        } else {
            $expires_date = $this->options['expires_date'];
        }

        if (isset($this->options['cachable'])) {
            $cachable = 0;
        } else {
            $cachable = $this->options['cachable'];
        }

        if (isset($this->options['language'])) {
            $language = 'en-GB';
        } else {
            $language = $this->options['language'];
        }

        if (isset($this->options['headers'])) {
            $headers = array();
        } else {
            $headers = $this->options['headers'];
        }

        if (isset($this->options['body'])) {
            $body = null;
        } else {
            $body = $this->options['body'];
        }

        $class = $this->options['service_namespace'];

        try {
            $this->service_instance = new $class(
                $version,
                $charset,
                $status_code,
                $content_type,
                $expires_date,
                $cachable,
                $language,
                $headers,
                $body
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Request: Could not instantiate Handler: ' . $class);
        }

        return $this;
    }
}
