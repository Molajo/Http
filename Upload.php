<?php
/**
 * File Upload
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Http;

use Exception\Http\UploadException;
use CommonApi\Http\FileUploadInterface;
use CommonApi\Filesystem\FilesystemInterface;

/**
 * File Upload
 *
 * @package    Molajo
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class Upload implements FileUploadInterface
{
    /**
     * $file_array contains $_FILE superglobal contents
     *
     * Helps with Unit Testing to add a class property
     *
     * @var array
     */
    protected $file_array = array();

    /**
     * Error Messages
     *
     * From: http://php.net/manual/en/features.file-upload.errors.php
     *
     * @var    array
     * @since  1.0
     */
    protected $error_messages = array(
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

    /**
     * Allowable File Extensions and Mime Types
     *
     * @var    array
     * @since  1.0
     */
    protected $allowable_mimes_and_extensions = array(
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

    /**
     * Allowable File Extensions and Mime Types
     *
     * @var    array
     * @since  1.0
     */
    protected $not_used_allowable_mimes_and_extensions = array(
        'text/css'                      => 'css',
        'application/x-javascript'      => 'js',
        'application/x-shockwave-flash' => 'swf'
    );

    /**
     * Conversion from Unit of Measure to Bytes Table
     *
     * @var    array
     * @since  1.0
     */
    protected $units_of_measure = array(
        'KB' => 1024,
        'MB' => 1048576,
        'GB' => 1073741824
    );

    /**
     * Maximum File Size expressed in unit of measure
     *
     * @var    string
     * @since  1.0
     */
    protected $maximum_file_size = '2MB';

    /**
     * Maximum File Size calculated from previous
     *
     * @var    int
     * @since  1.0
     */
    protected $maximum_file_size_in_bytes = 2097152;

    /**
     * Input Field Name
     *
     * <input type="file" name="this_value" ... />
     *
     * @var     string
     * @since   1.0
     */
    protected $input_field_name = null;

    /**
     * Overwrite Existing File
     *
     * @var     boolean
     * @since   1.0
     */
    protected $overwrite_existing_file = false;

    /**
     * Target Folder
     *
     * @var     string
     * @since   1.0
     */
    protected $target_folder;

    /**
     * Target File
     *
     * @var     array
     * @since   1.0
     */
    protected $target_filename = array();

    /**
     * Filesystem, optional
     *
     * @var     object  FilesystemInterface
     * @since   1.0
     */
    protected $filesystem = null;

    /**
     * Property Array
     *
     * @var    array
     * @since  1.0
     */
    protected $property_array = array(
        'error_codes',
        'file_array',
        'filesystem',
        'allowable_mimes_and_extensions',
        'maximum_file_size',
        'target_folder',
        'target_filename',
        'input_field_name',
        'overwrite_existing_file'
    );

    /**
     * Constructor
     *
     * @param  array               $options
     * @param  FilesystemInterface $filesystem (optional)
     *
     * @since  1.0
     */
    public function __construct($options = array(), FilesystemInterface $filesystem = null)
    {
        if (is_array($options)) {
        } else {
            $options = array();
        }

        $this->filesystem = $filesystem;

        $this->file_array = $_FILES;

        if (count($options) > 0) {
            foreach ($this->property_array as $property) {
                if (isset($options[$property])) {
                    $this->$property = $options[$property];
                }
            }
        }
    }

    /**
     * Get the list of File Extensions associated with the Mime Type
     *
     * @param   string $mime_type
     *
     * @return  string
     * @since   1.0
     * @throws  UploadException
     */
    public function getType($mime_type)
    {
        $extensions = '';

        if (isset($this->allowable_mimes_and_extensions[$mime_type])) {
            $extensions = $this->allowable_mimes_and_extensions[$mime_type];
        }

        return $extensions;
    }

    /**
     * Add Valid Mime Type and File Extension Entry to List
     *
     * @param   string $mime_type
     * @param   string $extension
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    public function addType($mime_type, $extension)
    {
        $extensions = '';

        if (isset($this->allowable_mimes_and_extensions[$mime_type])) {
            $extensions = $this->allowable_mimes_and_extensions[$mime_type];
        }

        if ($extensions == '') {
            $temp = array();
        } else {
            $temp = explode(',', $extensions);
        }

        $temp[] = trim($extension);
        sort($temp);
        array_unique($temp);

        if (count($temp) == 1) {
            $extensions = $temp[0];
        } else {
            $extensions = implode(',', $temp);
        }

        $this->allowable_mimes_and_extensions[$mime_type] = $extensions;

        return $this->allowable_mimes_and_extensions;
    }

    /**
     * Remove Mime Type from List of Valid Values
     *
     * @param   string $mime_type
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    public function removeType($mime_type)
    {
        if (isset($this->allowable_mimes_and_extensions[$mime_type])) {
            unset($this->allowable_mimes_and_extensions[$mime_type]);
        }

        return $this;
    }

    /**
     * Set the maximum file size for upload
     *
     * @param   string $maximum_file_size
     *
     * @return  integer
     * @since   1.0
     * @throws  UploadException
     */
    public function setMaxFileSize($maximum_file_size)
    {
        $unit_of_measure = substr($maximum_file_size, strlen($maximum_file_size - 2), 2);

        if ($unit_of_measure == 'KB'
            || $unit_of_measure == 'MB'
            || $unit_of_measure == 'GB'
        ) {
        } else {
            throw new UploadException ($this->error_messages[100] . $unit_of_measure);
        }

        $measure                          = (int)substr($maximum_file_size, 0, strlen($maximum_file_size) - 2);
        $this->maximum_file_size_in_bytes = $measure * $this->units_of_measure[$unit_of_measure];

        return $this->maximum_file_size_in_bytes;
    }

    /**
     * Set the target folder
     *
     * @param   string $target_folder
     *
     * @return  $this
     * @since   1.0
     */
    public function setTargetFolder($target_folder)
    {
        $this->target_folder = $target_folder;

        return $this;
    }

    /**
     * Set Input Field Name
     *
     * @param   string $input_field_name
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    public function setInputFieldName($input_field_name)
    {
        $this->input_field_name = $input_field_name;

        return $this;
    }

    /**
     * Set Overwrite Existing File
     *
     * @param   boolean $overwrite_existing_file
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    public function setOverwriteExistingFile($overwrite_existing_file)
    {
        if ((boolean)$overwrite_existing_file === true) {
            $this->overwrite_existing_file = true;
        } else {
            $this->overwrite_existing_file = false;
        }

        return $this;
    }

    /**
     * Upload File(s)
     *
     * @param   null|string|array $target_filename
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    public function upload($target_filename = null)
    {
        if ($target_filename === null) {
        } else {
            $this->target_filename = $target_filename;
        }

        /**
         *  Pre file processing
         */
        $this->validateFormToken();

        $this->validateInputFieldName();

        $this->createFileArray($target_filename);

        $this->validateTargetFolder();

        /**
         *  File processing
         */
        foreach ($this->file_array as $item) {

            $upload_path_and_file = $item['tmp_name'];

            if ($item['error'] === 0) {
            } else {
                throw new UploadException($item['error']);
            }

            $this->validateUploadFileExists($upload_path_and_file);

            $this->validateMimeType($upload_path_and_file);

            if ($target_filename === null) {
            } else {
                $this->target_filename = $target_filename;
            }

            $upload_file     = basename($upload_path_and_file);
            $target_filename = $item['target_filename'];

            if ($target_filename === null
                || $target_filename == ''
            ) {
                $target_path_and_file = $this->target_folder . '/' . $upload_file;
            } else {
                $target_path_and_file = $this->target_folder . '/' . $target_filename;
            }

            $this->validateTargetFile($target_path_and_file);

            if (is_object($this->filesystem)) {
                $data = file_get_contents($upload_path_and_file);
                $this->filesystem->write($target_path_and_file, $data, $this->overwrite_existing_file);
            } else {
                copy($upload_path_and_file, $target_path_and_file);
            }

            unlink($upload_path_and_file);
        }

        return $this;
    }

    /**
     * Validate Form Token
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    protected function validateFormToken()
    {
        return $this;

        try {
            call_user_func_array(array(__NAMESPACE__ . '\Foo', 'test'), array('Philip'));
        } catch (Exception $e) {
            throw new UploadException($this->error_messages[105] . $e->getMessage());
        }

        return $this;
    }

    /**
     * Validate Upload File is available in the $_FILE super global
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    protected function validateInputFieldName()
    {
        if (isset($this->file_array[$this->input_field_name])) {
        } else {
            throw new UploadException($this->error_messages[110] . $this->input_field_name);
        }

        return $this;
    }

    /**
     * Create File Array takes the $_POST madness and turns it into something normal
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    function createFileArray()
    {
        $raw = $this->file_array[$this->input_field_name];

        $this->file_array = array();

        /** single file */
        if (isset($raw['name'])) {

            $this->file_array[0]['name']     = $raw['name'];
            $this->file_array[0]['type']     = $raw['type'];
            $this->file_array[0]['tmp_name'] = $raw['tmp_name'];
            $this->file_array[0]['error']    = $raw['error'];
            $this->file_array[0]['size']     = $raw['size'];

            if (is_array($this->target_filename)) {
                $this->file_array[0]['target_filename'] = $this->target_filename[0];
            } else {
                $this->file_array[0]['target_filename'] = $this->target_filename;
            }
        } else {

            /** multiple files */
            $count = 0;
            foreach ($raw as $files_element => $files_raw) {
                $i = 0;

                foreach ($files_raw as $item => $value) {
                    $this->file_array[$i ++][$files_element] = $value;
                }

                if (is_array($this->target_filename)
                    && isset($this->target_filename[$count])
                ) {
                    $this->file_array[0]['target_filename'] = $this->target_filename[0];
                } else {
                    $this->file_array[0]['target_filename'] = null;
                }

                $count ++;
            }
        }

        return $this;
    }

    /**
     * Validate Target Directory
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    protected function validateTargetFolder()
    {
        if (is_object($this->filesystem)) {
            $true_or_false = $this->filesystem->exists($this->target_folder);
        } else {
            $true_or_false = is_dir($this->target_folder);
        }

        if ($true_or_false === true) {
        } else {
            throw new UploadException($this->error_messages[140] . $this->target_folder);
        }

        return $this;
    }

    /**
     * Validate Upload File from $_FILE super global exists
     *
     * @param   string $upload_path_and_file
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    protected function validateUploadFileExists($upload_path_and_file)
    {
        if (file_exists($upload_path_and_file)) {
        } else {
            throw new UploadException($this->error_messages[120] . $upload_path_and_file);
        }

        return $this;
    }

    /**
     * Validate Mime Type
     *
     * @param   string $upload_path_and_file
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    protected function validateMimeType($upload_path_and_file)
    {
        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $upload_path_and_file);

        if (isset($this->allowable_mimes_and_extensions[$mime_type])) {
        } else {
            throw new UploadException($this->error_messages[130] . $mime_type);
        }

        return $this;
    }

    /**
     * Validate Target Directory
     *
     * @param   string $target_path_and_file
     *
     * @return  $this
     * @since   1.0
     * @throws  UploadException
     */
    protected function validateTargetFile($target_path_and_file)
    {
        if (is_object($this->filesystem)) {
            $true_or_false = $this->filesystem->exists($target_path_and_file);
        } else {
            $true_or_false = file_exists($target_path_and_file);
        }

        if ($true_or_false === true && $this->overwrite_existing_file === false) {
            throw new UploadException($this->error_messages[150] . $target_path_and_file);
        }

        return $this;
    }
}
