<?php

class Report {
    private Database $db;

    private $validSortOrders = ['ASC', 'DESC'];

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getOverpayments(string $sortBy = 'amount', string $order = 'ASC'): array {
        $validSortColumns = ['company_name', 'amount'];

        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'overpayment_amount';
        }

        if (!in_array($order, $this->validSortOrders)) {
            $order = 'ASC';
        }

        $sql = "SELECT o.overpayment_amount, c.company_name
        FROM overpayments o
        JOIN customers c
        ON o.customer_id = c.id
        ORDER BY $sortBy $order        
        ";

        return $this->db->query($sql);
    }

    public function getUnderpayments(string $sortBy = 'outstanding_amount', string $order = 'ASC', string $filterCompany = ''): array {
        $validSortColumns = ['company_name', 'outstanding_amount'];

        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'outstanding_amount';
        }

        $sql = "SELECT i.invoice_number, c.company_name, i.total_amount - IFNULL(SUM(p.payment_amount), 0) AS outstanding_amount
                FROM invoices i
                JOIN customers c ON i.customer_id = c.id
                LEFT JOIN payments p ON i.id = p.invoice_id
                GROUP BY i.id, c.company_name
                HAVING outstanding_amount > 0";

        if ($filterCompany) {
            $sql .= " AND c.company_name LIKE :filterCompany";
        }

        $sql .= " ORDER BY $sortBy $order";

        $params = [];
        if ($filterCompany) {
          $params['filterCompany'] = "%$filterCompany%";
        }

        return $this->db->query($sql, $params);
    }

    public function getOverdueInvoices(string $sortBy = 'due_date', string $order = 'ASC', string $filterCompany = ''): array {
        $validSortColumns = ['company_name', 'due_date'];

        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'due_date';
        }

        $sql = "SELECT i.invoice_number, c.company_name, i.due_date, i.total_amount - IFNULL(SUM(p.payment_amount), 0) AS outstanding_amount
        FROM invoices i
        JOIN customers c ON i.customer_id = c.id
        LEFT JOIN payments p ON i.id = p.invoice_id
        GROUP BY i.id, c.company_name, i.due_date, i.total_amount
        HAVING i.due_date < NOW() AND outstanding_amount > 0";

        $params = [];

        if ($filterCompany) {
          $sql.=" AND c.company_name LIKE :filterCompany";

          $params['filterCompany'] = "%$filterCompany%";
        }

        $sql.=" ORDER BY $sortBy $order";

        return $this->db->query($sql, $params);
    }
}
