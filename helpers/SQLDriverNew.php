<?php

class SQLDriverNew {
    private static $model;
    public $db = false; 
    
    public static function model(){
        if (self::$model == null)
            self::$model = new SQLDriverNew();

        return self::$model;
    }

    public function __construct(){
        $this->db = mysql_connect(HOST, LOGIN, PASSWORD);
        mysql_select_db(BASE, $this->db);
        mysql_query('SET CHARACTER SET utf8');
    }
    
    public function close(){
        mysql_close($this->db);
    }

    /**
    * Select
    * @param $query
    * @return array
    */
    public function Select($query)
    {
        $result = mysql_query($query);

        if (!$result)
            die(mysql_error());

        $n = mysql_num_rows($result);
        $arr = array();

        for($i = 0; $i < $n; $i++)
        {
            $row = mysql_fetch_assoc($result);
            $arr[] = $row;
        }

        return $arr;
    }

    /**
    * Insert
    * @param $table
    * @param $object
    * @return int
    */
    public function Insert($table, $object)
    { 
       $columns = array();
       $values = array();

        foreach ($object as $key => $value)
        {
            $key = $this->prepareData($key);
            $columns[] = $key;

            if ($value === null)
            {
                $values[] = 'NULL';
            }
            else
            {
                $value = $this->prepareData($value);
                $values[] = "'$value'";
            }
        }

        $columns_s = implode(',', $columns);
        $values_s = implode(',', $values);

        $query = "INSERT INTO $table ($columns_s) VALUES ($values_s)";
        $result = mysql_query($query);

        if (!$result)
            die(mysql_error());

        return mysql_insert_id();
    }

    /**
    * Update
    * @param $table
    * @param $object
    * @param $where
    * @return int
    */
    public function Update($table, $object, $where)
    {
        $sets = array();

        foreach ($object as $key => $value)
        {
            $key = $this->prepareData($key);

            if ($value === null)
            {
                $sets[] = "$key=NULL";
            }
            else
            {
                $value = $this->prepareData($value);
                $sets[] = "$key='$value'";
            }
        }

        $sets_s = implode(',', $sets);
        $query  = "UPDATE $table SET $sets_s WHERE $where";
        $result = mysql_query($query);

        if (!$result) {
            return false;
        }
        else {
            $rows = mysql_affected_rows();
            return $rows ? $rows : 1 ;
        }
    }

    /**
    * Delete
    * @param $table
    * @param $where
    * @return int
    */
    public function Delete($table, $where)
    {
        $query = "DELETE FROM $table WHERE $where";
        $result = mysql_query($query);

        if (!$result)
            die(mysql_error());

        return mysql_affected_rows();
    }

        /**
    * Подготовка данных перед добавлением в БД
    * @param $value
    * @return string
    */
    public function prepareData($value){
        $value = trim(htmlspecialchars($value));
        return mysql_real_escape_string($value . '');
    }
    
    /**
     * Проверка наличия записи в БД по полю
     * @param $table
     * @param $field
     * @param $val
     * @return bool
     */
    public function rowExists($table, $field, $val){
        $val = (is_integer($val)) ? $val : "'".$this->prepareData($val)."'";

        $sql = "
            SELECT COUNT(*)
            FROM ".$table."
            WHERE ".$field." = ".$val."
        ";

        $result = mysql_query($sql);

        if ($result) {
            $count = mysql_fetch_array($result);
            return (!empty($count[0])) ? true : false;
        }
        else {
            return false;
        }
    }
}


