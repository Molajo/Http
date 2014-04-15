<?php
/**
 * File Upload
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Http;

use CommonApi\Http\UploadInterface;
use CommonApi\Exception\RuntimeException;
use CommonApi\Filesystem\FilesystemInterface;

/**
 * File Upload
 *
 * @package    Molajo
 * @license    MIT
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Upload implements UploadInterface
{
    /**
     * $file_array contains a copy of the $_FILE super global
     *
     * @since  1.0
     */
    protected $file_array = array();

    /**
     * Error Messages
     *
     * @link   http://php.net/manual/en/features.file-upload.errors.php
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
     * @var     object  CommonApi\Filesystem\FilesystemInterface
     * @since   1.0
     */
    protected $filesystem = null;

    /**
     * Constructor
     *
     * @param string              $input_field_name
     * @param string              $target_folder
     * @param string              $target_filename
     * @param int                 $overwrite_existing_file
     * @param array               $files
     * @param array               $error_messages
     * @param string              $maximum_file_size
     * @param array               $allowable_mimes_and_extensions
     * @param FilesystemInterface $filesystem
     *
     * @since  1.0
     */
    public function __construct(
        $input_field_name,
        $target_folder,
        $target_filename,
        $overwrite_existing_file,
        array $files = array(),
        array $error_messages = array(),
        $maximum_file_size = '2MB',
        array $allowable_mimes_and_extensions = array(),
        FilesystemInterface $filesystem = null
    ) {
        $this->target_folder           = $target_folder;
        $this->target_filename         = $target_filename;
        $this->input_field_name        = $input_field_name;
        $this->overwrite_existing_file = $overwrite_existing_file;
        $this->file_array              = $files;

        if (count($this->error_messages) > 0) {
            $this->error_messages = $error_messages;
        }

        if (trim((string)$maximum_file_size) === '') {
        } else {
            $this->maximum_file_size = $maximum_file_size;
        }

        if (count($allowable_mimes_and_extensions) > 0) {
            $this->allowable_mimes_and_extensions = $allowable_mimes_and_extensions;
        }

        if ($filesystem === null) {
        } else {
            $this->filesystem = $filesystem;
        }
    }

    /**
     * Upload File(s)
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function upload()
    {
        // Pre file processing
        $this->validateFormToken();
        $this->validateInputFieldName();
        $this->createFileArray();
        $this->validateTargetFolder();

        // For each uploaded file...
        foreach ($this->file_array as $item) {

            $upload_path_and_file = $item['tmp_name'];

            if ($item['error'] === 0) {
            } else {
                throw new RuntimeException ('Http Upload Error: ' . $item['error']);
            }

            $this->validateUploadFileExists($upload_path_and_file);

            $this->validateMimeType($upload_path_and_file);

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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function validateFormToken()
    {
        return $this;

        $session_id    = session_id();
        $session_token = $_SESSION[$session_id];

        if (isset($this->request_parameters[$session_token])) {

        }
        return $this;
    }

    /**
     * Validate Upload File is available in the $_FILE super global
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function validateInputFieldName()
    {
        if (isset($this->file_array[$this->input_field_name])) {
        } else {
            throw new RuntimeException($this->error_messages[110] . $this->input_field_name);
        }

        return $this;
    }

    /**
     * Create File Array takes the $_POST madness and turns it into something normal
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
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
     * @throws  \CommonApi\Exception\RuntimeException
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
            throw new RuntimeException($this->error_messages[140] . $this->target_folder);
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function validateUploadFileExists($upload_path_and_file)
    {
        if (file_exists($upload_path_and_file)) {
        } else {
            throw new RuntimeException($this->error_messages[120] . $upload_path_and_file);
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function validateMimeType($upload_path_and_file)
    {
        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $upload_path_and_file);

        if (isset($this->allowable_mimes_and_extensions[$mime_type])) {
        } else {
            throw new RuntimeException($this->error_messages[130] . $mime_type);
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
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function validateTargetFile($target_path_and_file)
    {
        if (is_object($this->filesystem)) {
            $true_or_false = $this->filesystem->exists($target_path_and_file);
        } else {
            $true_or_false = file_exists($target_path_and_file);
        }

        if ($true_or_false === true && $this->overwrite_existing_file === 0) {
            throw new RuntimeException($this->error_messages[150] . $target_path_and_file);
        }

        return $this;
    }
}
