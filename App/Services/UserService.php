<?php
require_once __DIR__ . '/../Repositories/UserRepository.php';

class UserService
{
    private UserRepository $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();
    }

    public function getAll(): array
    {
        return ['success' => true, 'data' => $this->repo->getAll()];
    }

    public function getOne(int $id): array
    {
        if (!$id) {
            return ['success' => false, 'message' => 'Thiếu mã người dùng'];
        }
        $user = $this->repo->getOne($id);
        return $user ? ['success' => true, 'data' => $user] : ['success' => false, 'message' => 'Không tìm thấy người dùng'];
    }

    public function add(array $input): array
    {
        $ho = trim($input['Ho'] ?? '');
        $ten = trim($input['Ten'] ?? '');
        $email = trim($input['Email'] ?? '');
        if ($ho === '' || $ten === '' || $email === '') {
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin'];
        }
        if ($this->repo->existsEmail($email)) {
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }

        $ok = $this->repo->add([
            'Ho' => $ho,
            'Ten' => $ten,
            'GioiTinh' => $input['GioiTinh'] ?? 'Nam',
            'SDT' => trim($input['SDT'] ?? ''),
            'Email' => $email,
            'DiaChi' => trim($input['DiaChi'] ?? ''),
            'TaiKhoan' => trim($input['TaiKhoan'] ?? '') ?: $email,
            'MatKhau' => password_hash($input['MatKhau'] ?? '123456', PASSWORD_DEFAULT),
            'MaQuyen' => (int) ($input['MaQuyen'] ?? 1),
            'TrangThai' => (int) ($input['TrangThai'] ?? 1),
        ]);

        return $ok
            ? ['success' => true, 'message' => 'Thêm khách hàng thành công']
            : ['success' => false, 'message' => 'Không thể thêm khách hàng'];
    }

    public function update(array $input): array
    {
        $id = (int) ($input['MaND'] ?? 0);
        if (!$id) {
            return ['success' => false, 'message' => 'Thiếu mã người dùng'];
        }

        $ho = trim($input['Ho'] ?? '');
        $ten = trim($input['Ten'] ?? '');
        $email = trim($input['Email'] ?? '');
        if ($ho === '' || $ten === '' || $email === '') {
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin'];
        }
        if ($this->repo->existsEmail($email, $id)) {
            return ['success' => false, 'message' => 'Email đã được sử dụng bởi tài khoản khác'];
        }

        $data = [
            'Ho' => $ho,
            'Ten' => $ten,
            'GioiTinh' => $input['GioiTinh'] ?? 'Nam',
            'SDT' => trim($input['SDT'] ?? ''),
            'Email' => $email,
            'DiaChi' => trim($input['DiaChi'] ?? ''),
            'TaiKhoan' => trim($input['TaiKhoan'] ?? '') ?: $email,
            'MaQuyen' => (int) ($input['MaQuyen'] ?? 1),
            'TrangThai' => (int) ($input['TrangThai'] ?? 1),
        ];
        if (!empty($input['MatKhau'])) {
            $data['MatKhau'] = password_hash($input['MatKhau'], PASSWORD_DEFAULT);
        }

        $this->repo->update($id, $data);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public function delete(int $id): array
    {
        if (!$id) {
            return ['success' => false, 'message' => 'Thiếu mã người dùng'];
        }

        if ($this->repo->countOrders($id) > 0) {
            $this->repo->update($id, ['TrangThai' => 0]);
            return ['success' => true, 'message' => 'Khách hàng có đơn hàng, đã chuyển sang trạng thái khóa'];
        }

        $this->repo->delete($id);
        return ['success' => true, 'message' => 'Xóa khách hàng thành công'];
    }

    public function toggleStatus(int $id): array
    {
        if (!$id) {
            return ['success' => false, 'message' => 'Thiếu mã người dùng'];
        }
        $status = $this->repo->getStatus($id);
        if ($status !== null) {
            $this->repo->update($id, ['TrangThai' => $status === 1 ? 0 : 1]);
        }
        return ['success' => true, 'message' => 'Cập nhật trạng thái thành công'];
    }
}

