<?php
/**
 * Redirect Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Redirect;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Redirect Service Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class RedirectInjector extends AbstractInjector implements ServiceHandlerInterface
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
        $options['service_name']             = basename(__DIR__);
        $options['service_namespace']        = 'Molajo\\Http\\Redirect';

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
        $class = $this->options['service_namespace'];

        if (isset($this->options['redirect_to'])) {
        } else {
            throw new RuntimeException('Redirect Service: No Redirect URL provided');
        }

        if (isset($this->options['redirect_status_code'])) {
        } else {
            $this->options['redirect_status_code'] = 302;
        }

        try {
            $this->service_instance = new $class(
                $this->options['redirect_to'],
                $this->options['redirect_status_code']
            );
        } catch (Exception $e) {
            throw new RuntimeException
            ('Redirect: Could not instantiate Handler: ' . $class);
        }

        return $this;
    }
}
