=======
FileUpload Package
=======

[![Build Status](https://travis-ci.org/Molajo/FileUpload.png?branch=master)](https://travis-ci.org/Molajo/FileUpload)

Upload file processing, PHP Platform Independent.

## Usage ##

The *Molajo File Upload* package provides a simple way to add upload file processing and validation to
your application.

### PHP ini settings ###

The following php settings relate to file uploading processing:

* `upload_tmp_dir` - defines the temporary folder PHP uses to upload the file before moving it to the location
specified in the `target_folder` and `target_filename` (defined below).
* `file_uploads` - must be set to 1 before any files will be uploaded.
* `upload_max_filesize` - maximum value that any PHP script can upload for this server instance.
* `max_file_uploads` - the maximum count of files that can be simultaneously uploaded.

### Upload Form ###

This example can be used to test how the Molajo File Upload process works. Note: this example is available
in .dev\Example folder. For security purposes, you must comment out the die statement in the `.dev\Example\Upload.php`
file.

```html
    <html>
        <head><title>Molajo FileUpload Package</title></head>
        <body>
            <h2>Molajo FileUpload Example</h2>
            <form name='UploadFile' enctype='multipart/form-data' action='Upload.php' method='POST'>
                Choose a file to upload:
                <input type="file" name="UploadFile" id="proof" required
                       accept="image/bmp, image/gif, image/jpeg, image/png, image/tiff, image/x-icon">
                <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                <button type='submit'>Upload File</button>
            </form>
        </body>
    </html>
```
The values that are key to see area:

* `UploadFile` value for the name element in the form input field is used to set the value of
`$options[input_field_name]`, below;
* Set `action` equal to server application responsible to handle the upload process;
* Set the `MAX_FILE_SIZE` value (the example is set to 2MB or 2,097,152 bytes).

## Quick Start ##

```php
    use Molajo\Http\Upload;
    use Exception\Http\UploadException;

    $options['input_field_name']        = 'UploadFile';
    $options['target_folder']           = __DIR__ . '/Target';
    $options['target_filename']         = 'newname.' . pathinfo($_FILES['UploadFile']['name'], PATHINFO_EXTENSION);
    $options['overwrite_existing_file'] = true;

    // Instantiate the class, passing in the options array
    try {
        $connect = new Upload($options);

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload: Could not instantiate Molajo FileUpload Upload class ' . $e->getMessage());
    }

    // Now, execute the uploadFile method
    try {
        $connect->upload();

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload: Upload failed ' . $e->getMessage());
    }

```

## Other Settings ##
Other settings can be used to adapt the class for your needs.

## Mime Types and File Extensions ##
The default settings allow the following mime types and associated file extensions. There are several ways
to change the allowable settings: add a valid mime type, add a valid file extension, remove
a mime type and its associated file type extensions or replace the entire list of allowable mimme types and
extensions.
```php
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

```

### Add a Valid Mime Type ###

First, instantiate the class. This instance is available in $connect.

Then, using that instance, execute the addType method, passing in both the mime type and extension value needed.

```php
    $mime_type = 'text/css';
    $extension = 'css';

    $connect->addType($mime_type, $extension);
```

### Add a Valid File Extension ###

First, instantiate the class. This instance is available in $connect.

Then, using that instance, execute the addType method, passing in the existing mime type
and the additional extension value needed.

```php
    $mime_type = 'text/plain';
    $extension = 'text';

    $connect->addType($mime_type, $extension);
```

### Remove a Mime Type and its File Extensions ###

First, instantiate the class. This instance is available in $connect.

Then, using that instance, execute the addType method, passing in the existing mime type
and the additional extension value needed.

```php
    $mime_type = 'text/css';
    $connect->removeType($mime_type);
```

### Define all Allowable Mime Type and File Extension Values ###

```php
    use Molajo\Http\Upload;
    use Exception\Http\UploadException;

    $options = array();

    $allowable_mimes_and_extensions = array(
        'image/jpeg'                    => 'jpg'
    );

    $options['allowable_mimes_and_extensions']  = $allowable_mimes_and_extensions;
    $options['input_field_name']                = 'UploadFile';
    $options['target_folder']                   = __DIR__ . '/Target';
    $options['target_filename']                 = 'newname.' . pathinfo($_FILES['UploadFile']['name'], PATHINFO_EXTENSION);
    $options['overwrite_existing_file']         = true;

    // Instantiate the class, passing in the options array (including the new allowable_mimes_and_extensions)
    try {
        $connect = new Upload($options);

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload: Could not instantiate Molajo FileUpload Upload class ' . $e->getMessage());
    }

    // Now, execute the uploadFile method
    try {
        $connect->upload();

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload: Upload failed ' . $e->getMessage());
    }

```

## Other Settings ##
There are several other settings that can be used to adapt the upload.


## Maximum File Size ##
The *setMaxFileSize* method sets a value for editing the maximum file size allowed during upload. This value
cannot exceed the `upload_max_filesize` `php.ini` setting or the MAX_FILE_SIZE value specified in the HTML Form.

```php
    // First, instantiate the class, as described above

    try {
        $connect->setMaxFileSize('2GB');

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload Upload: Set Maximum File Size Failed. ' . $e->getMessage());
    }

```

## Input Field Name ##
The `input_field_name` can be passed into the constructor, as demonstrated above, or use the `setInputFieldName`
method for this purpose. This value must match the `form name` value in the HTML Form.

```html
    <form name='UploadFile' enctype='multipart/form-data' action='Upload.php' method='POST'>
```

```php
    // First, instantiate the class, as described above

    try {
        $connect->setTargetFolder('UploadFile');

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload Upload: Set setTargetFolder Failed. ' . $e->getMessage());
    }

```

## Target Folder ##
The `target_folder` can be passed into the constructor, as demonstrated above, or use the `setTargetFolder`
method for this purpose.

```php
    // First, instantiate the class, as described above

    try {
        $connect->setTargetFolder(__DIR__ . '/', 'TargetFolderName');

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload Upload: Set Target Folder Failed. ' . $e->getMessage());
    }

```

## Overwrite Existing File ##
Overwriting existing files defaults to false. The `overwrite_existing_file` can be passed into the constructor
and set to true, or use the `setOverwriteExistingFile`
method, passing in `True`.

```php
    // First, instantiate the class, as described above

    try {
        $connect->setOverwriteExistingFile(true);

    } catch (Exception $e) {
        throw new UploadException
        ('Molajo FileUpload Upload: Set Target Folder Failed. ' . $e->getMessage());
    }

```

## Errors ##

The following are [1-7: PHP specific errors and messages](http://php.net/manual/en/features.file-upload.errors.php)
and Molajo-specific errors. To translate, simply add an $options entry 'error_messages' and pass the array through
the constructor, as described in the previous section.

```php
    protected $error_messages = array(
        0   => 'Molajo FileUpload: There is no error, the file uploaded with success.',
        1   => 'Molajo FileUpload: The uploaded file exceeds the upload_max_file_size directive in php.ini.',
        2   => 'Molajo FileUpload: The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
        3   => 'Molajo FileUpload: The uploaded file was only partially uploaded.',
        4   => 'Molajo FileUpload: No file was uploaded.',
        6   => 'Molajo FileUpload: Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.',
        7   => 'Molajo FileUpload: Failed to write file to disk. Introduced in PHP 5.1.0.',
        100 => 'Molajo FileUpload=>setMaxFilesize Unit of Measure must be KB, MB, GB Invalid Value: ',
        110 => 'Molajo FileUpload=>validateInputFieldName $input_field_name not in $_FILES super global: ',
        120 => 'Molajo FileUpload=>validateUploadFileExists $_FILES $input_field_name tmp_name does not exist: ',
        130 => 'Molajo FileUpload=>validateMimeType Mime type not allowed: ',
        140 => 'Molajo FileUpload=>validateTargetFolder $target_folder is not available: ',
        150 => 'Molajo FileUpload=>validateTargetFile: $target_file exists but $overwrite_existing_file is FALSE'
    );
```


## Install using Composer from Packagist ##

### Step 1: Install composer in your project ###

```php
    curl -s https://getcomposer.org/installer | php
```

### Step 2: Create a **composer.json** file in your project root ###

```php
{
    "require": {
        "Molajo/FileUpload": "1.*"
    }
}
```

### Step 3: Install via composer ###

```php
    php composer.phar install
```

## Requirements and Compliance ##
 * PHP framework independent, no dependencies
 * Requires PHP 5.3, or above
 * [Semantic Versioning](http://semver.org/)
 * Compliant with:
    * [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) and [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) Namespacing
    * [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) Coding Standards
    * [PSR-Cache Interfaces](https://github.com/php-fig/fig-standards/pull/96) (Still in Draft)
 * [phpDocumentor2] (https://github.com/phpDocumentor/phpDocumentor2)
 * [phpUnit Testing] (https://github.com/sebastianbergmann/phpunit)
 * [Travis Continuous Improvement] (https://travis-ci.org/profile/Molajo)
 * Listed on [Packagist] (http://packagist.org) and installed using [Composer] (http://getcomposer.org/)
 * Use github to submit [pull requests](https://github.com/Molajo/Cache/pulls) and [features](https://github.com/Molajo/Cache/issues)
 * Author [Amy Stephen](http://twitter.com/AmyStephen)
 * [MIT License](http://opensource.org/licenses/MIT) see the `LICENSE` file for details
