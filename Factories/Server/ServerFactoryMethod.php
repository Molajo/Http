<?php
/**
 * Server Factory Method
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Factories\Server;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\FactoryInterface;
use CommonApi\IoC\FactoryBatchInterface;
use Molajo\IoC\FactoryMethod\Base as FactoryMethodBase;

/**
 * Server Factory Method
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ServerFactoryMethod extends FactoryMethodBase implements FactoryInterface, FactoryBatchInterface
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
        $options['product_namespace']        = 'Molajo\\Http\\Server';

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

        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function instantiateClass()
    {
        $class = $this->product_namespace;

        if (empty($_SERVER['PHP_AUTH_USER'])) {
            $user = '';
        } else {
            $user = $_SERVER['PHP_AUTH_USER'];
        }

        if (empty($_SERVER['PHP_AUTH_PW'])) {
            $password = '';
        } else {
            $password = $_SERVER['PHP_AUTH_PW'];
        }

        try {
            $this->product_result = new $class(
                $_SERVER,
                $user,
                $password
            );
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Server: Could not instantiate Handler: ' . $class
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
        $results = $this->product_result->get();

        $this->product_result = $results;
    }

    /**
     * Factory Method Controller requests any Products (other than the current product) to be saved
     *
     * @return  array
     * @since   1.0.0
     */
    public function setContainerEntries()
    {
        $this->dependencies['Runtimedata']->request->server = $this->sortObject($this->product_result);

        $this->set_container_entries['Runtimedata'] = $this->dependencies['Runtimedata'];

        return $this->set_container_entries;
    }
}
