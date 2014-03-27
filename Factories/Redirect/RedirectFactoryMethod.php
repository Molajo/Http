<?php
/**
 * Redirect Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Redirect;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethodBase;

/**
 * Redirect Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class RedirectFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
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
        $options['product_name']      = basename(__DIR__);
        $options['product_namespace'] = 'Molajo\\Http\\Redirect';

        parent::__construct($options);
    }

    /**
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
    public function instantiateClass()
    {
        if (isset($this->options['timezone'])) {
            $timezone = $this->options['timezone'];
        } else {
            $timezone = 'UTC';
        }

        if (isset($this->options['url'])) {
            $url = $this->options['url'];
        } else {
            throw new RuntimeException('Redirect Factory: No Redirect URL provided');
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

        $class = $this->product_namespace;

        try {
            $this->product_result = new $class(
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
