<?php

namespace Big\ElibreBundle\db;

/**
 * Класс предоставляет методы для выполнения sql запросов в базу данных,
 * автоматически создавая соединение, закрывая его, обрабатывая полцченные данные
 */
class DBManager {

    var $host;
    var $user;
    var $pwd;
    var $db;
    var $charset = 'utf8';

    function __construct($host, $user, $pwd, $db) {

        $this->host = $host;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->db = $db;
    }

    /**
     * @param type $conn
     * @return \pedro\DBManager
     */
    static public function createDBM($conn) {
        return new DBManager($conn->host, $conn->user, $conn->pwd, $conn->dataBase);
    }

    function setConnectionCharset($charset) {
        $this->charset = $charset;
    }

    function getConnection() {
        return null;
    }
    
    function selectSingleValue($query){
        $res = $this->selectQuery($query);
        return current($res);
    }

    function selectQuery($query, $simplyfy_res = true) {
        $res = array();

        $db_connect = $this->createConnection();

        if (\mysql_errno() == 0) {

            //e(mysql_select_db($this->db, $db_connect));
            //e('$query '.$query);
            if (mysql_select_db($this->db, $db_connect)) {
                //e('$query '.$query);
                $result = mysql_query($query, $db_connect);
                if ($result === false) {
                    echo($query."<br>");
                    echo(mysql_error());
                } else {
                    $number = mysql_num_rows($result);

                    for ($i = 0; $i < $number; $i++) {
                        $res[$i] = mysql_fetch_assoc($result);
                    }
                    if ($number == 1 && $simplyfy_res) {
                        $res = $res[0];
                    }

                    /* Free result set */
                    mysql_free_result($result);
                }
            }

            /* Close connection */
            mysql_close($db_connect);
        } else {
            echo \mysql_error();
        }

        return $res;
    }

    function createConnection() {
        $db_connect = mysql_connect($this->host, $this->user, $this->pwd);
        if ($db_connect) {
            if (isset($this->charset)) {
                mysql_query("SET NAMES '$this->charset'");
            }
        } else {
            m(mysql_error() . ' ' . $this->user . '@' . $this->host . ' using password ' . (isValSet($this->pwd) ? 'yes' : 'no' ));
            vd(debug_backtrace());
        }
        return $db_connect;
    }

    /**
     * @param string $query
     * @return mixed The ID generated for an AUTO_INCREMENT column
     *    0 if the query does not generate an AUTO_INCREMENT value,
     *    -1 if no MySQL connection was established
     */
    function executeQuery($query) {
        $db_connect = $this->createConnection();

        $result = mysql_db_query($this->db, $query, $db_connect);
        if ($result === false || !$db_connect) {
            m(mysql_error());
        }
        $res = mysql_insert_id($db_connect);
        /* Close connection */
        mysql_close($db_connect);

        return $res;
    }

    function GetMaxID($db_props, $table_name, $id_name) {
        $id = -1;
        if (isset($table_name) && isset($id_name)) {
            $query = "SELECT MAX($id_name) AS id FROM $table_name";
            $res = $this->selectQuery($db_props, $query);
            $id = $res["id"];
        }
        return $id;
    }

    function MakeSafeFieldValue($value) {
        return str_replace("\\", "", str_replace('"', "", str_replace("'", "", $value)));
    }

    function PrepareTimeField($time = NULL) {
        if (!isSet($time)) {
            $time = time();
        }
        return date("Y-m-d H:i:s", $time);
    }

}
