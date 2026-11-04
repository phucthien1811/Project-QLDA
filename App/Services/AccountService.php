<?php
require_once __DIR__ . '/../Repositories/AccountRepository.php';

class AccountService
{
    private AccountRepository $repo;

    public function __construct()
    {
        $this->repo = new AccountRepository();
    }

    public function loginLegacy(string $username, string $password)
    {
        $user = $this->repo->findActiveByUsername($username);
        if (!$user) {
            return null;
        }

        if (md5($password) === $user['MatKhau'] || password_verify($password, $user['MatKhau'])) {
            return $user;
        }

        return null;
    }

    public function login(string $username, string $password): array
    {
        if ($username === '' || $password === '') {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
        }

        $user = $this->loginLegacy($username, $password);
        if (!$user) {
            return ['success' => false, 'message' => 'Tên tài khoản hoặc mật khẩu không chính xác'];
        }

        return ['success' => true, 'message' => 'Đăng nhập thành công', 'user' => $user];
    }

    public function registerLegacy(array $input)
    {
        $ok = $this->repo->createUser([
            'Ho' => $input['Ho'],
            'Ten' => $input['Ten'],
            'GioiTinh' => '',
            'SDT' => $input['SDT'],
            'Email' => $input['Email'],
            'DiaChi' => $input['DiaChi'],
            'TaiKhoan' => $input['TaiKhoan'],
            'MatKhau' => md5($input['MatKhau']),
            'MaQuyen' => 1,
            'TrangThai' => 1,
        ]);

        if (!$ok) {
            return null;
        }

        return $this->loginLegacy($input['TaiKhoan'], $input['MatKhau']);
    }

    public function register(array $input): array
    {
        $required = ['firstName', 'lastName', 'email', 'phone', 'password'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc'];
            }
        }

        if ($this->repo->findByEmail($input['email'])) {
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }

        if ($this->repo->findByPhone($input['phone'])) {
            return ['success' => false, 'message' => 'Số điện thoại đã được sử dụng'];
        }

        $base = explode('@', $input['email'])[0];
        $username = $base . rand(100, 999);
        while ($this->repo->existsUsername($username)) {
            $username = $base . rand(100, 999);
        }

        $ok = $this->repo->createUser([
            'Ho' => $input['firstName'],
            'Ten' => $input['lastName'],
            'GioiTinh' => '',
            'SDT' => $input['phone'],
            'Email' => $input['email'],
            'DiaChi' => $input['address'] ?? '',
            'TaiKhoan' => $username,
            'MatKhau' => password_hash($input['password'], PASSWORD_DEFAULT),
            'MaQuyen' => 1,
            'TrangThai' => 1,
        ]);

        if (!$ok) {
            return ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại'];
        }

        $user = $this->repo->findActiveByUsername($username);
        return $user
            ? ['success' => true, 'message' => 'Đăng ký thành công', 'user' => $user]
            : ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại'];
    }

    public function updateProfile(int $currentUserId, int $targetUserId, array $data): array
    {
        if ($currentUserId !== $targetUserId) {
            return ['success' => false, 'message' => 'Không có quyền cập nhật'];
        }

        if ($this->repo->isEmailTakenByAnother($data['Email'] ?? '', $targetUserId)) {
            return ['success' => false, 'message' => 'Email đã được sử dụng bởi tài khoản khác'];
        }

        if ($this->repo->isPhoneTakenByAnother($data['SDT'] ?? '', $targetUserId)) {
            return ['success' => false, 'message' => 'Số điện thoại đã được sử dụng bởi tài khoản khác'];
        }

        $ok = $this->repo->updateProfile($targetUserId, [
            'Ho' => $data['Ho'] ?? '',
            'Ten' => $data['Ten'] ?? '',
            'GioiTinh' => $data['GioiTinh'] ?? '',
            'NgaySinh' => ($data['NgaySinh'] ?? '') !== '' ? $data['NgaySinh'] : null,
            'Email' => $data['Email'] ?? '',
            'SDT' => $data['SDT'] ?? '',
            'DiaChi' => $data['DiaChi'] ?? '',
        ]);

        return $ok
            ? ['success' => true, 'message' => 'Cập nhật thông tin thành công']
            : ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại'];
    }

    public function changePassword(int $currentUserId, int $targetUserId, string $currentPassword, string $newPassword): array
    {
        if ($currentUserId !== $targetUserId) {
            return ['success' => false, 'message' => 'Không có quyền thay đổi'];
        }

        $user = $this->repo->findById($targetUserId);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy tài khoản'];
        }

        $passwordValid = (md5($currentPassword) === $user['MatKhau']) || password_verify($currentPassword, $user['MatKhau']);
        if (!$passwordValid) {
            return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng'];
        }

        $ok = $this->repo->updatePassword($targetUserId, password_hash($newPassword, PASSWORD_DEFAULT));
        return $ok
            ? ['success' => true, 'message' => 'Đổi mật khẩu thành công']
            : ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại'];
    }

    public function getMyOrders(int $userId): array
    {
        $orders = $this->repo->getOrdersByUser($userId);
        foreach ($orders as &$order) {
            $order['TrangThaiText'] = $this->statusText((int) $order['TrangThai']);
            $order['TrangThaiClass'] = $this->statusClass((int) $order['TrangThai']);
            $order['NgayLapFormatted'] = date('d/m/Y H:i', strtotime($order['NgayLap']));
            $order['TongTienFormatted'] = number_format((float) $order['TongTien'], 0, ',', '.') . 'đ';
        }
        return ['success' => true, 'data' => $orders];
    }

    public function getOrderDetail(int $userId, int $orderId): array
    {
        if (!$orderId) {
            return ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
        }

        $order = $this->repo->getOwnedOrder($orderId, $userId);
        if (!$order) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
        }

        $order['TrangThaiText'] = $this->statusText((int) $order['TrangThai']);
        $order['TrangThaiClass'] = $this->statusClass((int) $order['TrangThai']);
        $order['NgayLapFormatted'] = date('d/m/Y H:i', strtotime($order['NgayLap']));
        $order['TongTienFormatted'] = number_format((float) $order['TongTien'], 0, ',', '.') . 'đ';

        $products = $this->repo->getOrderDetails($orderId);
        foreach ($products as &$p) {
            $p['ThanhTien'] = $p['SoLuong'] * $p['DonGia'];
            $p['ThanhTienFormatted'] = number_format($p['ThanhTien'], 0, ',', '.') . 'đ';
            $p['DonGiaFormatted'] = number_format($p['DonGia'], 0, ',', '.') . 'đ';
        }

        return ['success' => true, 'order' => $order, 'products' => $products];
    }

    private function statusText(int $status): string
    {
        $map = [
            0 => 'Đã hủy',
            1 => 'Chờ xác nhận',
            2 => 'Đã xác nhận',
            3 => 'Đã lên đơn',
            4 => 'Đang giao hàng',
            5 => 'Hoàn thành',
        ];
        return $map[$status] ?? 'Không xác định';
    }

    private function statusClass(int $status): string
    {
        $map = [
            0 => 'cancelled',
            1 => 'pending',
            2 => 'confirmed',
            3 => 'processing',
            4 => 'shipping',
            5 => 'completed',
        ];
        return $map[$status] ?? 'pending';
    }
}

