<?php
require_once __DIR__ . '/../../Backend/Database/DbDriver.php';

class AccountRepository
{
    private DB_driver $db;

    public function __construct()
    {
        $this->db = new DB_driver();
        $this->db->connect();
    }

    public function findActiveByUsername(string $username)
    {
        $username = $this->escape($username);
        return $this->db->get_row("SELECT * FROM nguoidung WHERE TaiKhoan='$username' AND TrangThai=1");
    }

    public function findByEmail(string $email)
    {
        $email = $this->escape($email);
        return $this->db->get_row("SELECT * FROM nguoidung WHERE Email='$email'");
    }

    public function findByPhone(string $phone)
    {
        $phone = $this->escape($phone);
        return $this->db->get_row("SELECT * FROM nguoidung WHERE SDT='$phone'");
    }

    public function existsUsername(string $username): bool
    {
        $username = $this->escape($username);
        return $this->db->get_row("SELECT MaND FROM nguoidung WHERE TaiKhoan='$username'") !== false;
    }

    public function createUser(array $data): bool
    {
        return (bool) $this->db->insert('nguoidung', $data);
    }

    public function findById(int $id)
    {
        return $this->db->get_row("SELECT * FROM nguoidung WHERE MaND = $id");
    }

    public function isEmailTakenByAnother(string $email, int $excludeId): bool
    {
        $email = $this->escape($email);
        return $this->db->get_row("SELECT MaND FROM nguoidung WHERE Email='$email' AND MaND != $excludeId") !== false;
    }

    public function isPhoneTakenByAnother(string $phone, int $excludeId): bool
    {
        if ($phone === '') {
            return false;
        }
        $phone = $this->escape($phone);
        return $this->db->get_row("SELECT MaND FROM nguoidung WHERE SDT='$phone' AND MaND != $excludeId") !== false;
    }

    public function updateProfile(int $id, array $data): bool
    {
        return (bool) $this->db->update('nguoidung', $data, "MaND = $id");
    }

    public function updatePassword(int $id, string $hash): bool
    {
        return (bool) $this->db->update('nguoidung', ['MatKhau' => $hash], "MaND = $id");
    }

    public function getOrdersByUser(int $userId): array
    {
        $sql = "SELECT h.*, 
                (SELECT COUNT(*) FROM chitiethoadon WHERE MaHD = h.MaHD) as SoSanPham,
                (SELECT ct.HinhAnh FROM chitiethoadon ct WHERE ct.MaHD = h.MaHD LIMIT 1) as HinhAnhDau
                FROM hoadon h 
                WHERE h.MaND = $userId 
                ORDER BY h.MaHD DESC";
        return $this->db->get_list($sql);
    }

    public function getOwnedOrder(int $orderId, int $userId)
    {
        return $this->db->get_row("SELECT * FROM hoadon WHERE MaHD = $orderId AND MaND = $userId");
    }

    public function getOrderDetails(int $orderId): array
    {
        return $this->db->get_list("SELECT * FROM chitiethoadon WHERE MaHD = $orderId");
    }

    private function escape(string $value): string
    {
        return $this->db->__conn->real_escape_string($value);
    }
}

