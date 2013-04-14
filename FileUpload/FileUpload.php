<?php
/**
 * Http FileUpload
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   MIT
 */
namespace Molajo\Http\FileUpload;

defined('MOLAJO') or die;

use Exception;
use Molajo\Http\FileUpload\Exception\FileUploadException;

use Molajo\Http\FileUpload\Api\FileUploadInterface;

/**
 * Http FileUpload Adapter
 *
 * @package   Molajo
 * @license   MIT
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
0 */
class FileUpload implements FileUploadInterface
{
    /**
     * Content Type
     *
     * @var    string
     * @since  1.0
     */
    protected $content_type = null;

    /**
     * File Exists
     *
     * @var    bool
     * @since  1.0
     */
    protected $file_exists = false;

    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Size
     *
     * @var    int
     * @since  1.0
     */
    protected $size = 0;

    /**
     * Temporary File Location
     *
     * @var    string
     * @since  1.0
     */
    protected $temporary_file_location = null;

    /**
     * Files
     *
     * @var    array
     * @since  1.0
     */
    protected $files = array();

    /**
     * List of Properties
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'content_type',
        'file_exists',
        'name',
        'size',
        'temporary_file_location'
    );

    /**
     * __construct
     *
     * @since   1.0
     */
    public function __construct()
    {
        $this->exists();

        $this->getFile();

        return $this;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     * @since   1.0
     * @throws  FileUploadException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if ($key == '*') {

            $results = array();

            foreach ($this->property_array as $key) {
                $results[$key] = $this->$key;
            }

            return null;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new FileUploadException('FileUpload Service: is attempting to get value for unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of a specified key
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     * @since   1.0
     * @throws  FileUploadException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new FileUploadException('FileUpload Service: is attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Has a file(s) been uploaded?
     *
     * @return  object
     * @since   1.0
     */
    protected function exists()
    {
        return $this;
    }

    /**
     * Retrieve the file(s) that have been uploaded
     *
     * @return void
     * @since   1.0
     */
    protected function getFile()
    {
        // FILES and create FileUpload objects

        $this->input = file_get_contents('php://input');

        return;
    }
}
