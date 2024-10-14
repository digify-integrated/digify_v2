<?php

class ExportModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Export methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function exportData($p_table_name, $p_columns, $p_ids) {
        $stmt = $this->db->getConnection()->prepare('CALL exportData(:p_table_name, :p_columns, :p_ids)');
        $stmt->bindValue(':p_table_name', $p_table_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_columns', $p_columns, PDO::PARAM_STR);
        $stmt->bindValue(':p_ids', $p_ids, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>