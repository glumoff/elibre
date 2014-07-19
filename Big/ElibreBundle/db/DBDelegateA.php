<?php

namespace Big\ElibreBundle\db;

use Big\ElibreBundle\db\DBManager;

abstract class DBDelegateA {

    var $dbm;

    /**
     * 	Инициализирует и возвращает менеждер работы с базой данных
     * 
     * @return DBManager
     */
    function &getDBM() {
        if ($this->dbm == NULL) {
            $conn = $this->getConn();
            $this->dbm = new DBManager($conn->host, $conn->user, $conn->pwd, $conn->dataBase);
        }
        return $this->dbm;
    }

    abstract function getConn();

    function getList($arr) {
        $res = "";
        $s = count($arr);
        $sdec = $s - 1;
        for ($i = 0; $i < $sdec; $i++) {
            $res .= '"' . $arr[$i] . '",';
        }

        if ($s > 0) {
            $res .= '"' . $arr[$sdec] . '"';
        }

        return $res;
    }

    /**
     * возвращет массив представляющий собой колонку переданного сюда массива строк
     *
     * @param unknown_type $aList
     * @param unknown_type $columnName
     * @return unknown
     */
    function getColumn(&$aList, $columnName) {
        $column = array();
        for ($i = 0, $s = count($aList); $i < $s; $i++) {
            $column[] = $aList[$i][$columnName];
        }
        return $column;
    }

    /**
     * Возвращает ассоциативнный массив из имени параметра в одной колонке
     * и значения параметра в друго колонке тойже строки.
     * 
     * @param unknown_type $aList
     * @param unknown_type $columnParamName
     * @param unknown_type $columnValueName
     * @return unknown
     */
    function getParamValuesArray(&$aList, $columnParamName, $columnValueName) {
        $arr = array();
        for ($i = 0, $s = count($aList); $i < $s; $i++) {
            $param = $aList[$i][$columnParamName];
            $value = $aList[$i][$columnValueName];
            $arr[$param] = $value;
        }
        return $arr;
    }

    /**
     *  Checks whether record in table $tableName with value $id in field $idFieldName exists
     *
     * @param <type> $tableName
     * @param <type> $idFieldName
     * @param <type> $id
     */
    function isRecordExists($tableName, $idFieldName, $id) {
        return $this->isRecordExistsCond($tableName, "`" . $idFieldName . "` = '" . $id . "'");
    }

    /**
     *  Checks whether record in table $tableName that satisfies $condition exists
     *
     * @param string $tableName
     * @param string $condition
     */
    function isRecordExistsCond($tableName, $condition) {
        $query = "SELECT COUNT(*) AS cnt
              FROM " . $tableName . "
              WHERE " . $condition;
        $res = $this->getDBM()->selectQuery($query);
        return $res["cnt"] > 0;
    }

    /**
     * Saves record values $data identified by field $idFieldName with value $id to $table_name
     * 
     * @param <string> $tableName   Table name
     * @param <string> $idFieldName Name of ID field
     * @param <int> $id             ID value of record
     * @param <array> $data         associative array of record values (fieldname => value)
     * @param <boolean> $create     do we need to create new record if record with specified ID doesn`t exist
     */
    function saveRecord($tableName, $idFieldName, $id, $data, $create = true) {
        $res = false;
        if (is_array($data)) {
            if (count($data)) { // don`t have any sense to do something with no data provided
                $insert_fields = "";
                $insert_values = "";
                $update_values = "";
                foreach ($data as $key => $val) {
                    if ($insert_fields) { // if $insert_fields is not empty other strings not empty too
                        $insert_fields .= ",";
                        $insert_values .= ",";
                        $update_values .= ",";
                    }
                    if (!isset($val)) {
                        // Special case: if field is set and value is NULL
                        $val = 'NULL';
                    }
                    $insert_fields .= "`" . $key . "`";
                    $insert_values .= "'" . $val . "'";
                    $update_values .= "`" . $key . "`='" . $val . "'";
                }
                $query = "";
                if (!$this->isRecordExists($tableName, $idFieldName, $id)) {
                    if ($create) {
                        $query = "INSERT INTO " . $tableName . " (" . $insert_fields . ")
                      VALUES (" . $insert_values . ")";
                    }
                } else {
                    $query = "UPDATE " . $tableName . " SET " . $update_values . " WHERE `" . $idFieldName . "`='" . $id . "'";
                }
                if ($query) {
                    $dbm = $this->getDBM();
                    $res = $dbm->executeQuery($query);
                }
            }
        }
        return $res;
    }

    function deleteRecord($tableName, $idFieldName, $id) {
        $res = false;

        $query = "DELETE FROM " . $tableName . " WHERE `" . $idFieldName . "`='" . $id . "'";
        $dbm = $this->getDBM();
        //echo $query;
        $res = $dbm->executeQuery($query);

        return $res;
    }

}
