<?php 
require('../manufacturer.php');
include '../conf/mysql.php';

class ManufacturerTest extends PHPUnit_Framework_TestCase {

	var $mysqli;
	
	// Push any records we've inserted onto this stack, so we can delete them
	var $insert_ids = array();
	
	public function  setUp() { 

		$MYSQL_HOST = "127.0.0.1";
		$MYSQL_USER = "food";
		$MYSQL_PASS = ""; 
		$MYSQL_DB = "food";

		//Connects to the database
		$this->mysqli = new mysqli($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);
			
		if ($this->mysqli->connect_errno) {
			echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
		}
	}

	public function tearDown() { 
		
		while ($insert_id = array_pop($this->insert_ids)) { 
			
			$query = "DELETE FROM manufacturer WHERE id = (?)";
			
			if(!($stmt = $this->mysqli->prepare($query))) { 
	                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
	        }
	        $stmt->bind_param("i", $insert_id);
	        $stmt->execute(); 
	        // $stmt->close();                        
			$this->mysqli->close();	
		}
	}

	// Accurately add and retrieve a manufacturer using get() and set()
	public function testAdd() {
		
		$man1 = new Manufacturer(); 
		$man1->name = "Foo"; 
		$man1->phone_number = "123-456-7890"; 
		$man1->email = "foo@bar.com";
		$man1->website_url = "http://www.foo.com/";
		$insert_id = $man1->set($this->mysqli); 
		array_push($this->insert_ids, $insert_id);

		$man2 = new Manufacturer(); 
		$man2->get($this->mysqli, $insert_id); 
		
		$this->assertEquals($insert_id, $man2->id);
		$this->assertEquals($man1->name, $man2->name); 
		$this->assertEquals($man1->phone_number, $man2->phone_number); 
		$this->assertEquals($man1->email, $man2->email); 
		$this->assertEquals($man1->website_url, $man2->website_url); 
	}
	
	// You shouldn't be able to add a duplicate manufacturer	
	
	public function testDup() { 
		
		$man1 = new Manufacturer(); 
		$man1->name = "Foo"; 
		$man1->phone_number = "123-456-7890"; 
		$man1->email = "foo@bar.com";
		$man1->website_url = "http://www.foo.com/";
		$insert_id_1 = $man1->set($this->mysqli); 
		array_push($this->insert_ids, $insert_id_1);
		
		$man2 = new Manufacturer(); 
		$man2->name = "Foo"; 
		$man2->phone_number = "123-456-7890"; 
		$man2->email = "foo@bar.com";
		$man2->website_url = "http://www.foo.com/";
		$insert_id_2 = $man1->set($this->mysqli); 
		array_push($this->insert_ids, $insert_id_2);
		
		$this->assertNotEquals($this->mysqli->error, NULL);

	}
}
?>	