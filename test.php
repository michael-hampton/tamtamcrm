<?php

$arrTest[0] = ['name' => 'test', 'sku' => 'test2'];
$arrTest[1] = ['name' => 'test3', 'sku' => 'test4'];


class ProductImportController 
{
    $product_feed = fgetcsv("http://www.external-domain.com/product_feed.csv");
    $product_price_feed = new price_feed_web_service;
   
    if(!$product_feed) {
        // return errors to user   
    }

    if(!(new ProductImport($arrExistingProducts, new ProductFactory))->build($product_feed, $product_price_feed)) {
        // return errors to user   
    }

    //return success message to user

}

// this class may well be overkill 
// but it saves us from having to instantiate 
// the class directly in the product import
class ProductFactory
{
    public static function create() {
        return new Product;
    }
}

class ProductImport 
{
    
    private $validationErrors = [];
    private $arrParameters = []; 
    private $counter = 0;
    private $sql = '';    
    private $productFactory;
      /**
     *
     * @var type 
     */
    private $requiredHeaders = array(
        'name',
        'sku',
    ); //headers we expect

    public function __construct(array $arrExistingProducts, ProductFactory $productFactory) {
        // get array of all products so we can check new ones don't already exist
        // before inserting db should also have unique constraint
        $this->arrExistingProducts = $arrExistingProducts;
        $this->productFactory = $productFactory; 
    }
    
    public function build(array $arrData, ProductPriceFeed $product_price_feed) {
        
        $price_data = $product_price_feed->get_prices();
        // would need to reformat price array so that the product id is the key
        if(empty($arrData) || empty($price_data)) {
            
            return false;
        }
        
        $this->counter = 0;
        $this->sql = "INSERT INTO table (product_id, product_name, price) VALUES (";
        
        foreach($arrData as $key => $arrProduct) {
            
            
            if(!$this->validateHeaders($arrProduct)) {
                
                return false;
            }
            
            // would normally recommend passing this in through the constructor 
            $this->objProduct = $this->productFactory::create();
            
            if($this->counter > 0) {
                $this->sql .= "(";
            }
           
             // following methods will build placeholders for query as we loop through products 
            // coud just use the save method in the product object here and have multiple queries running
            // continue for each attribute
            $this->validateProductName($arrProduct);
            $this->validateProductPrice($arrProduct, $price_data); 
            $this->validateProductId($arrProduct);
            
            
            // check for errors
            $this->validationFailures = array_merge($this->objProduct->getValidationFailures(), $this->validationErrors);
            
            if(count($this->validationFailures) > 0) {
              // can either continue or stop if one fails depending on what you want to do
                // add to the errors array so we can get it from the controller later and display back to the user
                
          
                return false;
            }
           
            $this->sql = rtrim($this->sql, ",") . "), ";
            $this->counter++;
        }
        
        if(!$this->save()) {
            // could have more elaborate error messages here from db
            $this->validationFailures[] = "Unable to save to database";
            return false;
        }
        
     return true;
        
    }
    
    private function save() {
        
         // final query
        $sql = rtrim(trim($this->sql), ",");
        
        try {
              // run query in transaction so we cann rollback later if needs be
         $this->db->beginTransaction();
        
        if(!$this->db->save()) {
            
            $this->db->rollback();
            return false;
        }
        
        $this->db->commit();
        } catch(PDOException $exception) {
            $this->validationFailures[] = $e->getMessage();
            return false;
        }
        
        return true;
        
    }
    
    private function validateProductName($arrProduct) {
        // use the validation in the product object setters as technically this class shouldnt know about the details of a product. We will also use the getter to get the value back out just in case theres any casting within the getter
         
        if(!$this->objProduct->setName($arrProduct['name'])) {
             return false;
         }

         $this->arrParameters["name_{$this->counter}"] = $this->objProduct->getName();
         $this->sql .= ":name_{$this->counter},";
         return true;
    }
    
    private function validateProductPrice($arrProduct, $price_data) {
         
         // have made the assumption here the product ids between the 2 data sets will match
         // assume that the price feed array has been reformatted to have the product_id as the key
         if(empty($price_data[$arrProduct['product_id']]) || empty(empty($price_data[$arrProduct['product_id']]['price'])) {
                $this->validationErrors[] = "Invalid price data";
                return false;
         }

         if(!$this->objProduct->setPrice($arrProduct['product_price'])) {
             return false;
         }

         $this->arrParameters["price_{$this->counter}"] = $this->objProduct->getPrice();
         $this->sql .= ":price_{$this->counter},";
         return true;
    }

    private function validateProductId($arrProduct) {
        
         // check id doesn't already exist 
         if(!checkIfProductExists($arrProduct['product_id'])) {
             return false;
         }

         if(!$this->objProduct->setProductId($arrProduct['product_id'])) {
             return false;
         }

         $this->arrParameters["id_{$this->counter}"] = $this->objProduct->getProductId();
         $this->sql .= ":id_{$this->counter},";
         return true;
    }
    
    /**
     * 
     * @param type $arrData
     * @return boolean
     */
    private function validateHeaders ($arrData)
    {
        $foundHeaders = array_keys ($arrData);

        if ( $foundHeaders !== $this->requiredHeaders )
        {
            $this->validationErrors[] = "Incorrect headers found";
            return false;
        }

        return true;
    }
    
     /**
     * 
     * @param type $productName
     * @return boolean
     */
    private function checkIfProductExists($productId) {

        if (isset($this->arrExistingProducts[$productId]))
        {
            $this->validationErrors[] = 'The product you are trying to create already exists';
            return false;
        }

        return true;
    }
    
}

class Product {
    
    private $name;
    private $price;
    private $product_id;
    private $validationFailures = [];
    
    public function setName($name) {
            // each setter should contain validation for the specific attribute
         if(!is_string($name) || strlen($name) < 3) {
            $this->validationFailures[] = "Name must be at least 3 characters";
            return false;
        }
        
        $this->name = (string) $name;
        return true;
    }
    
    public function getName() {
         return (string) $this->name;   
    }
    
    public function getPrice() {
         return (float) $this->price;   
    }

    public function getProductId($product_id) {
         return (int) $this->product_id;
    } 

    public function setProductId($product_id) {
        if(!is_numeric($product_id) || empty($product_id)) {
            $this->validationFailures[] = "Invalid product id field";
            return false;
        }

       $this->product_id = (int) $product_id;

       return true;
    }
    
    public function setPrice($price) {
        
         if(!is_float($price) || empty($price)) {
            $this->validationFailures[] = "Invalid price field";
            return false;
        }
        
        $this->price = (float) $price;
        return true;
    }
    
     /**
     * @return array
     */
    public function getValidationFailures(): array
    {
        return $this->validationFailures;
    }
    
    public function save()
    {
        if (count($this->validationFailures) > 0) {
            return false;
        }

        // continue with save (see question 3)
    }
    
}
?>
