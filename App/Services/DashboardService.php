<?php
require_once __DIR__ . '/../Repositories/DashboardRepository.php';

class DashboardService
{
    private DashboardRepository $repo;

    public function __construct()
    {
        $this->repo = new DashboardRepository();
    }

    public function getStats(): array
    {
        $revenueRow = $this->repo->getRow("SELECT COALESCE(SUM(TongTien), 0) as totalRevenue FROM hoadon WHERE DaThanhToan = 1 AND TrangThai != 0");
        $todayRevenueRow = $this->repo->getRow("SELECT COALESCE(SUM(TongTien), 0) as todayRevenue FROM hoadon WHERE DaThanhToan = 1 AND TrangThai != 0 AND DATE(NgayLap) = CURDATE()");
        $monthRevenueRow = $this->repo->getRow("SELECT COALESCE(SUM(TongTien), 0) as monthRevenue FROM hoadon WHERE DaThanhToan = 1 AND TrangThai != 0 AND MONTH(NgayLap) = MONTH(CURDATE()) AND YEAR(NgayLap) = YEAR(CURDATE())");
        $ordersRow = $this->repo->getRow("SELECT COUNT(*) as totalOrders FROM hoadon");
        $pendingRow = $this->repo->getRow("SELECT COUNT(*) as pendingOrders FROM hoadon WHERE TrangThai = 1");
        $todayOrdersRow = $this->repo->getRow("SELECT COUNT(*) as todayOrders FROM hoadon WHERE DATE(NgayLap) = CURDATE()");
        $customersRow = $this->repo->getRow("SELECT COUNT(*) as totalCustomers FROM nguoidung WHERE MaQuyen = 1");
        $seriesRow = $this->repo->getRow("SELECT COUNT(*) as totalSeries FROM product_series");
        $inStockRow = $this->repo->getRow("SELECT COUNT(DISTINCT ps.id) as inStock FROM product_series ps JOIN product_variants pv ON ps.id = pv.series_id JOIN variant_storage_options vso ON pv.id = vso.variant_id LEFT JOIN variant_colors vc ON vso.id = vc.storage_option_id WHERE vc.stock_quantity > 0 OR vso.stock_quantity > 0");

        $totalSeries = (int) ($seriesRow['totalSeries'] ?? 0);
        return [
            'success' => true,
            'data' => [
                'revenue' => [
                    'total' => (float) ($revenueRow['totalRevenue'] ?? 0),
                    'today' => (float) ($todayRevenueRow['todayRevenue'] ?? 0),
                    'month' => (float) ($monthRevenueRow['monthRevenue'] ?? 0),
                ],
                'orders' => [
                    'total' => (int) ($ordersRow['totalOrders'] ?? 0),
                    'pending' => (int) ($pendingRow['pendingOrders'] ?? 0),
                    'today' => (int) ($todayOrdersRow['todayOrders'] ?? 0),
                ],
                'customers' => [
                    'total' => (int) ($customersRow['totalCustomers'] ?? 0),
                    'newThisMonth' => 0,
                ],
                'products' => [
                    'total' => $totalSeries + $totalSeries,
                    'inStock' => (int) ($inStockRow['inStock'] ?? 0),
                    'series' => $totalSeries,
                ],
            ],
        ];
    }

    public function getRevenueChart(string $period): array
    {
        if ($period === '7days') {
            $sql = "SELECT DATE(NgayLap) as date, COALESCE(SUM(TongTien), 0) as revenue FROM hoadon WHERE DaThanhToan = 1 AND TrangThai != 0 AND NgayLap >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(NgayLap) ORDER BY date ASC";
        } elseif ($period === '30days') {
            $sql = "SELECT DATE(NgayLap) as date, COALESCE(SUM(TongTien), 0) as revenue FROM hoadon WHERE DaThanhToan = 1 AND TrangThai != 0 AND NgayLap >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(NgayLap) ORDER BY date ASC";
        } else {
            $sql = "SELECT DATE_FORMAT(NgayLap, '%Y-%m') as date, COALESCE(SUM(TongTien), 0) as revenue FROM hoadon WHERE DaThanhToan = 1 AND TrangThai != 0 AND NgayLap >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY DATE_FORMAT(NgayLap, '%Y-%m') ORDER BY date ASC";
        }

        $rows = $this->repo->getList($sql);
        $chartData = [];
        foreach ($rows as $row) {
            $chartData[] = ['date' => $row['date'], 'revenue' => (float) $row['revenue']];
        }
        if ($period === '7days') {
            $chartData = $this->fillMissingDates($chartData, 7);
        } elseif ($period === '30days') {
            $chartData = $this->fillMissingDates($chartData, 30);
        }
        return ['success' => true, 'data' => $chartData];
    }

