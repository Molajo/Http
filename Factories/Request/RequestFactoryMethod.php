<?php
/**
 * Request Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Request;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Request Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class RequestFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0.0
     */
    public function __construct(array $options = array())
    {
        $options['product_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['product_namespace']        = 'Molajo\\Http\\Request';

        parent::__construct($options);
    }

    /**
     * Identify Class Dependencies for Constructor Injection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function setDependencies(array $reflection = array())
    {
        $this->reflection = null;

        $options                           = array();
        $this->dependencies['Runtimedata'] = $options;

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $class = $this->product_namespace;

        try {
            $this->product_result = new $class(
                $_SERVER
            );
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Request: Could not instantiate Handler: ' . $class
            );
        }

        return $this;
    }

    /**
     * Logic contained within this method is invoked after the class construction
     *  and can be used for setter logic or other post-construction processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterInstantiation()
    {
        $results = $this->product_result->get();

        $this->product_result = $results;
    }

    /**
     * Factory Method Controller requests any Products (other than the current product) to be saved
     *
     * @return  array
     * @since   1.0
     */
    public function setContainerEntries()
    {
        $this->dependencies['Runtimedata']->request->data = $this->product_result;

        $this->set_container_entries['Runtimedata'] = $this->dependencies['Runtimedata'];

        return $this->set_container_entries;
    }

    /**
     * Request for array of Factory Methods to be Scheduled
     *
     * @return  $this
     * @since   1.0
     */
    public function scheduleFactories()
    {
        $options              = array();
        $options['base_path'] = $this->base_path;

        $this->schedule_factory_methods['Client'] = $options;
        $this->schedule_factory_methods['Server'] = $options;
        $this->schedule_factory_methods['Site']   = $options;

        return $this->schedule_factory_methods;
    }
}
