<?php

/**
 * Example File Upload Usage
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

/**
 * To use the example, you must un-comment out this line. WARNING: PUT DIE() BACK WHEN DONE TESTING =)
 */
include __DIR__ . '/../Bootstrap.php';

use Molajo\Http\Upload;
use Exception\Http\UploadException;

$options['input_field_name']        = 'UploadFile';
$options['target_folder']           = __DIR__ . '/Target';
$options['overwrite_existing_file'] = true;

$connect = new Upload($options);

try {
    $connect->upload();
} catch (Exception $e) {
    throw new UploadException
    ('Molajo FileUpload: Upload failed ' . $e->getMessage());
}

/**
 * Redirect back to form
 */
header('Location: ' . 'TestUpload.html');
exit();
