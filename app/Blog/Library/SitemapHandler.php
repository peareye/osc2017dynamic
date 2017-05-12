<?php
/**
 *  Sitemap Handler Class
 *
 *  Generates or updates sitemap
 */
namespace Blog\Library;

class SitemapHandler
{
    protected $sitemapFileName = 'sitemap.xml';
    protected $sitemapFilePath;
    protected $baseUrl;
    protected $logger;
    protected $sitemapXML;
    protected $alertSearchEngines;
    protected $messages = [];

    /**
     *  Constructor
     */
    public function __construct($logger, $config)
    {
        $this->logger = $logger;
        $this->sitemapFilePath = $config['sitemapFilePath'] . $this->sitemapFileName;
        $this->baseUrl = $config['baseUrl'];
        $this->alertSearchEngines = $config['alertSearchEngines'];
    }

    /**
     * Generate sitemap
     *
     * Renders XML, writes to file, and pings search engines
     * @param array $links
     */
    public function make(array $links = null)
    {
        if (empty($links)) {
            return;
        }

        $this->generateXML($links);
        $this->writeXMLFile();

        // Only alert search engines if in production
        if ($this->alertSearchEngines === true) {
            $this->logger->alert('..Alerting search engines with updated sitemap');
            $this->alertSearchEngines();
        }
    }

    /**
     * Get Messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Generate XML
     *
     * Creates XML string from array of links
     * @param array $links
     */
    public function generateXML(array $links)
    {
        // Start sitemap XML header
        $this->sitemapXML = "<\x3Fxml version=\"1.0\" encoding=\"UTF-8\"\x3F>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        // Add all links
        foreach ($links as $link) {
            $this->sitemapXML .= "\t<url>\n\t\t<loc>{$link['link']}</loc>\n";
            $this->sitemapXML .= "\t\t<lastmod>{$link['date']}</lastmod>\n \t</url>\n";
        }

        // Close the sitemap XML string
        $this->sitemapXML .= "</urlset>\n";
    }

    /**
     * Write XML File
     */
    public function writeXMLFile()
    {
        // Write the sitemap data to file
        try {
            $fh = fopen($this->sitemapFilePath, 'w');
            fwrite($fh, $this->sitemapXML);
            fclose($fh);
        } catch (\Exception $e) {
            // Log failure
            $this->logger->error('..Failed to write sitemap');
            $this->logger->error(print_r($e->getMessage(), true));

            return false;
        }

        return true;
    }

    /**
     * Alert Search Engines
     */
    public function alertSearchEngines()
    {
        // Ping Google and Bing with the updated sitemap
        $sitemapUrl = urlencode($this->baseUrl . '/' . $this->sitemapFileName);

        // Google
        $submitSitemapUrl[] = "http://www.google.com/webmasters/tools/ping?sitemap=" . $sitemapUrl;

        // Bing
        $submitSitemapUrl[] = 'http://www.bing.com/ping?sitemap=' . $sitemapUrl;

        foreach ($submitSitemapUrl as $submission) {
            $this->logger->alert('..Submitting sitemap to: ' . $submission);
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $submission);
                $response = curl_exec($ch);
                $httpResponseStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                // Save messages
                $this->messages[] = ['engine' => $submission, 'status' => $httpResponseStatus];

            } catch (\Exception $e) {
                // Log failure
                $log->error('Failed to connect to search engines');
                $log->error(print_r($e->getMessage(), true));

                // Save messages
                $this->messages[] = ['engine' => $submission, 'status' => 'Error: ' . print_r($e->getMessage(), true)];
            }

            $this->logger->alert('..Sitemap submission response: ' . $httpResponseStatus);
        }
    }
}
