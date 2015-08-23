<?php
/**
 * Http Request Query Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Request;

use stdClass;

/**
 * Http Request Query Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 *
 * URI Syntax (RFC 3986) http://tools.ietf.org/html/rfc3986
 */
class Query
{
    /**
     * $server_object
     *
     * Injected copy of $_SERVER
     *
     * @var    object
     * @since  1.0.0
     */
    protected $server_object = null;

    /**
     * Query String
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.4
     * @example  type=animal&name=narwhal
     * @var      string
     * @since    1.0
     */
    protected $query = null;

    /**
     * Query Parameters
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.4
     * @example  array('type' => 'animal', 'name' => 'narwhal')
     * @var      array
     * @since    1.0
     */
    protected $parameters = array();

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'query',
            'parameters'
        );

    /**
     * Construct
     *
     * @param   object $server_object
     *
     * @since   1.0.0
     */
    public function __construct($server_object)
    {
        $this->server_object = $server_object;
    }

    /**
     * Process Request
     *
     * @return  stdClass
     * @since   1.0.0
     */
    public function set()
    {
        $this->setQueryParameters();
        $this->setQueryString();

        $query = new stdClass();
        foreach ($this->property_array as $key) {
            $query->$key = $this->$key;
        }

        return $query;
    }

    /**
     * Builds query parameters array with sorted key/value pairs
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setQueryParameters()
    {
        $query = $this->server_object['QUERY_STRING'];
        if ($query == '') {
            return $this;
        }

        $parameter_pairs = $this->extractQueryParameterPairs($query);
        if (count($parameter_pairs) > 0) {
        } else {
            return $this;
        }

        ksort($parameter_pairs);

        $this->parameters = $parameter_pairs;

        return $this;
    }

    /**
     * Extract the Parameter Pairs
     *
     * @param   string $query
     *
     * @return  array
     * @since   1.0.0
     */
    protected function extractQueryParameterPairs($query)
    {
        $parameter_pairs = array();

        $parts = explode("&", $query);

        if (is_array($parts) && count($parts) > 0) {
            foreach ($parts as $keyAndValue) {
                $pair                  = explode('=', $keyAndValue);
                $key                   = rawurlencode(urldecode($pair[0]));
                $value                 = rawurlencode(urldecode($pair[1]));
                $parameter_pairs[$key] = $value;
            }
        }

        return $parameter_pairs;
    }

    /**
     * Set normalized query string with sorted key/value pairs
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setQueryString()
    {
        $this->query = '';

        if (count($this->parameters) === 0) {
            return $this;
        }

        foreach ($this->parameters as $key => $value) {
            if ($this->query === '') {
            } else {
                $this->query .= '&';
            }
            $this->query .= $key . '=' . $value;
        }

        return $this;
    }
}
