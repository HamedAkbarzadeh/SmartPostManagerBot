<?php

namespace src\app\Classes;

use Exception;
// use src\app\Trait\Log;
require_once '../../includes/DBConnection.php';

use src\includes\DBConnection;

class DB
{
    // use Log;
    private $tableName;
    private $sql = "";
    private $values = [];
    private $wheres = "";
    private $orderByProperty = "";
    private $allowMethods = ['table'];
    private $callSelect = false;

    public function table($table)
    {

        if (in_array('table', $this->allowMethods)) {
            $this->tableName = $table;
            $this->allowMethods = ['select', 'insert', 'where', 'orWhere', 'find', 'get', 'first', 'find'];
            return $this;
        }
        return false;
    }

    /////crud/////

    //read
    public function select($columns = '*')
    {

        if (in_array('select', $this->allowMethods)) {
            $this->callSelect = true;
            if (is_array($columns)) {
                $columns = implode(', ', $columns);
            }
            $this->sql = "SELECT " . $columns . " FROM `" . $this->tableName . "` ";
            $this->allowMethods = ['join', 'where', 'orWhere', 'orderBy', 'get', 'first'];

            return $this;
        }
        return false;
    }
    public function join($table)
    {
        if (in_array('join', $this->allowMethods)) {
            $this->sql .= " JOIN " . $table . " ";
            $this->allowMethods = ['join', 'on', 'where', 'orWhere', 'orderBy', 'get', 'first'];
            return $this;
        }
        return false;
    }
    public function on($table1, $column1, $table2, $column2)
    {
        if (in_array('on', $this->allowMethods)) {
            $this->sql .= " ON " . $table1 . "." . $column1 . " = " . $table2 . "." . $column2 . " ";
            $this->allowMethods = ['join', 'on', 'where', 'orWhere', 'orderBy', 'get', 'first'];
            return $this;
        }
        return false;
    }


    //create
    public function insert($fields, $values)
    {
        if (in_array('insert', $this->allowMethods)) {
            if (is_array($fields) && is_array($values)) {
                $this->sql = "INSERT INTO `" . $this->tableName . "` ( " . implode(", ", $fields) . " , created_at ) VALUES (:" . implode(", :", $fields) . " , now()) ";
                $this->values = array_combine($fields, $values);
                $result = $this->runSql();
                if ($result) {
                    // return DBConnection::getInstance()->lastInsertId();

                    $lastInsertId = DBConnection::getInstance()->lastInsertId();
                    return $lastInsertId;
                }
                return false;
            }
            // "INSERT INTO MyGuests (firstname, lastname, email) VALUES ('John', 'Doe', 'john@example.com')"
        }
    }
    //update
    public function update($fields, $values)
    {
        $this->sql = "UPDATE `$this->tableName` SET ";
        $new_values = [];
        if (in_array('update', $this->allowMethods) && $this->wheres != "") {
            if (is_array($fields) && is_array($values)) {
                foreach (array_combine($fields, $values) as $field => $value) {
                    if ($value) {
                        $this->sql .= "`$field` = ? , ";
                        $new_values[] = $value;
                    } elseif ($value === 0) {
                        $this->sql .= "`$field` = 0 , ";
                    } elseif ($value === false) {
                        $this->sql .= "`$field` = false , ";
                    } else {
                        $this->sql .= "`$field` = NULL , ";
                    }
                }
            } else {
                $this->sql .= "`$fields` = ? , ";
                $new_values[] = $values;
            }
            $this->sql .= "`updated_at` = now()";
            $this->values = array_merge($new_values, $this->values);
            $result = $this->runSql();
            if ($result) {
                // return DBConnection::getInstance()->lastInsertId();

                $lastInsertId = DBConnection::getInstance()->lastInsertId();
                return $lastInsertId;
            }
        }
        return false;
    }
    //delete
    public function delete()
    {
        //DELETE FROM `basket` WHERE `id` = ?
        if (in_array('delete', $this->allowMethods) && $this->wheres != "") {
            $this->sql = "DELETE FROM `$this->tableName`";
            $result = $this->runSql();
            if ($result) {
                return true;
            }
        }
        return false;
    }


