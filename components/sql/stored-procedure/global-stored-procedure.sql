DELIMITER //

DROP PROCEDURE IF EXISTS buildAppModuleStack//
CREATE PROCEDURE buildAppModuleStack(IN p_user_account_id INT)
BEGIN
    SELECT DISTINCT(am.app_module_id) as app_module_id, am.app_module_name, am.menu_item_id, app_logo, app_module_description
    FROM app_module am
    JOIN menu_item mi ON mi.app_module_id = am.app_module_id
    WHERE EXISTS (
        SELECT 1
        FROM role_permission mar
        WHERE mar.menu_item_id = mi.menu_item_id
        AND mar.read_access = 1
        AND mar.role_id IN (
            SELECT role_id
            FROM role_user_account
            WHERE user_account_id = p_user_account_id
        )
    )
    ORDER BY am.order_sequence, am.app_module_name;
END //

DROP PROCEDURE IF EXISTS generateExportOption//
CREATE PROCEDURE generateExportOption(IN p_databasename VARCHAR(500), IN p_table_name VARCHAR(500))
BEGIN
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_schema = p_databasename 
    AND table_name = p_table_name
    ORDER BY ordinal_position;
END //

DROP PROCEDURE IF EXISTS exportData//
CREATE PROCEDURE exportData(
    IN p_table_name VARCHAR(255),
    IN p_columns TEXT,
    IN p_ids TEXT
)
BEGIN
    SET @sql = CONCAT('SELECT ', p_columns, ' FROM ', p_table_name);

    IF p_table_name = 'app_module' THEN
        SET @sql = CONCAT(@sql, ' WHERE app_module_id IN (', p_ids, ')');
    END IF;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS saveImport //
CREATE PROCEDURE saveImport(
    IN p_table_name VARCHAR(255),
    IN p_columns TEXT,
    IN p_placeholders TEXT,
    IN p_updateFields TEXT,
    IN p_values TEXT
)
BEGIN
    SET @sql = CONCAT(
        'INSERT INTO ', p_table_name, ' (', p_columns, ') ',
        'VALUES ', p_values, ' ',
        'ON DUPLICATE KEY UPDATE ', p_updateFields
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateTables//
CREATE PROCEDURE generateTables(
    IN p_database_name VARCHAR(255)
)
BEGIN
	SELECT table_name FROM information_schema.tables WHERE table_schema = p_database_name;
END //