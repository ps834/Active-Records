
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

				$results1 = $objCollection->getOneRecord();
				$createData1 = processResults::generateTable($results1);
				$this->html .= $createData1;

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
		function getOneRecord() {

		    try {
		    	
					$db = dbConn::getConnection();
			        $tableName = get_called_class();
			        $sql = "SELECT * FROM " . $tableName . " WHERE id = 2";
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


	class accounts extends collection {
	    protected static $modelName = 'account';
	}
	class todos extends collection {
	    protected static $modelName = 'todo';
	}

class model {
    protected $tableName;
    public function save()
    {
        if ($this->id = '') {
            $sql = $this->insert();
        } else {
            $sql = $this->update();
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