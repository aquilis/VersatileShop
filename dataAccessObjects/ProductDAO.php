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

    /** The length of the product description portion that will be shown in the header * */
    const HEADER_DESCRIPTION_LENGTH = 350;

    /** The criteria fields that will be searched using 'contains' (non-exact) matching * */
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
     * 	Gets all products headers (the most basic, shortened information only)
     * */
    public function getProductHeaders() {
        $fields = array("productID", "title", "description", "price", "thumbnailPath");
        $criteria = array("available" => "1");
        $data = $this->getByCriteria($fields, $criteria, "dateAdded", "DESC");
        $this->shortenProductDescriptions($data);
        return $data;
    }

    /**
     * Gets the quantity in stock for the given product.
     *
     * @param $id is the id of the product
     * @return the quantity in stock
     */
    public function getQuantityInStock($id) {
        $criteria = array("productID" => $id);
        $productFields = array("quantityInStock");
        $product = $this->getByCriteria($productFields, $criteria, "", "");
        return $product[0]["quantityInStock"];
    }

    /**
     * 	Gets all product details for the product with the given ID, joining the data from the related data tables too 
     * 	(videos, images, etc).
     * */
    public function getProduct($id) {
        $criteria = array("productID" => $id);
        //get the product details
        $productFields = array("productID", "title", "description", "price", "manufacturer", "dateAdded", "quantityInStock");
        $product = $this->getByCriteria($productFields, $criteria, "", "");
        if (!isset($product[0])) {
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
     * Decrements the quantity in stock of the product with the given ID.
     *
     * @param $productID is the product ID
     * @param $decrementWith is the quantity to decrement with
     */
    public function decrementQuantity($productID, $decrementWith) {
        $sqlQuery = "UPDATE " . $this->dataTable . " SET quantityInStock = quantityInStock - " .
            mysqli_real_escape_string($this->dbConnection, $decrementWith) . " WHERE " .
            $this->primaryKeyColumnName . "=" . mysqli_real_escape_string($this->dbConnection, $productID) . ";";
        mysqli_query($this->dbConnection, $sqlQuery) or trigger_error("Query Failed: " . mysql_error());
    }

    /**
     *  Searches for products according to the given criteria. If non-exact matching is needed for certain fields, they
     *  have to be added to the class member - searchFieldsNonExactMatch array
     *
     *   @param criteria - is an associative array, where the keys are the column names 
     *                     and the values are their expected values to match the criteria.
     * */
    public function search($criteria) {
        foreach ($criteria as $key => &$value) {
            if (in_array($key, $this->searchFieldsNonExactMatch)) {
                $value = "*" . $value . "*";
            }
        }
        $fields = array("productID", "title", "description", "price", "thumbnailPath");
        $criteria["available"] = "1";
        $data = $this->getByCriteria($fields, $criteria, "dateAdded", "DESC");
        $this->shortenProductDescriptions($data);
        return $data;
    }

    /**
     * Gets all ids and titles of all available products.
     *
     * @param $term is the search term that the title should match
     * @return array of the returned results
     */
    public function getAllProductTitles($term) {
        $criteria = array("available" => "1");
        if(isset($term) && strlen($term) > 0) {
            $criteria["title"] = "*" . $term . "*";
        }
        return $this->getByCriteria(array("productID" , "title"), $criteria , "", "");
    }

    /*     * **** PRIVATE METHODS ***** */

    /**
     * 	Shortens the products description, trimming them and keeping only the first HEADER_DESCRIPTION_LENGTH characters.
     * */
    private function shortenProductDescriptions(&$products) {
        foreach ($products as &$value) {
            $value["description"] = substr($value["description"], 0, self::HEADER_DESCRIPTION_LENGTH);
        }
    }

}

?>