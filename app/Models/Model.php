<?php
namespace App\Models; 
use PDO;
use PDOException;
// Nhận thấy bất cứ class nào trong model đều cần kết đến CSDL
// Và đều cần thực hiện 1 số thao tác hoặc truy vấn CSDL vậy nên 
// Cần 1 class chung tạo các tài nguyên để dễ thực trong quá trình làm
class Model{
    // Thuộc tính 
    // Cấu hình kết nối cơ sơ dữ liệu
    // private $host = 'localhost'; // Địa chỉ IP của máy chủ CSDL
    // private $dbname = 'wd19309'; // Tên CSDL
    // private $username = 'root'; // Tên đăng nhập mysql
    // private $password = ''; // Mật khẩu đăng nhập mysql
    // Xử lý
    private $pdo; // Đối tượng PDO để kết nối
    private $sql; // Câu lệnh SQL hiện tại
    private $sta; // Đối tượng PDOStatemet sau thực thi 
    // Khai báo hằng số để phân biệt chế độ truy vấn
    const FETCH_ALL = 'all'; // Lấy nhiều 
    const FETCH_FIRST = 'first'; // Lấy 1
    // Phương khởi tạo
    public function __construct($dbname = "admin"){
        $this->pdo = $this->getConnection($dbname);
     }
    // Phương thức kết nối
    private function getConnection($dbname){
        try{
            $conn = new PDO("mysql:host={$_ENV['HOST']};
            dbname={$_ENV['DBNAME']}",
            $_ENV['USERNAME'], 
            $_ENV['PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
            ]);

            $stmt = $conn->prepare("SELECT * FROM list_of_database WHERE name=:name");
            $stmt->execute([':name' => $dbname]);
            $result = $stmt->fetch();

            if ($result) {
                $host = $result['host'];
                $dbname = $result['dbname'];
                $user = $result['user'];
                $pass = $result['pass'];

                return new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            } else {
                return false; // Trả về false nếu không tìm thấy cơ sở dữ liệu
            }
            // echo "Kết nối thành công";
        }catch(PDOException $ex){
            throw new PDOException("Kết nối lỗi".$ex->getMessage());
        }
    }
    // Phương thức thiết lập câu lệnh sql
    protected function setSQL($sql){
        $this->sql = $sql;
    }
    // Phương thức thực thi câu lệnh sql
    protected function execute($options = []){
        try{
            $this->sta = $this->pdo->prepare($this->sql);
            if(!empty($options)){
                foreach($options as $key => $value){
                    $this->sta->bindValue($key+ 1, $value);
                    // Sử dụng bindValue để tự động xác định KDL
                }
            }
            $this->sta->execute();
            return $this->sta;
        }catch(PDOException $ex){
            throw new PDOException("Thực thi lỗi".$ex->getMessage());
        }
    }
    // Phương thức lấy dữ liệu từ câu truy vấn
    // Truy vân -> có nhiều bản ghi, 1 bản ghi (id)
    protected function executeQuery($options = [], $fetchMode = self::FETCH_ALL){
        // Mặc định là lấy nhiều bản ghi
        $result = $this->execute($options);
        if(!$result){
            return false;
        }
        return $fetchMode === self::FETCH_FIRST 
        ?  $result->fetch(PDO::FETCH_OBJ) 
        :  $result->fetchAll(PDO::FETCH_OBJ);
    }
    // lấy kết quả từ truy vấn
    // Lấy nhiều bản ghi
    protected function all($options = []){
        return $this->executeQuery($options, self::FETCH_ALL);
    }
    protected function first($options = []){
        return $this->executeQuery($options, self::FETCH_FIRST);
    }
    // đóng kết nối
    public function __destruct(){
        $this->pdo = null;
        $this->sta = null;
    }
}
?>