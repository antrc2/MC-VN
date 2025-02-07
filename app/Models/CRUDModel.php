<?php
    namespace App\Models;
    interface CRUDModel{
        public function getAll();
        public function getOne($options);
        // public function post($options);
        // public function put($options);
        // public function delete($options);
    }
?>