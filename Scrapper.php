<?php
//require_once 'goutte.phar'; 
require_once 'vendor/autoload.php';
use Goutte\Client;

class Scrapper 
{

	protected $url;
	protected $category;

    /**
     * Construct
     *
     * @access public
     * @param  string $url  
     * @param  string $category  
     * @throws ErrorException
     */
    public function __construct($url, $category) {
		$this->init($url, $category);
    }

    /**
     * Check if input data is valid
     *
     * @access public
     * @param  string $url  
     * @param  string $category  
     * @throws ErrorException
     */

	public function init($url, $category) {
		$url = filter_var($url, FILTER_SANITIZE_URL);
		if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
			$this->url = $url;
		} else {
			throw new Exception("Given url is invalid", 1);
		}

		if (strlen($category)) {
			$this->category = $category;
		} else {
			throw new Exception("Category cannot be empty", 1);
		}
	}

	 /**
     * Get data from websites and return json string
     *
     * @access public
     * @return string json encoded data 
     */
	public function json() {
		return 	json_encode($this->getUrlsDetails($this->getUrls()));
	} 

    /**
     * Get links and their titles from given category
     *
     * @access protected 
     * @return array of associative arrays with 'url' and 'link' keys
     */
	protected function getUrls() {
		$client = new Client();
		$crawler = $client->request('GET', $this->url);
		//get nodes with a requested category
		$category = $crawler->filter('.category')->reduce(function($node) {
			if (strpos($node->text(), $this->category) !== false) return true;
			else return false;
		});

		//get a links from this category, we have to go up to parents and down to post
		$urls = array();
		$category->each(function($node) use (&$urls) {
			$article = $node->parents()->filter('article');// {
			$entry = $article->children()->filter('.entry-summary a');
			$entry->each(function($node, $i) use (&$urls) {
				$urls[] = array('url' => $node->attr('href'),
								'link' => $node->text()
				);
			});
		});
		return $urls;
	}

    /**
     * Get filesize, meta description and keywords from urls.
     *
     * @access protected 
     * @param  array $urls array returned by getUrls() 
     * @return array with results and total size
     */
	protected function getUrlsDetails($urls) {
		$total = 0;
		$results = array();
		foreach ($urls as $links) {
			//first get keywords and description
			$head = get_meta_tags($links['url']);
			$links['keywords'] = (isset($head['keywords'] ) ? $head['keywords'] : '' );
			$links['meta description'] = (isset($head['description'] ) ? $head['description'] : '' );

			//get size of a webpage
			$headers = get_headers($links['url'], 1);
			$size = (isset($headers['Content-Length']) ? $headers['Content-Length'] : 0 );
			//the size can be type of array if page was redirected
			if (is_array($size)) $size = array_pop($size);
			//header content-length can be unset, in this case use file_get_contents
			if (!intval($size)) {
				$s = strlen(file_get_contents($links['url']));
				$size = ($s===false) ? 0: $s;
			}

			$links['filesize'] = round($size/1000,1) . 'kb';
			$total += $size;
			$results[] = $links;
		}
		return array('results' => $results, 'total' => round($size/1000,1).'kb' );
	}
}
