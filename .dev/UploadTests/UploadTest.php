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
        if (file_exists(__DIR__ . '/Temp/test.txt')) {
        } else {
            copy(__DIR__ . '/test.txt', __DIR__ . '/Temp/test.txt');
        }

        $file             = array();
        $file['name']     = 'test.txt';
        $file['type']     = 'text/plain';
        $file['tmp_name'] = __DIR__ . '/Temp/test.txt';
        $file['error']    = 0;
        $file['size']     = 10219;

        $files                = array();
        $files['upload_file'] = $file;

        $options                            = array();
        $options['file_array']              = $files;
        $options['input_field_name']        = 'upload_file'; //matches the $files array entry
        $options['overwrite_existing_file'] = true;
        $options['target_folder']           = __DIR__ . '/Target';
        $options['target_filename']         = 'newname.txt';

        $class         = 'Molajo\\Upload\\Upload';
        $this->connect = new $class($options);

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
