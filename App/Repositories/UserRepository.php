<?php
require_once __DIR__ . '/../../Backend/Database/DbDriver.php';

class UserRepository
{
    private DB_driver $db;

    public function __construct()
    {
        $this->db = new DB_driver();
        $this->db->connect();
    }

    public function getAll(): array
    {
        return $this->db->get_list("SELECT MaND, Ho, Ten, GioiTinh, SDT, Email, DiaChi, TaiKhoan, MaQuyen, TrangThai, google_id FROM nguoidung ORDER BY MaND DESC");
    }

    public function getOne(int $id)
    {
        return $this->db->get_row("SELECT MaND, Ho, Ten, GioiTinh, SDT, Email, DiaChi, TaiKhoan, MaQuyen, TrangThai, google_id FROM nguoidung WHERE MaND = $id");
    }

    public function existsEmail(string $email, ?int $excludeId = null): bool
    {
        $email = $this->escape($email);
        $whereExclude = $excludeId ? " AND MaND != $excludeId" : '';
        return $this->db->get_row("SELECT MaND FROM nguoidung WHERE Email = '$email'$whereExclude") !== false;
    }

    public function countOrders(int $userId): int
    {
        $row = $this->db->get_row("SELECT COUNT(*) as count FROM hoadon WHERE MaND = $userId");
        return (int) ($row['count'] ?? 0);
    }

    public function add(array $data): bool
    {
        return (bool) $this->db->insert('nguoidung', $data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) $this->db->update('nguoidung', $data, "MaND = $id");
    }

    public function delete(int $id): bool
    {
        return (bool) $this->db->remove('nguoidung', "MaND = $id");
    }

    public function getStatus(int $id): ?int
    {
        $row = $this->db->get_row("SELECT TrangThai FROM nguoidung WHERE MaND = $id");
        return $row ? (int) $row['TrangThai'] : null;
    }

    private function escape(string $value): string
    {
        return $this->db->__conn->real_escape_string($value);
    }
}

