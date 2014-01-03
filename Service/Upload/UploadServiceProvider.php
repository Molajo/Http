<?php
/**
 * Upload Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Upload;

use Exception;
use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Upload Service Provider
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class UploadServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
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
        $options['store_instance_indicator'] = true;
        $options['service_namespace']        = 'Molajo\\Http\\Upload';

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
        $this->dependencies                = array();
        $this->dependencies['Filesystem']  = array();
        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        if (isset($this->options['error_messages'])) {
            $error_messages = $this->options['error_messages'];
        } else {
            $error_messages = array(
                0   => 'File(s) were uploaded successfully.',
                1   => 'The uploaded file exceeds the upload_max_file_size directive in php.ini.',
                2   => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
                3   => 'The uploaded file was only partially uploaded.',
                4   => 'No file was uploaded.',
                6   => 'Upload failed. Missing a temporary folder.',
                7   => 'Upload failed. Unable to write file to disk.',
                100 => 'Unit of Measure must be KB, MB, GB invalid value: ',
                110 => '$input_field_name not in $_FILES super global: ',
                120 => '$_FILES $input_field_name tmp_name does not exist: ',
                130 => 'Mime type not allowed: ',
                140 => '$target_folder is not available: ',
                150 => '$target_file exists but $overwrite_existing_file is FALSE'
            );
        }

        if (isset($this->options['maximum_file_size'])) {
            $maximum_file_size = $this->options['maximum_file_size'];
        } else {
            $maximum_file_size = '2MB';
        }

        if (isset($this->options['allowable_mimes_and_extensions'])) {
            $allowable_mimes_and_extensions = $this->options['allowable_mimes_and_extensions'];
        } else {
            $allowable_mimes_and_extensions = array(
                'image/bmp'                     => 'bmp',
                'image/gif'                     => 'gif',
                'image/jpeg'                    => 'jpeg,jpg',
                'image/png'                     => 'png',
                'image/tiff'                    => 'tiff',
                'image/x-icon'                  => 'ico',
                'audio/midi'                    => 'mid,midi',
                'audio/mpeg'                    => 'mp2,mp3,mpga',
                'audio/wav'                     => 'wav',
                'audio/x-aiff'                  => 'aif,aifc,aiff',
                'audio/x-pn-realaudio-plugin'   => 'rpm',
                'audio/x-pn-realaudio'          => 'ram,rm',
                'audio/x-realaudio'             => 'ra',
                'audio/x-wav'                   => 'wav',
                'video/mpeg'                    => 'mpeg',
                'video/mpg'                     => 'mpg',
                'video/quicktime'               => 'mov,qt',
                'video/vnd.rn-realvideo'        => 'rv',
                'video/webm'                    => 'webm',
                'video/x-ms-wmv'                => 'wmv',
                'video/x-msvideo'               => 'avi',
                'application/pdf'               => 'pdf',
                'application/vnd.ms-excel'      => 'xls',
                'application/vnd.ms-powerpoint' => 'ppt',
                'application/msword'            => 'doc',
                'text/plain'                    => 'txt',
                'text/csv'                      => 'csv',
                'text/rtf'                      => 'rtf',
                'application/zip'               => 'zip',
                'application/x-tar'             => 'tar,tgz'
            );
        }
        if (isset($this->options['target_folder'])) {
            $target_folder = $this->options['target_folder'];
        } else {
            $target_folder = __DIR__;
        }

        if (isset($this->options['overwrite_existing_file'])) {
            $overwrite_existing_file = $this->options['overwrite_existing_file'];
        } else {
            $overwrite_existing_file = 0;
        }

        /** Typically, this is all the application will provide */
        if (isset($this->options['target_filename'])) {
            $target_filename = $this->options['target_filename'];
        } else {
            // throw exception
        }

        if (isset($this->options['input_field_name'])) {
            $input_field_name = $this->options['input_field_name'];
        } else {
            // throw exception
        }

        // Load the Super Global
        $file_array = $_FILES;

        try {
            $class                  = $this->service_namespace;
            $this->service_instance = $class(
                $error_messages,
                $maximum_file_size,
                $allowable_mimes_and_extensions,
                $target_folder,
                $target_filename,
                $input_field_name,
                $overwrite_existing_file,
                $file_array,
                $this->dependencies['Filesystem']
            );

            $this->service_instance->uploadFile();

        } catch (Exception $e) {

            throw new RuntimeException
            ('Http Upload Service Locator Instance Failed for ' . $this->service_namespace
            . ' failed.' . $e->getMessage());
        }

        return $this;
    }
}
