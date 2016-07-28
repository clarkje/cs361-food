<?php

class Manufacturer { 
 
    var $id;     // Unique ID
    var $name;   
    var $phone_number;
    var $email;
    var $website_url; 
    
    function Manufacturer() { 
         $this->id = null;   
    }
    
    /** 
     * function get($mysqli, $id)
     * Returns a Manufacturer object for the supplied ID
     * 
     * Preconditions: 
     *  Requires a valid mysqli database handle
     *  Manufacturer must exist (returns NULL if nothing found)
     * 
     * Postconditions: None
     */

    function get($mysqli, $id) { 
       
       $query = "SELECT 
                    id,
                    name, 
                    phone_number,
                    email, 
                    website_url 
                FROM manufacturer WHERE id = (?)"; 

        // Just dump the error to the screen.... it's school.
        if(!($stmt = $mysqli->prepare($query))) { 
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
        }
        $stmt -> bind_param("i", $id);
        $stmt->execute(); 
        $stmt->bind_result($this->id, $this->name, $this->phone_number, $this->email, $this->website_url);
        $stmt->fetch();
        $stmt->store_result();
        
        if ($stmt->error) { 
            $stmt->close();
            return $stmt->error;
        } else { 
            $stmt->close();
            return $this;
        }
    }
    
      /**
     * function set($mysqli)
     * 
     * Updates the values in the database
     * If id is NULL, a new record will be inserted into the database 
     * Returns the record ID for the insert
     * 
     * Preconditions: 
     *  Requires a valid mysqli database handle
     *  The value supplied for id must already exist, if non-null
     */
    
    
    function set($mysqli) { 

        // Do an insert
        if ($this->id == NULL) { 
        
            $query = "INSERT INTO manufacturer (
                        name, 
                        phone_number, 
                        email, 
                        website_url 
                    ) VALUES (?,?,?,?)"; 
                    
            if(!($stmt = $mysqli->prepare($query))) { 
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
            }
            $stmt -> bind_param("ssss", 
                                    $this->name, 
                                    $this->phone_number, 
                                    $this->email, 
                                    $this->website_url
                                    );        
                                    
        } else { 
            
            $query = "UPDATE manufacturer SET 
                        name = ?, 
                        phone_number = ?, 
                        email = ?, 
                        website_url = ? 
                      WHERE id = ?"; 
                      
            if(!($stmt = $mysqli->prepare($query))) { 
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
            }
            $stmt -> bind_param("ssssi", 
                                    $this->name, 
                                    $this->phone_number, 
                                    $this->email, 
                                    $this->website_url,
                                    $this->id
                                    );        
        } 
            
        $stmt->execute(); 
        $this->id = $stmt->insert_id; 

        $stmt->close();
        
        return $this->id;
    }
}