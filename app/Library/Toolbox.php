<?php
/**
 *  Toolbox Class
 *
 *  Misc methods
 */
namespace Blog\Library;

class Toolbox
{
    /**
     * Truncate HTML Text
     *
     * Accepts an HTML string, and returns just the unformatted text, truncated to the number of words
     * @param string $text Input HTML string
     * @param intger, $characters Number of characters to return
     * @return string
     */
    public function truncateHtmlText($text, $characters = 300)
    {
        // Clean up html tags and special characters
        $text = preg_replace('/<[^>]*>/', ' ', $text);
        $text = str_replace("\r", '', $text); // replace with empty space
        $text = str_replace("\n", ' ', $text); // replace with space
        $text = str_replace("\t", ' ', $text); // replace with space
        $text = preg_replace('/\s+/', ' ', $text); // remove multiple consecutive spaces
        $text = preg_replace('/^[\s]/', '', $text); // Remove leading space from excerpt
        $text = preg_replace('/[\s]$/', '', $text); // Remove trailing space from excerpt

        // If we are already within the limit, just return the text
        if (mb_strlen($text) <= $characters) {
            return $text;
        }

        // Truncate to character limit if longer than requested
        $text = substr($text, 0, $characters);

        // We don't want the string cut mid-word, so search for the last space and trim there
        $lastSpacePosition = strrpos($text, ' ');
        if (isset($lastSpacePosition)) {
            // Cut the text at this last word
            $text = substr($text, 0, $lastSpacePosition);
        }

        return $text;
    }

    /**
     * Clean URLs
     *
     * Replaces any non alphanumeric or space characters with dashes
     * @param string $string Unformatted URL
     * @return string
     */
    public function cleanUrl($string)
    {
        // First replace ampersands with the word 'and'
        $string = str_replace('&', 'and', $string);

        // Remove slashes
        $string = str_replace('/', '-', $string);

        // Strip out any single quotes
        $string = str_replace("'", '', $string);

        // Remove unwelcome characters, and replace with dashes
        $string = preg_replace('/[^a-zA-Z0-9]+/', '-', $string);

        // Lower case
        $string = strtolower($string);

        // Finally remove and trailing dashes
        $string = trim($string, '-');

        return $string;
    }
}
