<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Raporty</title>
</head>

<body>
  <h1>Raporty</h1>

  <h2>Nadpłaty</h2>

  <form method="GET" action="">
    <div class="">
      <label for="sort_overpayments">
        Sortuj według:
      </label>

      <select id="sort_overpayments" name="sort_overpayments">
        <option
          <?php selectIfCurrent(SortBy::$overpayments, 'company_name') ?>
          value="company_name"
        >
          Nazwa firmy
        </option>

        <option 
          value="overpayment_amount"
          <?php selectIfCurrent(SortBy::$overpayments, 'overpayment_amount') ?>
        >
          Kwota nadpłaty
        </option>
      </select>
    </div>

    <div class="">
      <label for="order_overpayments">Kolejność:</label>

      <select id="order_overpayments" name="order_overpayments">
        <option
          <?php selectIfCurrent(OrderColumn::$overpayments, SortOrder::$asc) ?>
        value="ASC"
        >
          Rosnąco

        </option>

        <option
        <?php selectIfCurrent(OrderColumn::$overpayments, SortOrder::$desc) ?>
        value="DESC"
        >
          Malejąco

        </option>
      </select>
    </div>

    <button type="submit" name="report" value="overpayments">Wyświetl</button>
  </form>

  <h2>Niedopłaty</h2>

  <form method="GET" action="">
    <label for="sort_underpayments">Sortuj według:</label>
    <select name="sort_underpayments">
      <option
        value="company_name"
        <?php selectIfCurrent(SortBy::$underpayments, "company_name") ?>
      >
        Nazwa firmy
      </option>

      <option
        value="outstanding_amount"
        <?php selectIfCurrent(SortBy::$underpayments, "outstanding_amount") ?>
      >
        Kwota niedopłaty
      </option>
    </select>

    <label for="order_underpayments">Kolejność:</label>
    <select name="order_underpayments">
      <option
        value="ASC"
        <?php selectIfCurrent(OrderColumn::$underpayments, SortOrder::$asc) ?>
      >
        Rosnąco
      </option>

      <option
        value="DESC"
        <?php selectIfCurrent(OrderColumn::$underpayments, SortOrder::$desc) ?>
      >
        Malejąco
      </option>
    </select>

    <label for="filter_company">Filtruj po nazwie firmy:</label>
    <input type="text" name="filter_company" />

    <button type="submit" name="report" value="underpayments">Wyświetl</button>
  </form>

  <h2>Nierozliczone faktury po terminie</h2>

  <form method="GET" action="">
    <label for="sort_overdue">Sortuj według:</label>
    <select name="sort_overdue">
      <option
        value="company_name"
        <?php selectIfCurrent(SortBy::$overdue, "company_name") ?>
      >
        Nazwa firmy
      </option>

      <option
        <?php selectIfCurrent(SortBy::$overdue, "due_date") ?>
        value="due_date"
      >
        Termin płatności
      </option>
    </select>

    <label for="order_overdue">Kolejność:</label>
    <select name="order_overdue">
      <option
        value="ASC"
        <?php selectIfCurrent(OrderColumn::$overdue, SortOrder::$asc) ?>
      >
        Rosnąco
      </option>

      <option
        value="DESC"
        <?php selectIfCurrent(OrderColumn::$overdue, SortOrder::$desc) ?>
      >
        Malejąco
      </option>
    </select>


    <button type="submit" name="report" value="overdue">Wyświetl</button>
  </form>

  <hr>

  <?php
  require 'Database.php';
  require 'Report.php';

  // Initialize report and db
  $db_password = 'dbpass';
  $db = new Database('db', 'report', 'root', $db_password);
  $report = new Report($db);

  class SortOrder {
    public static $asc = 'ASC';
    public static $desc = 'DESC';
  }

  class SortBy {
    public static $overpayments = 'sort_overpayments';
    public static $underpayments = 'sort_underpayments';
    public static $overdue = 'sort_overdue';
  }

  class OrderColumn {
    public static $overpayments = 'order_overpayments';
    public static $underpayments = 'order_underpayments';
    public static $overdue = 'order_overdue';
  }

  class DefaultSortingColumn {
    public static $overpayments = 'overpayment_amount';
    public static $underpayments = 'outstanding_amount';
    public static $overdue = 'due_date';
  }

  if (isset($_GET['report'])) {
    // Currently displayed reports: overpayments / underpayments / overdue
    $reportType = $_GET['report'];

    switch ($reportType) {
      case 'overpayments':
        $sortBy = $_GET[SortBy::$overpayments] ?? DefaultSortingColumn::$overpayments;
        $order = $_GET[OrderColumn::$overpayments] ?? SortOrder::$asc;
        $data = $report->getOverpayments($sortBy, $order);
        echo '<h3>Raport nadpłat</h3>';

        foreach ($data as $row) {
          echo "Firma: {$row['company_name']}, Kwota nadpłaty: {$row['overpayment_amount']}<br>";
        }
        break;

      case 'underpayments':
        $sortBy = $_GET[SortBy::$overpayments] ?? DefaultSortingColumn::$underpayments;
        $order = $_GET[OrderColumn::$underpayments] ?? SortOrder::$asc;
        $filterCompany = $_GET['filter_company'] ?? '';
        $data = $report->getUnderpayments($sortBy, $order, $filterCompany);

        echo '<h3>Raport niedopłat</h3>';

        foreach ($data as $row) {
          echo "Firma: {$row['company_name']}, Kwota niedopłaty: {$row['outstanding_amount']}<br>";
        }
        break;

      case 'overdue':
        $sortBy = $_GET[SortBy::$overdue] ?? DefaultSortingColumn::$overdue;
        $order = $_GET[OrderColumn::$overdue] ?? SortOrder::$asc;
        $data = $report->getOverdueInvoices($sortBy, $order);

        echo '<h3>Raport nierozliczonych faktur</h3>';

        foreach ($data as $row) {
          echo "Firma: {$row['company_name']}, Termin płatności: {$row['due_date']}, Niedopłata: {$row['outstanding_amount']}<br>";
        }

        break;
    }
  }

  function selectIfCurrent(string $currentUrlColumn, string $optionValue) {
    if (isset($_GET[$currentUrlColumn]) && $_GET[$currentUrlColumn] === $optionValue) {
      echo 'selected';
    }
  }

  ?>

</body>

</html>