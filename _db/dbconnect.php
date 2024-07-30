<?php
class connexionDB {
    private $host    = 'localhost';
    private $name    = 'crud'; 
    private $user    = 'root';       
    private $pass    = '';         
    private $connexion;
    function __construct($host = null, $name = null, $user = null, $pass = null){
        if($host != null){
            $this->host = $host;           
            $this->name = $name;           
            $this->user = $user;          
            $this->pass = $pass;
        }
        try{
            $this->connexion = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->name,
                $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES UTF8', 
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        }catch (PDOException $e){
            echo 'Erreur : Impossible de se connecter  à la BDD !';
            die();
        }
    }
    public function getConnection() {
        return $this->connexion;
    }
}
$db = new connexionDB();
$conn = $db->getConnection();
?>