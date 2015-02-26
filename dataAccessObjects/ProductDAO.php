<?php
include_once 'BaseDAO.php';
include_once 'ImagesDAO.php';
include_once 'VideosDAO.php';

/**
 *  A data access object for the products data table.
 * 
 *  @author Vilizar Tsonev
 * 
 * */
class ProductDAO extends BaseDAO {

	/** The length of the product description portion that will be shown in the header **/
	const HEADER_DESCRIPTION_LENGTH = 350;

	/** The criteria fields that will be searched using 'contains' (non-exact) matching **/
	private $searchFieldsNonExactMatch;

	private $imagesDAO;

	private $videosDAO;

	function __construct($dataTable) {
       parent::__construct($dataTable);
       $this->imagesDAO = new ImagesDAO("images");
       $this->videosDAO = new VideosDAO("videos");
       $this->searchFieldsNonExactMatch = array("title", "description");
   }

	/**
	*	Gets all products headers (the most basic, shortened information only)
	**/
	public function getProductHeaders() {
		$fields = array("productID", "title", "description", "price", "thumbnailPath");
		$criteria = array("available" => "1");
		$data = $this->getByCriteria($fields, $criteria, "dateAdded", "DESC");
		$this->shortenProductDescriptions($data);
        return $data;
	}

	/**
	*	Gets all product details for the product with the given ID, joining the data from the related data tables too 
	*	(videos, images, etc).
	**/
	public function getProduct($id) {
		$criteria = array("productID" => $id);
		//get the product details
		$productFields = array("productID", "title", "description", "price", "manufacturer", "dateAdded");
		$product = $this->getByCriteria($productFields, $criteria, "", "");
		if(!isset($product[0])) {
			return array();
		} else {
			$product = $product[0];
		}
		//get the associated images
		$imageFields = array("imagePath", "imageDescription");
		$images = $this->imagesDAO->getByCriteria($imageFields, $criteria, "", "");
		//get the associated videos
		$videoFields = array("videoSrc", "videoCaption");
		$videos = $this->videosDAO->getByCriteria($videoFields, $criteria, "", "");
		$product["images"] = $images;
		$product["videos"] = $videos;
		return $product;
	}


	/**
    *  Searches for products according to the given criteria. If non-exact matching is needed for certain fields, they
    *  have to be added to the class member - searchFieldsNonExactMatch array
    *
    *   @param criteria - is an associative array, where the keys are the column names 
    *                     and the values are their expected values to match the criteria.
    **/
	public function search($criteria) {
		foreach ($criteria as $key => &$value) {
			if(in_array($key, $this->searchFieldsNonExactMatch)) {
				$value = "*". $value ."*";
			}
		}
		$fields = array("productID", "title", "description", "price", "thumbnailPath");
		$criteria["available"] = "1";
		$data = $this->getByCriteria($fields, $criteria, "dateAdded", "DESC");
		$this->shortenProductDescriptions($data);
        return $data;
	}

	/**
	*	Gets all titles of all available products.
	**/
	public function getAllProductTitles() {
		return $this->getByCriteria(array("title"), array("available" => "1"), "", "");
	}

	/****** PRIVATE METHODS ******/

	/**
	*	Shortens the products description, trimming them and keeping only the first HEADER_DESCRIPTION_LENGTH characters.
	**/
	private function shortenProductDescriptions(&$products) {
		foreach ($products as &$value) {
			$value["description"] = substr($value["description"], 0, self::HEADER_DESCRIPTION_LENGTH);
		}
	}
}
?>