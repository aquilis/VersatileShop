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
class productDAO extends BaseDAO {

	/** The length of the product description portion that will be shown in the header **/
	const HEADER_DESCRIPTION_LENGTH = 220;

	private $imagesDAO;

	private $videosDAO;

	function __construct($dataTable) {
       parent::__construct($dataTable);
       $this->imagesDAO = new ImagesDAO("images");
       $this->videosDAO = new VideosDAO("videos");
   }

	/**
	*	Gets all products headers (the most basic, shortened information only)
	**/
	public function getProductHeaders() {
		$query = "SELECT productID, title, SUBSTRING(description,1,".self::HEADER_DESCRIPTION_LENGTH."), price, thumbnailPath".
				" FROM ". $this->dataTable ." WHERE available = '1' order by dateAdded desc";
        $result = mysqli_query($this->dbConnection, $query);
        $data = array();
        $rowArray = array();
        //fetch_assoc can not be used here, because the substring clause screws it up
        while ($row = mysqli_fetch_array($result, MYSQL_NUM)) {
            $rowArray['productID'] = $row[0];
            $rowArray['title'] = $row[1];
            $rowArray['description'] = $row[2];
            $rowArray['price'] = $row[3];
            $rowArray['thumbnailPath'] = $row[4];
            array_push($data, $rowArray);
        }
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
}
?>