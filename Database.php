<?php

class Database
{
  private PDO $pdo;

  public function __construct(
    string $host,
    string $db_name,
    string $db_username,
    string $db_password
  ) {
    try {
      $this->pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_username, $db_password);

      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      throw new Exception("Could not connect to the database:" . $e->getMessage());
    }
  }

  public function query(string $sql, array $params = []): array {
    $pdo_statement = $this->pdo->prepare($sql);

    $pdo_statement->execute($params);

    return $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
  }
}
