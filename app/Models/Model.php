<?php
namespace App\Models;

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct() {
        $this->connect();
    }
    
    protected function connect() {
        try {
            $this->db = new \PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (\PDOException $e) {
            throw new \Exception("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
    }
    
    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }
    
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create(array $data) {
        $data = $this->filterFillable($data);
        
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$fields}) VALUES ({$values})");
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, array $data) {
        $data = $this->filterFillable($data);
        
        $fields = array_map(function($field) {
            return "{$field} = ?";
        }, array_keys($data));
        
        $fields = implode(', ', $fields);
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = ?");
        $values = array_values($data);
        $values[] = $id;
        
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    public function where($field, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    public function whereIn($field, array $values) {
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} IN ({$placeholders})");
        $stmt->execute($values);
        return $stmt->fetchAll();
    }
    
    public function orderBy($field, $direction = 'ASC') {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$field} {$direction}");
        return $stmt->fetchAll();
    }
    
    public function limit($limit, $offset = 0) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    protected function filterFillable(array $data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    public function commit() {
        return $this->db->commit();
    }
    
    public function rollBack() {
        return $this->db->rollBack();
    }
} 