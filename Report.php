<?php 

class Report {
  private Database $db;
  private $valid_sort_orders = ['ASC', 'DESC'];

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  function getOverpayments($sortBy, $order): array {
    return $this->db->query("
      SELECT c.company_name, o.overpayment_amount FROM overpayments o
      JOIN customers c
      ON o.customer_id = c.id
      ORDER BY $sortBy $order
    ");
  }

  function getUnderpayments($sortBy, $order): array {
    $sql = "SELECT i.invoice_number, c.company_name, i.total_amount - IFNULL(SUM(p.payment_amount), 0) as outstanding_amount
      FROM invoices i
      JOIN customers c ON i.customer_id = c.id
      LEFT JOIN payments p on i.id = p.invoice_id
      GROUP BY i.id, c.company_name
      HAVING outstanding_amount > 0
    ";
    return $this->db->query($sql);
  }
}