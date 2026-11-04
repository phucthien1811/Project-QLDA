<?php
require_once __DIR__ . '/../../Backend/Database/DbDriver.php';

class DashboardRepository
{
    private DB_driver $db;

    public function __construct()
    {
        $this->db = new DB_driver();
    }

    public function getRow(string $sql)
    {
        return $this->db->get_row($sql);
    }

    public function getList(string $sql): array
    {
        return $this->db->get_list($sql);
    }
}

