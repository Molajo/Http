<?php
/**
 * Response Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Response;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Response Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ResponseFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Constructor
     *
     * @param  array $option
     *
     * @since  1.0.0
     */
    public function __construct(array $options = array())
    {
        $options['product_namespace']        = 'Molajo\\Http\\Response';
        $options['store_instance_indicator'] = true;
        $options['product_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Retrieve a list of Interface dependencies and return the data ot the controller.
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = array())
    {
        $this->reflection = array();

        $this->dependencies['rendered_page'] = array();
        $this->dependencies['Runtimedata']   = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this$this->rendered_page
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        if (isset($this->options['timezone'])) {
            $timezone = $this->options['timezone'];
        } else {
            $timezone = 'UTC';
        }

        if (isset($this->options['body'])) {
            $body = $this->options['body'];
        } else {
            $body = $this->dependencies['rendered_page'];
        }

        $headers = array();

        $class = $this->product_namespace;

        try {
            $this->product_result = new $class(
                $timezone,
                $headers,
                $body
            );
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Redirect: Could not instantiate Handler: ' . $class
            );
        }

        return $this;
    }

    /**
     * Logic contained within this method is invoked after the class construction
     *  and can be used for setter logic or other post-construction processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterInstantiation()
    {
        $this->product_result->send();

        return $this;
    }
}
