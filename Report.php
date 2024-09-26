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
}