<?php


namespace app\entities;
use PDO;


class DBwrap {
    private $servername = "localhost";
    private $username = "root";
    private $password = "12345";
    private $dbname = "astro";
    
    private $conn;
    private $prepared;
    private function connect(){
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){throw ($e->getMessage());}}
    public function __construct() {
        if (!isset($this->conn)){$this->connect();}}
    public function __destruct() {$this->conn = null;}
    public function sendSQL($sql,$para_arr) {
        $this->prepared = $this->conn->prepare($sql);
        $this->prepared->execute($para_arr);
    }
    public function sendSQL_varType_forced($sql,$para_arr,$type_mask) {
        $this->prepared = $this->conn->prepare($sql);
        if (count($para_arr) != count($type_mask)){return FALSE;}
        $c = 0;
        foreach ($type_mask as $mask){
            switch ($mask){
                case "s": $type_mask_proc = PDO::PARAM_STR; break;
                case "i": $type_mask_proc = PDO::PARAM_INT; break;
                default: return FALSE; 
            }
            $this->prepared->bindParam($c + 1, $para_arr[$c],$type_mask_proc);
            $c++;
        }
        $this->prepared->execute();
    }
    public function fetch() {
        if (!isset($this->prepared)){return FALSE;}
        $fetched = $this->prepared->fetch(PDO::FETCH_ASSOC);
        if (!$fetched){$this->prepared = null;}
        return $fetched;
    }
}
