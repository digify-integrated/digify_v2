<?php

class ImportModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function saveImport($p_table_name, $p_columns, $p_placeholders, $p_updateFields, $p_values) {
        // Prepare the SQL query to call the stored procedure
        $stmt = $this->db->getConnection()->prepare('CALL saveImport(:p_table_name, :p_columns, :p_placeholders, :p_updateFields, :p_values)');
    
        // Bind the procedure parameters
        $stmt->bindValue(':p_table_name', $p_table_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_columns', $p_columns, PDO::PARAM_STR);
        $stmt->bindValue(':p_placeholders', $p_placeholders, PDO::PARAM_STR);
        $stmt->bindValue(':p_updateFields', $p_updateFields, PDO::PARAM_STR);
    
        // Prepare the values for the procedure call
        $allValues = [];
        foreach ($p_values as $row) {
            $escapedRow = array_map(function($value) {
                return "'" . addslashes($value) . "'";  // Properly escape each value
            }, $row);
            $allValues[] = '(' . implode(',', $escapedRow) . ')';  // Create a row string
        }
    
        // Join all values for the procedure call
        $valuesString = implode(',', $allValues);
    
        // Bind the values string to the procedure call
        $stmt->bindValue(':p_values', $valuesString, PDO::PARAM_STR);
    
        // Execute the statement
        $stmt->execute();
    
        // Close the cursor to free up resources
        $stmt->closeCursor();
    }    
    # -------------------------------------------------------------

}
?>