
<?php

	//turn on debugging message
	ini_set('display errors','on');
	error_reporting(E_ALL);

	//Class to load  classes it finds the file when the program starts to fail for calling a missing class
	class Manage {
		public static function autoload($class){
			include $class . 'php';
		}
	}

	spl_autoload_register(array('Manage','autoload'));


	//Set DB Connection Parameters
	define('DATABASE', 'ps834');
	define('USERNAME', 'ps834');
	define('PASSWORD', 'q1ZT9FnRO');
	define('CONNECTION', 'sql1.njit.edu');
	$obj = new main();

	class main{



		public function __construct(){


				//Execute the Program
				$progObj = new programExecution();
				$progObj->executeProgram();


		}

	}




	//This Class contains the Layout of the entire program
	class programExecution{


		protected $html;

		public function __construct(){

			//Open HTML 
			$this->html = htmlLayout::beginHTMLTag();
			$this->html = htmlLayout::beginTable();
		}


		//This function will call functions to create table and count no. of records returned
		public function executeProgram(){


				$objCollection = new accounts();
				$todoObjects = new todos();

				//Display all records in accounts 
				$this->html .= htmlLayout::setHeaderSize("Select all records from accounts table");
				$results = $objCollection->getAllRecords();
				$createData = processResults::generateTable($results);
				$this->html .= $createData ;	
				$this->html .= htmlLayout::goToNextLine();
				$this->html .= htmlLayout::horizontalLine();


				//Fetch a record from accounts where ID is 7
				$this->html .= htmlLayout::setHeaderSize("Fetch a record from accounts where ID is 7");
				$results1 = $objCollection->getOneRecord(7);
				$createData1 = processResults::generateTable($results1);
				$this->html .= $createData1;
				$this->html .= htmlLayout::goToNextLine();
				$this->html .= htmlLayout::horizontalLine();


				//Display all records in todos 
				$this->html .= htmlLayout::setHeaderSize("Select all records from todos table");
				$results = $todoObjects->getAllRecords();
				$createData = processResults::generateTable($results);
				$this->html .= $createData ;	
				$this->html .= htmlLayout::goToNextLine();
				$this->html .= htmlLayout::horizontalLine();

				//Fetch a record from todos where ID is 7
				$this->html .= htmlLayout::setHeaderSize("Fetch a record from todos where ID is 7");
				$results1 = $todoObjects->getOneRecord(7);
				$createData1 = processResults::generateTable($results1);
				$this->html .= $createData1;
				$this->html .= htmlLayout::goToNextLine();
				$this->html .= htmlLayout::horizontalLine();

				//Insert a record in Todos Table
				$this->html .= htmlLayout::setHeaderSize("Insert a record in todos table");
				$todoObj = todos::create();
			    $todoObj->owneremail='"mj812@gmail.com"';
			    $todoObj->ownerid=1;
			    $todoObj->createddate='"2017-01-01"';
			    $todoObj->duedate='"2017-01-10"';
			    $todoObj->message='"Need to Update Password"';
			    $todoObj->isdone=0;
				$todoObj->save();
				$results = $todoObjects->getAllRecords();
				$createData = processResults::generateTable($results);
				$this->html .= $createData ;	
				$this->html .= htmlLayout::goToNextLine();
				$this->html .= htmlLayout::horizontalLine();


				//Update message in todos table where ID = 1 and set isdone as 1
				$this->html .= htmlLayout::setHeaderSize("Update message in todos table and set isdone as 1");
				$todoObjUpdate = todos::create();
				$todoObjUpdate->id=1;
				$todoObjUpdate->message='"Password Updated "';
				$todoObjUpdate->isdone=1;
				$todoObjUpdate->save();
				$results = $todoObjects->getOneRecord(1);
				$createData = processResults::generateTable($results);
				$this->html .= $createData ;	
				$this->html .= htmlLayout::goToNextLine();
				$this->html .= htmlLayout::horizontalLine();				


				//Delete the record from accounts table 
				$this->html .= htmlLayout::setHeaderSize("Delete the record from todos table where id = 9");
				$accObjDelete = accounts::create();
				$accObjDelete->delete(9); 
				$results = $objCollection->getAllRecords();
				$createData = processResults::generateTable($results);
				$this->html .= $createData ;	
				$this->html .= htmlLayout::goToNextLine();
				$this->html .= htmlLayout::horizontalLine();	

		}


		public function __destruct(){


			$this->html .=  htmlLayout::endHTML();

			//Print Program output
			printStrings::printText($this->html);


		}



	}



	class collection{


		    static public function create() {
		      $model = new static::$modelName;
		      return $model;
		    }


		//This will return all the records from the table
		function getAllRecords() {

		    try {

					$db = dbConn::getConnection();
			        $tableName = get_called_class();
			        $sql = 'SELECT * FROM ' . $tableName;
			        $statement = $db->prepare($sql);
			        $statement->execute();
			        $class = static::$modelName;
			        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
			        $recordsSet =  $statement->fetchAll(PDO::FETCH_ASSOC);
			        return $recordsSet;	

			} catch (PDOException $e) {
				http_error("500 Internal Server Error\n\n"."There was a SQL error:\n\n" . $e->getMessage());
			}	  
		}

		//This will return only one row from the table
		function getOneRecord($id) {

		    try {
		    	
					$db = dbConn::getConnection();
			        $tableName = get_called_class();
			        $sql = "SELECT * FROM " . $tableName . " WHERE id = " . $id;
			        $statement = $db->prepare($sql);
			        $statement->execute();
			        $class = static::$modelName;
			        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
			        $recordsSet =  $statement->fetchAll(PDO::FETCH_ASSOC);
			        return $recordsSet;	

			} catch (PDOException $e) {
				http_error("500 Internal Server Error\n\n"."There was a SQL error:\n\n" . $e->getMessage());
			}	  
		}

	}

	class accounts extends collection {
	    protected static $modelName = 'account';
	}
	class todos extends collection {
	    protected static $modelName = 'todo';
	}



