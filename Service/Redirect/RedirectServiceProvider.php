<?php
/**
 * Redirect Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Redirect;

use Exception;
use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Redirect Service Provider
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class RedirectServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_name']      = basename(__DIR__);
        $options['service_namespace'] = 'Molajo\\Http\\Response';

        parent::__construct($options);
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the ServiceProviderInterface
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
        if (isset($this->options['timezone'])) {
            $timezone = $this->options['timezone'];
        } else {
            $timezone = 'UTC';
        }

        if (isset($this->options['url'])) {
            $url = $this->options['url'];
        } else {
            throw new RuntimeException('Redirect Service: No Redirect URL provided');
        }

        if (isset($this->options['status'])) {
            $status = $this->options['status'];
        } else {
            $status = 302;
        }

        if (isset($this->options['body'])) {
            $body = $this->options['body'];
        } else {
            $body = '';
        }

        $headers = array(
            'Location' => $url,
            'Status'   => $status
        );

        $class = $this->options['service_namespace'];

        try {
            $this->service_instance = new $class(
                $timezone,
                $headers,
                $body
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Redirect: Could not instantiate Handler: ' . $class);
        }

        return $this;
    }
}
