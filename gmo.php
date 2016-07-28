<?php

class GMO { 
    
    var $id;            // Unique ID
    var $m_id;          // Parent manufacturer's unqiue ID
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
     * function get($mysqli, $id)
     * Returns a GMO object for the supplied GMO ID
     * 
     * Preconditions: 
     *  Requires a valid mysqli database handle
     *  GMO must exist (returns NULL if nothing found)
     * 
     * Postconditions: None
     */

    function get($mysqli, $id = NULL) { 
       
       $query = "SELECT 
                    id, 
                    m_id,
                    name, 
                    sci_name, 
                    description, 
                    type 
                FROM gmo
       "; 
       
       if ($id != NULL) { 
    
            $query += " WHERE id = (?)"; 

            // Just dump the error to the screen.... it's school.
            if(!($stmt = $mysqli->prepare($query))) { 
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
            }
            $stmt -> bind_param("i", $id);
       }

        $stmt->execute(); 
        $stmt->bind_result($this->id, $this->m_id, $this->name, $this->sci_name, $this->description, $this->type); 
        // $stmt->store_result();
        $stmt->close();
        
        if ($stmt->error) { 
            return $stmt->error;
        } else { 
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
                        type = ? 
                      WHERE id = ?"; 
                      
            if(!($stmt = $mysqli->prepare($query))) { 
                echo "Prepare failed: " . $stmt->errno . " " . $stmt->error;    
            }
            $stmt -> bind_param("issss", 
                                    $this->m_id, 
                                    $this->name, 
                                    $this->sci_name, 
                                    $this->description, 
                                    $this->type, 
                                    $this->id 
                                    );        
        } 
            
        $stmt->execute(); 
        $stmt->close();
            
        return;
    }
}
?>