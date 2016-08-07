<?php 
require('../gmo.php');
require('../manufacturer.php');
include '../conf/mysql.php';

class GMOTest extends PHPUnit_Framework_TestCase {

	var $mysqli;
	
	// Push any records we've inserted onto this stack, so we can delete them
	var $gmo_ids = array();
	
	// We'll make one manufacturer in setUp() for all the new GMOs to use
	var $man_id;
	
	public function  setUp() { 

		$MYSQL_HOST = "127.0.0.1";
		$MYSQL_USER = "food";
		$MYSQL_PASS = ""; 
		$MYSQL_DB = "food";

		//Connects to the database
		$this->mysqli = new mysqli($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);
			
		if ($this->mysqli->connect_errno) {
			echo "Connection error " . $this->mysqli->connect_errno . " " . $this->mysqli->connect_error;
		}
		
		// All GMOs require a parent manufacturer, so we'll make one to use
		
		$man1 = new Manufacturer(); 
		$man1->name = "TEST MANUFACTURER - " . microtime();  // Just make sure it's unique... 
		$man1->phone_number = "123-456-7890"; 
		$man1->email = "foo@bar.com";
		$man1->website_url = "http://www.foo.com/";
		$this->man_id = $man1->set($this->mysqli); 

	}

	public function tearDown() { 
		
		// Delete Manufacturer			
		$query = "DELETE FROM manufacturer WHERE id = (?)"; 
		if(!($stmt = $this->mysqli->prepare($query))) { 
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
        }
        $stmt->bind_param("i", $this->man_id);
        $stmt->execute(); 
        $stmt->close();  

		// Delete GMOs	
		foreach($this->gmo_ids as $gmo_id) { 
			if($gmo_id > 0) { 
				$query = "DELETE FROM gmo WHERE id = (?)";
				
				if(!($stmt = $this->mysqli->prepare($query))) { 
		                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
		        }
		        $stmt->bind_param("i", $gmo_id);
		        $stmt->execute(); 
		        $stmt->close();                        
		
			}
		}
	}

	// Accurately add and retrieve a manufacturer using get() and set()
	public function testAdd() {

		$gmo = new GMO(); 
		$gmo->m_id = $this->man_id;
		$gmo->name = "Foo GMO - " . microtime();  // make the name unqiue
		$gmo->sci_name = "Mmmmm... Sciency!";
		$gmo->description = "THIS IS A TEST";
		$gmo->type = "FOO";
		
		$gmo_id = $gmo->set($this->mysqli);
		
		array_push($this->gmo_ids, $gmo_id);  // Push it on to the stack for cleanup

		$gmo2 = new GMO(); 
		$gmo2->get($this->mysqli, $gmo_id);

		// Confirm that the submitted and retrieved objects are equivalent
		$this->assertEquals($gmo_id, $gmo2->id); 
		$this->assertEquals($this->man_id, $gmo2->m_id);
		$this->assertEquals($gmo->name, $gmo2->name);
		$this->assertEquals($gmo->sci_name, $gmo2->sci_name);
		$this->assertEquals($gmo->description, $gmo2->description);
		$this->assertEquals($gmo->type, $gmo2->type);
		
		// Confirm that new GMOs are inactive by default.
		$this->assertEquals($gmo2->active, 0);  
		
	}
	
	// You shouldn't be able to add a duplicate GMO	
	public function testDup() { 
		
		$gmo = new GMO(); 
		$gmo->m_id = $this->man_id;
		$gmo->name = "Foo GMO"; 
		$gmo->sci_name = "Mmmmm... Sciency!";
		$gmo->description = "THIS IS A TEST";
		$gmo->type = "FOO";
		$gmo_id = $gmo->set($this->mysqli);
		array_push($this->gmo_ids, $gmo_id);  // Push it on to the stack for cleanup

		$gmo = new GMO(); 
		$gmo->m_id = $this->man_id;
		$gmo->name = "Foo GMO"; 
		$gmo->sci_name = "Mmmmm... Sciency!";
		$gmo->description = "THIS IS A TEST";
		$gmo->type = "FOO";
		$gmo_id = $gmo->set($this->mysqli);
		array_push($this->gmo_ids, $gmo_id);  // Push it on to the stack for cleanup
	
		// MySQL should really thrown an error here.
		$this->assertEquals($this->mysqli->affected_rows, -1);
	}
	
	// Manufacturer must be supplied when adding
	public function testManRequired() { 
	
		$gmo = new GMO();
		$gmo->name = "Foo GMO"; 
		$gmo->sci_name = "Mmmmm... Sciency!";
		$gmo->description = "THIS IS A TEST";
		$gmo->type = "FOO";
		$gmo_id = $gmo->set($this->mysqli);
		
		array_push($this->gmo_ids, $gmo_id);  // Push it on to the stack for cleanup
		
		// MySQL should really thrown an error here.
		$this->assertEquals($this->mysqli->affected_rows, -1);
	}

	
	// Name must be supplied when adding
	public function testNameRequired() { 
	
		$gmo = new GMO();
		$gmo->m_id = $this->man_id;
		$gmo->sci_name = "Mmmmm... Sciency!";
		$gmo->description = "THIS IS A TEST";
		$gmo->type = "FOO";
		$gmo_id = $gmo->set($this->mysqli);
		array_push($this->gmo_ids, $gmo_id);  // Push it on to the stack for cleanup
		
		// MySQL should really thrown an error here.
		$this->assertEquals($this->mysqli->affected_rows, -1);
	}

	// Scientific Name must be supplied when adding
	
	public function testSciNameRequired() { 
		$gmo = new GMO(); 
		$gmo->name = "Foo GMO - " + microtime(); 
		$gmo->m_id = $this->man_id;
		$gmo->description = "THIS IS A TEST";
		$gmo->type = "FOO";
		$gmo_id = $gmo->set($this->mysqli);
		array_push($this->gmo_ids, $gmo_id);  // Push it on to the stack for cleanup

		// MySQL should really thrown an error here.
		$this->assertEquals($this->mysqli->affected_rows, -1);
	}

	// Type must be supplied when adding
	
	public function testTypeRequired() { 

		$gmo = new GMO(); 
		$gmo->name = "Foo GMO - " + microtime(); 
		$gmo->m_id = $this->man_id;
		$gmo->sci_name = "Mmmmm... Sciency!";
		$gmo->description = "THIS IS A TEST";
		$gmo_id = $gmo->set($this->mysqli);
		array_push($this->gmo_ids, $gmo_id);  // Push it on to the stack for cleanup

		// MySQL should really thrown an error here.
		$this->assertEquals($this->mysqli->affected_rows, -1);
	}



}
?>	