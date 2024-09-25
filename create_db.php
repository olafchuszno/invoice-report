<?php

$host = 'db';
$dbname = 'report';
$user = 'root'; 
$pass = 'dbpass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
    DROP TABLE IF EXISTS customers, invoices, invoice_items, payments, overpayments;

    CREATE TABLE customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255) NOT NULL,
        bank_account_number VARCHAR(20),
        nip VARCHAR(20) NOT NULL
    );

    CREATE TABLE invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        invoice_number VARCHAR(50),
        issue_date DATE,
        due_date DATE,
        total_amount DECIMAL(10, 2),
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    );

    CREATE TABLE invoice_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_id INT,
        product_name VARCHAR(255),
        quantity INT,
        price DECIMAL(10, 2),
        FOREIGN KEY (invoice_id) REFERENCES invoices(id)
    );

    CREATE TABLE payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        invoice_id INT,
        payment_title VARCHAR(255),
        payment_amount DECIMAL(10, 2),
        payment_date DATE,
        bank_account_number VARCHAR(20),
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (invoice_id) REFERENCES invoices(id)
    );

    CREATE TABLE overpayments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        overpayment_amount DECIMAL(10, 2),
        overpayment_date DATE,
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    );


    INSERT INTO customers (id, company_name, bank_account_number, nip)
    VALUES
    (1, 'KFC', 123, 123),
    (2, 'MCDONALDS', 456, 456),
    (3, 'Apple', 789, 789),
    (4, 'Samsung', 12343, 23453),
    (5, 'T-mobile', 34213, 2342134),
    (6, 'Play', 34213, 2342134);

    INSERT INTO invoices (customer_id, invoice_number, issue_date, due_date, total_amount)
    VALUES 
    (1, 'INV-0001', '2024-01-10', '2024-02-10', 1000.00),
    (2, 'INV-0002', '2024-01-15', '2024-02-15', 2000.00),
    (3, 'INV-0003', '2024-01-20', '2024-02-20', 3000.00),
    (4, 'INV-0004', '2024-01-25', '2024-02-25', 4000.00),
    (5, 'INV-0005', '2024-09-30', '2024-10-28', 5000.00),
    (6, 'INV-0006', '2024-09-30', '2024-10-28', 1000.00);

    INSERT INTO invoice_items (invoice_id, product_name, quantity, price)
    VALUES 
    (1, 'Product A', 1, 1000.00),
    (2, 'Product B', 1, 2000.00),
    (3, 'Product C', 1, 3000.00),
    (4, 'Product D', 1, 4000.00),
    (5, 'Product E', 1, 2000.00),
    (5, 'Product F', 1, 3000.00),
    (6, 'Product A', 1, 1000.00);

    INSERT INTO payments (
    customer_id,
    invoice_id,
    payment_title,
    payment_amount,
    payment_date,
    bank_account_number
    )
    VALUES 
    (1, 1, 'Payment for INV-0001', 1000.00, '2024-01-12', '123456789'),
    (2, 2, 'Payment for INV-0002', 3000.00, '2024-01-17', '987654321'),
    (3, 3, 'Payment for INV-0003', 2000.00, '2024-01-22', '111222333'),
    (4, 4, 'Payment for INV-0004', 4200.00, '2024-01-27', '222333444'),
    (5, 5, 'Payment for INV-0005', 3000.00, '2024-09-01', '333444555');

    INSERT INTO overpayments (customer_id, overpayment_amount, overpayment_date)
    VALUES 
    (3, 1000.00, '2024-01-17'),
    (4, 200.00, '2024-01-27');
    ";

    $pdo->exec($sql);
    echo "Tables created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close db connection
$pdo = null;
?>
