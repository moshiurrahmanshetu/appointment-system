<?php

namespace App\Core;

use PDO;

class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }
    
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    public function findBy($field, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        return $this->db->fetch($sql, ['value' => $value]);
    }
    
    public function where($field, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE {$field} {$operator} :value";
        return $this->db->fetchAll($sql, ['value' => $value]);
    }
    
    public function create($data)
    {
        $data = $this->filterFillable($data);
        
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ':' . $field;
        }, $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->execute($sql, $data);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        
        $fields = array_map(function($field) {
            return "{$field} = :{$field}";
        }, array_keys($data));
        
        $data['id'] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        
        return $this->db->execute($sql, $data);
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }
    
    public function exists($field, $value, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$field} = :value";
        $params = ['value' => $value];
        
        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }
    
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
