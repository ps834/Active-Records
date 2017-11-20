
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

				//$results = collection::getAllRecords($conn,$query);
				$objCollection = new todos();
				$results = $objCollection->getAllRecords();
				$createData = processResults::generateTable($results);
				$this->html .= $createData ;	

				$results1 = $objCollection->getOneRecord(7);
				$createData1 = processResults::generateTable($results1);
				$this->html .= $createData1;

				$this->html .= "<br>";



				$todoObj = todos::create();
			    $todoObj->owneremail='"someemail"';
			    $todoObj->ownerid=1;
			    $todoObj->createddate='"01-01-2017"';
			    $todoObj->duedate='"01-10-2017"';
			    $todoObj->message='"some message"';
			    $todoObj->isdone=0;
				$todoObj->save();


				$todoObj->id=210;

				$todoObj->save();


		}


		public function __destruct(){


			$this->html .=  htmlLayout::endHTML();

			//Print Program output
			printStrings::printText($this->html);
/*
			//Close Database Connection
			PDODefinition::closeConnection($conn);*/



		}



	}



	class collection{


		    static public function create() {
		      $model = new static::$modelName;
		      return $model;
		    }


		//This will execute the query passed and return the resultset
		function getAllRecords() {

		    try {

					$db = dbConn::getConnection();
			        $tableName = get_called_class();
			        $sql = 'SELECT * FROM ' . $tableName;
			        $statement = $db->prepare($sql);
			        $statement->execute();
			        $class = static::$modelName;
			        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
			        $recordsSet =  $statement->fetchAll();
			        return $recordsSet;	

			} catch (PDOException $e) {
				http_error("500 Internal Server Error\n\n"."There was a SQL error:\n\n" . $e->getMessage());
			}	  
		}

		//This will execute the query passed and return the resultset
		function getOneRecord($id) {

		    try {
		    	
					$db = dbConn::getConnection();
			        $tableName = get_called_class();
			        $sql = "SELECT * FROM " . $tableName . " WHERE id = " . $id;
			        $statement = $db->prepare($sql);
			        $statement->execute();
			        $class = static::$modelName;
			        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
			        $recordsSet =  $statement->fetchAll();
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

    public function save(){



        $tableName = $this->tableName;
        $array = get_object_vars($this);

        static::$valueString = implode(',', array_slice($array, 0, sizeof($array)-1));
        static::$columnString = implode(',', array_keys(array_slice($array, 0, sizeof($array)-1)));
        static::$valueString = 320 . static::$valueString ;

	        if ($this->id == '') {
	            $sql = $this->insert($tableName);

	        } else {

	            $sql = $this->update();
	        }

	   try{

		    $db = dbConn::getConnection();
	        $statement = $db->prepare($sql);
	        $statement->execute();  
 	        echo "data inserted";

	     }catch(Exception $e){

	       	echo "error ::: " .  $e->getMessage() . "  <br>";

	     }

        }


        public function insert($tableName){


        	$sql = "Insert Into ". $this->tableName ." (". static::$columnString . ") VALUES (" . static::$valueString . ")"; 	
        	return $sql;
        }

        public function update($tableName){

        	$sql = "Update $tableName set " . $columnName = $valueName . " where " $conditionID = $conditionValue;  	
        	return $sql;
        }

/*        public function delete($tableName){

        	$sql = "Delete from " . $tableName . " where " $conditionID = $conditionValue;
        }*/

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

}

class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;


   static $data = array(20,'srk@njit.edu','Sunny','Jain','122','1995-12-12','Male','sunny');

    public function __construct(){
        
        $this->tableName = 'todos';
	
    }
}



	class processResults{

		
		//Function to generate results in tabular format as per the resultset passed
		static function generateTable($results){


			$createData =  htmlLayout::beginTable();
			$createData .= htmlLayout::beginTableRow(); 
			foreach($results as $rows){
				foreach($rows as $key => $values){
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

	//Converts the entire String to upper case
	static function toUpperCase($value){

		return strtoupper($value);
	}

}

?>