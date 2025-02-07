<?php
    namespace App\Models;
    use App\Models\Model;
    use App\Models\CRUDModel;
    class AccountModel extends Model implements CRUDModel{
        private $account;
        
        public function __construct()
        {
            $this->account = new Model("authme");
        }
        private function computeHash($password, $salt = null ){
            if ($salt == null){
                $salt = bin2hex(random_bytes(8));
            }
            $firstHash = hash('sha256', $password);
            $combined = $firstHash . $salt;
            $secondHash = hash('sha256', $combined);
            $finalHash = "\$SHA\$" . $salt . "$" . $secondHash;
            
            return $finalHash;
        }
        private function comparePassword($password, $hashedPassword) {
            $parts = explode('$', $hashedPassword);
            if (count($parts) === 4) {
                $salt = $parts[2];
                $computedHash = $this->computeHash($password, $salt);
                return hash_equals($hashedPassword, $computedHash);
            }
            return false;
        }
        public function getAll(){
            $sql = "SELECT * FROM authme JOIN role ON authme.role_id = role.role_id";
            $this->account->setSQL($sql);
            return $this->account->all();
        }
        public function getOne($options){
            $sql = "SELECT * FROM authme JOIN role ON authme.role_id = role.role_id WHERE id=? or realname=?";
            $this->account->setSQL($sql);
            return $this->account->first($options);
        }
        private function checkIssetEmail($options){
            $sql = "SELECT email FROM authme WHERE email=?";
            $this->account->setSQL($sql);
            return $this->account->first($sql);
        }
        public function post($options){
            if (count($this->checkIssetEmail($options[2])) !== 0){
                return ['status'=>False,'message'=>"Email đã tồn tại"];
            }
            if (count($this->getOne([null,$options[0]])) !== 0) {
                return ['status'=>False,'message'=>"Tài khoản đã tồn tại"];
            }

            $options[1] = $this->computeHash($options[1]);
            $options[] = strtolower($options[0]);
            $options[] = time() * 1000;
            $options[] = $_SERVER['REMOTE_ADDR'];
            $sql = "INSERT INTO authme (realname, password, email, username, regdate, regip) VALUES (?,?,?,?,?,?)";
            $this->account->setSQL($options);
            return $this->account->execute($options);
        }
    }
?>