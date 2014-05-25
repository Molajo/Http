<?php
/**
 * Http Request Path Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Request;

use stdClass;

/**
 * Http Request Path Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Path
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
     * Path
     *
     * @link     http://tools.ietf.org/html/rfc3986#section-3.3
     * @example  /over/there/index.php
     * @var      string
     * @since    1.0
     */
    protected $path = null;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0.0
     */
    protected $property_array
        = array(
            'path'
        );

    /**
     * Property Object
     *
     * @var    object
     * @since  1.0.0
     */
    protected $properties;

    /**
     * Construct
     *
     * @param   object $server_object
     *
     * @since   1.0
     */
    public function __construct($server_object)
    {
        $this->server_object = $server_object;
        $this->properties    = new stdClass();
    }

    /**
     * Process Request
     *
     * @return  $this
     * @since   1.0
     */
    public function set()
    {
        $this->setPath();

        foreach ($this->property_array as $key) {
            $this->properties->$key = $this->$key;
        }

        return $this->properties;
    }

    /**
     * Returns Path
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPath()
    {
        if (isset($this->server_object['ORIG_PATH_INFO'])) {
            $uri = $this->setPathOrigPathInfo();

        } else {
            $uri = $this->server_object['REQUEST_URI'];
        }

        $this->path = filter_var($uri, FILTER_SANITIZE_URL);

        $this->setPathCleanup();

        return $this;
    }

    /**
     * Set Path using server object ORIG_PATH_INFO (IIS 5 and PHP as CGI)
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPathOrigPathInfo()
    {
        $uri = $this->server_object['ORIG_PATH_INFO'];

        $query_string = '';
        if (isset($this->server_object['QUERY_STRING'])) {
            $query_string = $this->server_object['QUERY_STRING'];
        }

        if (trim($query_string) === '') {
        } else {
            $uri .= '?' . $this->server_object['QUERY_STRING'];
        }

        return $uri;
    }

    /**
     * Set Path using server object ORIG_PATH_INFO
     *
     * @return  $this
     * @since   1.0
     */
    protected function setPathCleanup()
    {
        if (strpos($this->path, '?')) {
            $this->path = substr($this->path, 0, strpos($this->path, '?'));
        }

        $this->path = ltrim($this->path, '/');

        $this->path = rtrim($this->path, '/');

        return $this;
    }
}
