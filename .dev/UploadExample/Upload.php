<?php

/**
 * Example File Upload Usage
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

// To use the example, you must un-comment out this line.
die;
include __DIR__ . '/../Bootstrap.php';

use Molajo\Http\Upload;
use CommonApi\Exception\RuntimeException;

$input_field_name_value        = 'UploadFile';
$target_folder_value           = __DIR__ . '/Target';
$target_filename_value         = 'newname.' . pathinfo($_FILES['UploadFile']['name'], PATHINFO_EXTENSION);
$overwrite_existing_file_value = true;

$connect = new Upload(
    $input_field_name = $input_field_name_value,
    $target_folder = $target_folder_value,
    $target_filename = $target_filename_value,
    $overwrite_existing_file = $overwrite_existing_file_value
);

try {
    $connect->upload();
} catch (Exception $e) {
    throw new RuntimeException
    ('Molajo Upload: Upload failed ' . $e->getMessage());
}

/**
 * Redirect back to form
 */
header('Location: ' . 'TestUpload.html');
exit();
