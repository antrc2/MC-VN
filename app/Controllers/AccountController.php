<?php
    namespace App\Controllers;
    use App\Models\AccountModel;
    class AccountController extends AccountModel{
        private $account;
        public function __construct()
        {
            // $this->account = new AccountModel;
            parent::__construct();
        }
        public function listAccount(){
            // echo 'a';
            $accounts = $this->getAll();
            var_dump($accounts);
            
        }
    }
?>