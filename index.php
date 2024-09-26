<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
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
          value="company_name">
          Nazwa firmy
        </option>

        <option
          value="overpayment_amount"
          >
          Kwota nadpłaty
        </option>
      </select>
    </div>

    <div class="">
      <label for="order_overpayments">
        Kolejność:
      </label>

      <select id="order_overpayments" name="order_overpayments">
        <option

          value="ASC">
          Rosnąco

        </option>

        <option

          value="DESC">
          Malejąco

        </option>
      </select>
    </div>

    <button type="submit" name="report" value="overpayments">Wyświetl</button>
  </form>

  <h2>Niedopłaty</h2>

  <form method="GET" action="">
    <label for="sort_underpayments">Sortuj według:</label>
    <select id="sort_underpayments" name="sort_underpayments">
      <option
        value="company_name">
        Nazwa firmy
      </option>

      <option
        value="outstanding_amount">
        Kwota niedopłaty
      </option>
    </select>

    <label for="order_underpayments">Kolejność:</label>
    <select id="order_underpayments" name="order_underpayments">
      <option
        value="ASC">
        Rosnąco
      </option>

      <option
        value="DESC">
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
    <select id="sort_overdue" name="sort_overdue">
      <option
        value="company_name">
        Nazwa firmy
      </option>

      <option

        value="due_date">
        Termin płatności
      </option>
    </select>

    <label for="order_overdue">Kolejność:</label>
    <select id="order_overdue" name="order_overdue">
      <option
        value="ASC">
        Rosnąco
      </option>

      <option
        value="DESC">
        Malejąco
      </option>
    </select>


    <button type="submit" name="report" value="overdue">Wyświetl</button>
  </form>

  <hr>

  <?php
    require './Database.php';
    require './Report.php';

    $db = new Database('db', 'report', 'root', 'dbpass');
    $report = new Report($db);

    if (isset($_GET['report'])) {
      $report_type = $_GET['report'];
      $DEFAULT_ORDER = 'ASC';

      switch($report_type) {
        case 'overpayments':
          $sortBy = $_GET['sort_overpayments'] === 'overpayment_amount'
            ? 'overpayment_amount'
            : 'company_name';

          if ($_GET['order_overpayments'] === 'DESC') {
            $order = 'DESC';
          }

          $rows = $report->getOverpayments($sortBy, isset($order) ? $order : $DEFAULT_ORDER);
          break;
      }
    }

    foreach ($rows as $row) {
      echo "<div>
      <span>{$row['company_name']}</span>
      <span>{$row['overpayment_amount']}</span>
      </div>";
    }
  ?>
</body>

</html>