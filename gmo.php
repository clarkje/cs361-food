<?php

class GMO { 
    
    var $id;            // Unique ID
    var $m_id;          // Parent manufacturer's unique ID
    var $name;          // Name of the GMO
    var $sci_name;      // Scientific Name
    var $description; 
    
    // TODO: Is there a fixed list of types?
    var $type;          // Type of GMO
    
    function GMO() { 
      $this->id = NULL; 
      $this->m_id = NULL;
    }
    
    
    /**
     * function getAll($mysqli, $active)
     *
     * Returns an array of GMO objects
     *
     * Parameter active
     * -1 - Return all GMOs [default]
     * 0 - Return only inactive GMOs
     * 1 - Return only active GMOs
     */ 
     
     function getAll($mysqli, $active = -1) { 
         
         $gmoArray = array(); // Store the array of GMO results
         
         $query = "SELECT 
                        id, 
                        m_id,
                        name, 
                        sci_name, 
                        description,
                        type,
                        active
                    FROM gmo"; 
                  
        if ($active == 0) {             
         $query .= " WHERE active IS FALSE";
        } else if ($active == 1) { 
         $query .= " WHERE active IS TRUE";
        }

        // Just dump the error to the screen.... it's school.
        if(!($stmt = $mysqli->prepare($query))) { 
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
        }
            
            
        $stmt->execute(); 
        $stmt->bind_result($id, $m_id, $name, $sci_name, $description, $type, $active); 
        
        while($stmt->fetch()) { 
         $gmo = new GMO; 
         $gmo->id = $id; 
         $gmo->m_id = $m_id;
         $gmo->name = $name;
         $gmo->sci_name = $sci_name;
         $gmo->description = $description;
         $gmo->type = $type;
         $gmo->active = $active; 
         $gmoArray[] = $gmo;   
        }

        if ($mysqli->error) { 
            return $mysqli->error;
        } else { 
            return $gmoArray;
        }                
        
     }
    
    
    /** 
     * function get($mysqli, $id)
     * Returns a GMO object for the supplied GMO ID
     * 
     * Preconditions: 
     *  Requires a valid mysqli database handle
     *  GMO must exist (returns NULL if nothing found)
     * 
     * Postconditions: None
     */

    function get($mysqli, $id) { 
       
       $query = "SELECT 
                    id, 
                    m_id,
                    name, 
                    sci_name, 
                    description, 
                    type, 
                    active
                FROM gmo WHERE id = (?)"; 

        // Just dump the error to the screen.... it's school.
        if(!($stmt = $mysqli->prepare($query))) { 
            echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
        }
            
            
        $stmt->bind_param("i", $id);
        $stmt->execute(); 
        $stmt->bind_result($this->id, $this->m_id, $this->name, $this->sci_name, $this->description, $this->type, $this->active); 
        $stmt->fetch();
        $stmt->store_result();
        
        if ($stmt->error) { 
        //    $stmt->close();
            return $stmt->error;
        } else { 
        //    $stmt->close();
            return $this;
        }        
    }
    
    /**
     * function set($mysqli)
     * 
     * Updates the values in the database with the supplied GMO object
     * If id is NULL, a new record will be inserted into the database
     * 
     * Preconditions: 
     *  Requires a valid mysqli database handle
     *  The value supplied for id must already exist, if non-null
     */
    
    
    function set($mysqli) { 

        // Do an insert
        if ($this->id == NULL) { 
        
            $query = "INSERT INTO gmo (
                        m_id, 
                        name, 
                        sci_name, 
                        description, 
                        type
                    ) VALUES (?,?,?,?,?)"; 
                    
            if(!($stmt = $mysqli->prepare($query))) { 
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
            }
            $stmt -> bind_param("issss", 
                                    $this->m_id, 
                                    $this->name, 
                                    $this->sci_name, 
                                    $this->description, 
                                    $this->type
                                    );
        } else { 
            
            $query = "UPDATE gmo SET 
                        m_id = ?, 
                        name = ?, 
                        sci_name = ?, 
                        description = ?, 
                        type = ?, 
                        active = ? 
                      WHERE id = ?"; 
                      
            if(!($stmt = $mysqli->prepare($query))) { 
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
            }
            
            $stmt -> bind_param("issssii", 
                                    $this->m_id, 
                                    $this->name, 
                                    $this->sci_name, 
                                    $this->description, 
                                    $this->type, 
                                    $this->active,
                                    $this->id
                                    );        
        } 
        

        
        $stmt->execute();
        $this->id = $mysqli->insert_id;

        // $stmt->close();
        return $this->id;
    }
}
?>