class model {


    	static $columnString;
    	static $valueString;
    	static $condition ;
    	static $columns;



    // Will execute Insert and Update function
    public function save(){

        $tableName = $this->tableName;
        $array = get_object_vars($this);


        	//if ID is empty, then insert a record
	        if ($this->id == '') {

	        	$id = rand(200,500);

	        	///Will segregate the values from array and form a String
		        static::$valueString = implode(',', array_slice($array, 0, sizeof($array)-1));

		        ///Will segregate the column from array and form a String
		        static::$columnString = implode(',', array_keys(array_slice($array, 0, sizeof($array)-1)));
		        static::$valueString = $id . static::$valueString ;

	            $sql = $this->insert();


	        } else {

	        	$keyValues = "";
	        	$valuesString = "";
	        	$i=1;

	        	//separate the column names, values to be set and condition
	        	foreach($array as $key => $values){
	        		if($values!=null && $values!='' && $values != $this->tableName){
	        			
	        			if($i==1){
	        				static::$condition = $key . "=" . $values;
	        			}else{
	        				static::$columns .= $key . "=" . $values . ",";
	        			}
	        			
	        			$i++;
	        		}

	        	}

				static::$columns = substr(static::$columns,0, strlen(static::$columns)-1);



	            $sql = $this->update();
	        }


	        self::runQuery($sql);
        }


        //Insert a record 
        public function insert(){


        	$sql = "Insert Into ". $this->tableName ." (". static::$columnString . ") VALUES (" . static::$valueString . ")"; 	
        	return $sql;
        }


        //Update a record
        public function update(){

        $sql = 'Update ' . $this->tableName . ' set ' . static::$columns . " where " . static::$condition;
        	return $sql;
        }

        //Delete a record
       public function delete($id){

        	$sql = "Delete from " . $this->tableName . " where id = " . $id;
        	self::runQuery($sql); 
        }


        //Executes the query 
        static public function runQuery($sql){

        try{

		    $db = dbConn::getConnection();
	        $statement = $db->prepare($sql);
	        $statement->execute();  

	     }catch(Exception $e){

	       	echo "Error : " .  $e->getMessage() . "  <br>";

	     }
        }

    } 



class account extends model{


	public $id;
	public $email;
	public $fname;
	public $lname;
	public $phone;
	public $birthday;
	public $gender;
	public $password;

	 public function __construct(){
        
        $this->tableName = 'accounts';
	
    }

}

class todo extends model {

    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;


    public function __construct(){
        
        $this->tableName = 'todos';
	
    }
}



	class processResults{

		
		//Function to generate results in tabular format as per the resultset passed
		static function generateTable($results){


			$createData =  htmlLayout::beginTable();

			//Generate Table Header
			foreach($results as $rows){	
				$createData .= htmlLayout::beginTableRow(); 
				foreach($rows as $key => $values){

					$createData .= htmlLayout::createTableheader($key);


				}

				$createData .= htmlLayout::endTableRow();
				break;
			}

			//Generate Table data
			foreach($results as $rows){		
			$createData .= htmlLayout::beginTableRow(); 
			foreach($rows as $values){

					$createData .= htmlLayout::createTableData($values);

				}

				$createData .= htmlLayout::endTableRow();
			} 

			
			//End Table and close HTML
			$createData .=  htmlLayout::endTable();
			$createData .= htmlLayout::goToNextLine();
			return $createData;
		}
		
	}





	class dbConn{
	    //variable to hold connection object.
	    protected static $db;
	    //private construct - class cannot be instatiated externally.
	    private function __construct() {
	        try {
	            // assign PDO object to db variable
	            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
	            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	        }
	        catch (PDOException $e) {
	            //Output error - would normally log this to error file rather than output to user.
	            echo "Connection Error: " . $e->getMessage();
	        }
	    }
	    // get connection function. Static method - accessible without instantiation
	    public static function getConnection() {
	        //Guarantees single instance, if no connection object exists then create one.
	        if (!self::$db) {
	            //new connection object.
	            new dbConn();
	        }
	        //return connection.
	        return self::$db;
	    }

	}



class printStrings{

	//This will print all the String passed to it
	static function printText($text){

			print($text);
	}
}



//Class containing all HTML tags
class htmlLayout{


	//Start HTML
	static function beginHTMLTag(){

		return '<html><body><title>PDO Connection</title>';
	}


	//Start Table
	static function beginTable(){

		return '<table border="1" align = "center">';
	}


	//Open Table Row
	static function beginTableRow(){

		return '<tr>';
	}

	//Close Table Row
	static function endTableRow(){

		return '</tr>';
	}



	//End Table
	static function endTable(){

		return '</table>';

	}


	//Create table Header
	static function createTableheader($values){

		return '<th>' . $values . '</th>';
	}

	//Create Table Data
	static function createTableData($values){

		return '<td>' . $values . '</td>';
	}

	//End HTML
	static function endHTML(){

		return '</body></html>';
	}

	//Set Text Bold
	static function setBold($values){

		return '<b>' . $values . '</b>';
	}

	//Prints data in the next line
	static function goToNextLine(){

		return '<br>';
	}

	//Print a horizontal Line
	static function horizontalLine(){

		return '<hr>';
	}

	//Converts the entire String to upper case
	static function toUpperCase($value){

		return strtoupper($value);
	}

	//Set h3 header Tag
	static function setHeaderSize($text){

		return '<h3>' . $text . '</h3>';
	}


}

?>