    public function find($id)
    {
        if (in_array('find', $this->allowMethods)) {
            $this->sql = "SELECT * FROM `$this->tableName`";
            $this->wheres = "`id` = ?";
            $this->values[] = $id;
            $result = $this->runSql();
            return $result->fetch();
        }
        return false;
    }
    public function where($column, $condition, $value = null)
    {
        if (in_array('where', $this->allowMethods)) {

            $startWhere = $this->wheres != "" ? " AND " : "";

            if ($value == null) {
                $this->wheres .= $startWhere . $column . " = ?";
                $this->values[] = $condition;
            } else {
                $this->wheres .= $startWhere . $column . " $condition ? ";
                $this->values[] = $value;
            }
            $this->allowMethods = ['where', 'orWhere', 'orderBy', 'get', 'first', 'update', 'delete'];
            return $this;
        }
        // $this->setLog("Order By Eror", "../Log/DB.log", 'EMERGENCY');
        return false;
    }

    public function orWhere($columsn, $condition, $values = null)
    {
        if (in_array('orWhere', $this->allowMethods)) {
            $startOrWhere = $this->wheres != "" ? " OR " : "";

            if ($values == null) {
                $this->wheres .= $startOrWhere . $columsn . " = ?";
            } else {
                $this->wheres .= $startOrWhere . $columsn . " $condition ?";
            }
            $this->values[] = $values;
            $this->allowMethods = ['where', 'orWhere', 'orderBy', 'get', 'first', 'update', 'delete'];
            return $this;
        }
        return false;
    }
    public function orderBy($column, $asc = 'desc')
    {
        if (in_array('orderBy', $this->allowMethods)) {
            $this->orderByProperty = ' ORDER BY ' . $column . " " . $asc;
            $this->allowMethods = ['get', 'first'];

            return $this;
        }
        // $this->setLog("Where Eror", "../Log/DB.log", 'EMERGENCY');
        return false;
    }
    public function first()
    {
        if (in_array('first', $this->allowMethods)) {
            if ($this->callSelect == false) {
                $this->select();
            }
            $stmt = $this->runSql();
            return $stmt->fetch();
        }
        return false;
    }

    public function get()
    {
        if (in_array('get', $this->allowMethods)) {
            if ($this->callSelect == false) {
                $this->select();
            }
            $stmt = $this->runSql();
            return $stmt->fetchAll();
        }
        return false;
    }
    public function runSql()
    {
        try {
            if (!empty($this->values)) {
                $fullSql = $this->sql . " " . $this->orderByProperty;
                if ($this->wheres != "") {
                    $fullSql .= " WHERE " . $this->wheres;
                }
                $stmt = DBConnection::getInstance()->prepare($fullSql);
                $stmt->execute($this->values);
            } else {
                $fullSql = $this->sql . $this->orderByProperty;
                $stmt = DBConnection::getInstance()->query($fullSql);
            }
            $this->default();
            return $stmt;
        } catch (Exception $e) {
            // $this->setLog("DATABASE Eror :" . $e->getMessage(), "../Log/DB.log", 'EMERGENCY');

            return false;
        }
    }

    public function default()
    {
        $this->sql = "";
        $this->values = [];
        $this->wheres = "";
        $this->orderByProperty = "";
        $this->allowMethods = ['table'];
        $this->callSelect = false;
    }
}

/// example tutorial for crud ///

// $sql = new DB();
// var_dump($sql->table('posts')->select()->where('id' , 2)->first());
// var_dump($sql->table('posts')->select()->where('id' , 2)->first());
// var_dump($sql->table('posts')->insert(['name'] ,['ali']));
// var_dump($sql->table('postss')->where('id', 2)->delete());

//relation

// var_dump($sql->table('posts')->select()->join('files')->on('posts' , 'id' , 'files' , 'post_id')->where('posts.id' , 2)->first());
// var_dump($sql->table('posts')->select()->join('files')->on('posts' , 'id' , 'files' , 'post_id')->get());