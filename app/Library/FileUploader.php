<?php
/**
 * File Uploader Class
 *
 */
namespace Blog\Library;

use \Exception;

class FileUploader
{
    // Uploded files object
    protected $uploadedFiles;

    // Path to root of image uploads
    protected $uploadFilePathRoot;

    // Path to filename based custom directory
    protected $uploadFilePath;

    // Relative URI to uploaded image
    protected $fileUri = '';

    // Filename to save
    public $fileName;

    /**
     * Constructor
     *
     * @param array $uploadedfiles Array of Slim\Http\UploadedFile objects
     * @param array $config Array of configuration items
     */
    public function __construct(array $uploadedFiles, array $config)
    {
        $this->uploadedFiles = $uploadedFiles;
        $this->uploadFilePathRoot = $config['filePath'];
    }

    /**
     * Upload Images
     *
     * Upload specific image from $_FILES array
     * @param string, array key for image
     * @return boolean, true on success or false on failure
     */
    public function upload($imageKeyName)
    {
        if (empty($this->uploadedFiles[$imageKeyName])) {
            throw new Exception('Upload image key not found in uploadedFiles');
        }

        $file = $this->uploadedFiles[$imageKeyName];

        if ($file->getError() !== UPLOAD_ERR_OK) {
            return false;
        }

        // Get file name and extension
        $uploadFileName = $file->getClientFilename();
        $ext = strtolower(pathinfo($uploadFileName, PATHINFO_EXTENSION));

        // Generate new file name
        $this->newFilename($uploadFileName);

        // Create new directory based on filename
        $this->makeImagePath();

        // Add the extension to the filename, and form new file URI
        $this->fileName .= ".{$ext}";
        $this->fileUri .= $this->fileName;

        // Save to new directory
        $file->moveTo("{$this->uploadFilePath}/{$this->fileName}");

        // Unset this file
        unset($file);

        return true;
    }

    /**
     * Uploaded File URI
     *
     * @return string
     */
    public function getUploadedFileUri()
    {
        return $this->fileUri;
    }

    /**
     * Create Directory Path
     *
     * Defines the custom directory path based on the record ID
     */
    protected function makeImagePath()
    {
        // Create file subfolder  path
        $subFolder = substr($this->fileName, 0, 2);
        $this->fileUri = $subFolder . '/';
        $this->uploadFilePath = $this->uploadFilePathRoot . $subFolder;

        // Create the path if the directory does not exist
        if (!is_dir($this->uploadFilePath)) {
            try {
                mkdir($this->uploadFilePath, 0775, true);
            } catch (Exception $e) {
                throw new Exception('Failed to create image directory path');
            }
        }

        return;
    }

    /**
     * Make Filename
     *
     * Generates new filename
     * @param string $oldName Current filename
     * @return void
     */
    protected function newFilename($oldName)
    {
        $this->fileName = md5($oldName . microtime(true));
    }
}
