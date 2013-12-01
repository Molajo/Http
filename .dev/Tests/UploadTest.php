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

        $class         = 'Molajo\\Upload\\Upload';
        $this->connect = $class(
            $input_field_name,
            $target_folder,
            $target_filename,
            $overwrite_existing_file,
            $files,

            $error_messages,
            $maximum_file_size,
            $allowable_mimes_and_extensions
        );

        $this->service_instance->uploadFile();

        return;
    }

    /**
     * Add Valid Mime Type and File Extension Entry to List
     *
     * @covers \Molajo\Http\Upload::addType
     * @covers \Molajo\Http\Upload::getType
     */
    public function testAddTypeAdditionalExtension()
    {
        $this->connect->addType('text/plain', 'text');
        $value = $this->connect->getType('text/plain');
        $this->assertEquals('text,txt', $value);
    }

    /**
     * Add Valid Mime Type and File Extension Entry to List
     *
     * @covers \Molajo\Http\Upload::addType
     */
    public function testAddTypeDoesNotExist()
    {
        $this->connect->addType('video/xxx', 'xxx');
        $value = $this->connect->getType('video/xxx');
        $this->assertEquals('xxx', $value);
    }

    /**
     * Remove Mime Type and File Extension Entry from List of Valid Values
     *
     * @covers \Molajo\Http\Upload::removeType
     */
    public function testRemoveType()
    {
        $mime_type = 'text/plain';
        $this->connect->removeType($mime_type);
        $value = $this->connect->getType($mime_type);
        $this->assertEquals('', $value);
    }

    /**
     * Remove Mime Type That does not exist
     *
     * @covers \Molajo\Http\Upload::removeType
     */
    public function testRemoveTypeThatDoesNotExist()
    {
        $mime_type = 'textddddddddddd/css';
        $this->connect->removeType($mime_type);
        $value = $this->connect->getType($mime_type);
        $this->assertEquals('', $value);
    }

    /**
     * Set the Maximum FileSize in KB for upload
     *
     * @covers \Molajo\Http\Upload::setMaxFileSize
     */
    public function testSetMaxFileSizeKB()
    {
        $maximum_file_size = '2KB';
        $results           = $this->connect->setMaxFileSize($maximum_file_size);
        $this->assertEquals(2048, $results);
    }

    /**
     * Set the Maximum FileSize in MB for upload
     *
     * @covers \Molajo\Http\Upload::setMaxFileSize
     */
    public function testSetMaxFileSizeMB()
    {
        $maximum_file_size = '2MB';
        $results           = $this->connect->setMaxFileSize($maximum_file_size);
        $this->assertEquals(2097152, $results);
    }

    /**
     * Set the Maximum FileSize in MB for upload
     *
     * @covers \Molajo\Http\Upload::setMaxFileSize
     */
    public function testSetMaxFileSizeGB()
    {
        $maximum_file_size = '2GB';
        $results           = $this->connect->setMaxFileSize($maximum_file_size);
        $this->assertEquals(2147483648, $results);
    }

    /**
     * Set the Maximum FileSize in MB for upload
     *
     * @covers \Molajo\Http\Upload::setMaxFileSize
     * @expectedException \Exception\Http\UploadException
     */
    public function testSetMaxFileSizeFail()
    {
        $maximum_file_size = '2XX';
        $this->connect->setMaxFileSize($maximum_file_size);
    }

    /**
     * Set Upload Folder
     *
     * @covers \Molajo\Http\Upload::setTargetFolder
     */
    public function testSetTargetFolder()
    {
        $target_path = __DIR__ . '/Target';
        $results     = $this->connect->setTargetFolder($target_path);
        $this->assertTrue(is_object($results));
    }

    /**
     * Set Input Field Name
     *
     * @covers \Molajo\Http\Upload::setInputFieldName
     */
    public function testSetInputFieldName()
    {
        $input_field_name = 'upload_file';
        $results          = $this->connect->setInputFieldName($input_field_name);
        $this->assertTrue(is_object($results));
    }

    /**
     * Upload File
     *
     * @covers \Molajo\Http\Upload::uploadFile
     */
    public function testUploadFile()
    {
        $this->connect->setOverwriteExistingFile(true);
        $this->connect->upload();
        $this->assertfileExists(__DIR__ . '/Target/newname.txt');
        $this->assertfileNotExists(__DIR__ . '/Temp/test.txt');
    }

    /**
     * Upload File - Fail - Mime Type not allowed
     *
     * @covers \Molajo\Http\Upload::setInputFieldName
     * @expectedException \Exception\Http\UploadException
     */
    public function testUploadFileFailMime()
    {
        $mime_type = 'text/plain';
        $this->connect->removeType($mime_type);
        $this->connect->upload();
    }

    /**
     * Upload File
     *
     * @covers \Molajo\Http\Upload::uploadFile
     * @expectedException \Exception\Http\UploadException
     */
    public function testUploadFileOverwriteExistingFalse()
    {
        if (file_exists(__DIR__ . '/Target/newname.txt')) {
        } else {
            copy(__DIR__ . '/test.txt', __DIR__ . '/Target/newname.txt');
        }
        $this->connect->setOverwriteExistingFile(false);
        $this->connect->upload();
    }

    /**
     * Upload File
     *
     * @covers \Molajo\Http\Upload::uploadFile
     */
    public function testUploadFile2()
    {
        $this->connect->setOverwriteExistingFile(false);
        unlink(__DIR__ . '/Target/newname.txt');
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
