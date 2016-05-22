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
class CategoriesDAO extends BaseDAO {

    public function getAllCategories($term) {
        if(isset($term) && strlen($term) > 0) {
            $criteria = array("categoryName" => "*" . $term . "*");
            return $this->getByCriteria(array("categoryID" , "categoryName"), $criteria , "", "");
        }
        return $this->getProjectionOf(array("categoryID" , "categoryName"));
    }
}
?>