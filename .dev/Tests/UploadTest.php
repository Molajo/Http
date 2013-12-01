<?php
/**
 * Upload Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Http\Test;

/**
 * Upload Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class UploadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Molajo\Http\Upload Object
     */
    protected $connect;

    /**
     * Initialises Adapter
     */
    protected function setUp()
    {
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

        $maximum_file_size = '2MB';

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

        return;
    }

    /**
     * Upload File - Fail - Mime Type not allowed
     *
     * @covers \Molajo\Http\Upload::upload
     */
    public function testUpload()
    {
        if (file_exists(__DIR__ . '/Temp/test.txt')) {
        } else {
            copy(__DIR__ . '/test.txt', __DIR__ . '/Temp/test.txt');
        }

        $target_folder           = __DIR__ . '/Target';
        $target_filename         = 'newname.txt';
        $input_field_name        = 'upload_file'; //matches the $files array entry
        $overwrite_existing_file = 1;

        // $_FILES superglobal
        $file             = array();
        $file['name']     = 'test.txt';
        $file['type']     = 'text/plain';
        $file['tmp_name'] = __DIR__ . '/Temp/test.txt';
        $file['error']    = 0;
        $file['size']     = 10219;

        $files                = array();
        $files['upload_file'] = $file;

        $class         = 'Molajo\\Http\\Upload';
        $this->connect = new $class(
            $input_field_name,
            $target_folder,
            $target_filename,
            $overwrite_existing_file,
            $files
        );

        $this->connect->upload();

        $this->assertfileExists(__DIR__ . '/Target/newname.txt');
        $this->assertfileNotExists(__DIR__ . '/Temp/test.txt');

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}
