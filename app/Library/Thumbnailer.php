<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2016 Wolfgang Moritz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Thumbnailer
 *
 * Create thumbnails on-the-fly
 * @version 1.0.0
 */
namespace Blog\Library;

use Intervention\Image\ImageManager;

class Thumbnailer
{
    /**
     * Physical path to original image directory
     * @var string
     */
    protected $pathToOriginalImage;

    /**
     * Physical path to thumbnail image directory
     * @var string
     */
    protected $pathToThumbImage;

    /**
     * Original File Name
     * @var string
     */
    protected $originalImageName = '';

    /**
     * Thumbnail Width
     * @var int
     */
    protected $width;

    /**
     * Thumbnail Height
     * @var int
     */
    protected $height;

    /**
     * Route to Match (Regex)
     * @var string
     */
    protected $matchRoutePattern = '';

    /**
     * Intervention Image Manager
     * @var Intervention\Image\ImageManager
     */
    protected $imageManager;

    /**
     * Constructor
     *
     * @param array $config Configuration array
     * @param Intervention\Image\ImageManager $imageManager
     */
    public function __construct(array $config, ImageManager $imageManager)
    {
        // Set encoding
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');

        // Assume failure so on error a 404 is returned
        http_response_code(404);

        // Set error reporting level
        if (isset($config['showErrors']) && $config['showErrors'] === true) {
            // Development
            error_reporting(-1);
            $config['displayErrorDetails'] = true;
        } else {
            // Production
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }

        // Set configuration items
        $this->pathToOriginalImage = $config['pathToOriginalImage'];
        $this->pathToThumbImage = $config['pathToThumbImage'];
        $this->matchRoutePattern = $config['matchRoutePattern'];
        $this->imageManager = $imageManager;
    }

    /**
     * Make Thumbnail
     *
     * Runs thumbnail engine
     */
    public function make()
    {
        // Start by checking the route
        $this->matchRoute();

        // Does the original file exist?
        if (!file_exists($this->pathToOriginalImage)) {
            // Then nothing to do but return
            return $this->response();
        }

        // Make directory
        $this->makeThumbDir();

        // Create image instance
        $image = $this->imageManager->make($this->pathToOriginalImage);

        // Check how to resize
        if (is_numeric($this->width) && is_numeric($this->height)) {
            // If both dimensions are set, crop and resise to requested aspect ratio but do not upsize
            $image->fit($this->width, $this->height, function ($constraint) {
                $constraint->upsize();
            });
        } elseif (is_numeric($this->width) && empty($this->height)) {
            // If just a width is provided, widen proportionately
            $image->widen($this->width);
        } elseif (empty($this->width) && is_numeric($this->height)) {
            // If just a height is provided, heighten proportionately
            $image->heighten($this->height);
        }

        // Now save resized image to thumbnail path
        $image->save($this->pathToThumbImage . $this->originalImageName);

        // Return image stream to display thumbnail on first view
        $this->response(200, $image->response());
    }

    /**
     * Make New Directory
     */
    protected function makeThumbDir()
    {
        // Create new directory for this size if it does not exist
        if (!file_exists($this->pathToThumbImage)) {
            if (!mkdir($this->pathToThumbImage, 0755, true)) {
                // Unable to make directory, so give up now
                $this->response();
            }
        }
    }

    /**
     * Match Route
     *
     * Checks REQUEST_URI for matching route
     * If route pattern is not matched, returns 404
     */
    protected function matchRoute()
    {
        // Check route pattern
        if (!preg_match($this->matchRoutePattern, $_SERVER['REQUEST_URI'], $segments)) {
            // Does not match, end here
            $this->response();
        }

        // Break out width and height dimensions
        $dimSegments = explode('x', $segments['dims']);
        $this->width = !empty($dimSegments[0]) ? $dimSegments[0] : null;
        $this->height = !empty($dimSegments[1]) ? $dimSegments[1] : null;

        // Save what we need from the route
        $this->originalImageName = $segments['image'];
        $this->pathToOriginalImage .= "{$segments['subdir']}/{$segments['image']}";
        $this->pathToThumbImage .= "{$segments['subdir']}/{$segments['dims']}/";
    }

    /**
     * Response
     *
     * Sets HTTP response code, echos supplied body, and exits
     * @param int $status HTTP status code
     * @param stream $body http body
     */
    protected function response($status = 404, $body = null)
    {
        http_response_code($status);

        if ($body !== null) {
            echo $body;
        }

        exit;
    }
}