    public function getRecentOrders(int $limit): array
    {
        $sql = "SELECT h.MaHD, h.TongTien, h.TrangThai, h.NgayLap, h.DaThanhToan, h.PhuongThucTT, n.Ho, n.Ten, n.Email
                FROM hoadon h
                LEFT JOIN nguoidung n ON h.MaND = n.MaND
                ORDER BY h.NgayLap DESC
                LIMIT $limit";
        $rows = $this->repo->getList($sql);
        $orders = [];
        foreach ($rows as $row) {
            $product = $this->repo->getRow("SELECT COALESCE(ct.TenSP, 'Sản phẩm') as TenSP FROM chitiethoadon ct WHERE ct.MaHD = " . $row['MaHD'] . " LIMIT 1");
            $customerName = trim(($row['Ho'] ?? '') . ' ' . ($row['Ten'] ?? ''));
            if ($customerName === '') {
                $customerName = 'Khách vãng lai';
            }
            $orders[] = [
                'id' => $row['MaHD'],
                'customer' => $customerName,
                'product' => $product ? $product['TenSP'] : 'N/A',
                'amount' => (float) $row['TongTien'],
                'status' => (int) $row['TrangThai'],
                'paid' => (int) ($row['DaThanhToan'] ?? 0),
                'paymentMethod' => $row['PhuongThucTT'] ?? 'cod',
                'date' => $row['NgayLap'],
            ];
        }
        return ['success' => true, 'data' => $orders];
    }

    public function getTopProducts(int $limit): array
    {
        $sql = "SELECT ps.id, ps.series_name as name, ps.main_image as image, ps.display_price as price,
                       COALESCE(sales.totalSold, 0) as totalSold,
                       COALESCE(sales.totalRevenue, 0) as totalRevenue
                FROM product_series ps
                LEFT JOIN (
                    SELECT 
                        JSON_UNQUOTE(JSON_EXTRACT(ct.SeriesConfig, '$.seriesId')) as seriesId,
                        SUM(ct.SoLuong) as totalSold,
                        SUM(ct.SoLuong * ct.DonGia) as totalRevenue
                    FROM chitiethoadon ct
                    JOIN hoadon h ON ct.MaHD = h.MaHD AND h.TrangThai = 5
                    WHERE ct.SeriesConfig IS NOT NULL
                    GROUP BY JSON_UNQUOTE(JSON_EXTRACT(ct.SeriesConfig, '$.seriesId'))
                ) sales ON ps.id = sales.seriesId
                ORDER BY totalSold DESC, ps.created_at DESC
                LIMIT $limit";
        $rows = $this->repo->getList($sql);
        $products = [];
        foreach ($rows as $row) {
            $products[] = [
                'id' => 'S' . $row['id'],
                'name' => $row['name'],
                'image' => $row['image'],
                'price' => (float) $row['price'],
                'sold' => (int) $row['totalSold'],
                'revenue' => (float) $row['totalRevenue'],
                'source' => 'series',
            ];
        }
        usort($products, fn($a, $b) => $b['sold'] - $a['sold']);
        return ['success' => true, 'data' => array_slice($products, 0, $limit)];
    }

    public function getOrderStatus(): array
    {
        $rows = $this->repo->getList("SELECT TrangThai, COUNT(*) as count FROM hoadon GROUP BY TrangThai");
        $statusData = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($rows as $row) {
            $statusData[(int) $row['TrangThai']] = (int) $row['count'];
        }
        return [
            'success' => true,
            'data' => [
                ['status' => 'Đã hủy', 'count' => $statusData[0], 'color' => '#dc3545'],
                ['status' => 'Chờ xử lý', 'count' => $statusData[1], 'color' => '#ffc107'],
                ['status' => 'Đã xác nhận', 'count' => $statusData[2], 'color' => '#17a2b8'],
                ['status' => 'Đã lên đơn', 'count' => $statusData[3], 'color' => '#6f42c1'],
                ['status' => 'Đang giao', 'count' => $statusData[4], 'color' => '#fd7e14'],
                ['status' => 'Hoàn thành', 'count' => $statusData[5], 'color' => '#28a745'],
            ],
        ];
    }

    private function fillMissingDates(array $data, int $days): array
    {
        $existing = [];
        foreach ($data as $item) {
            $existing[$item['date']] = $item['revenue'];
        }
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $result[] = ['date' => $date, 'revenue' => $existing[$date] ?? 0];
        }
        return $result;
    }
}

