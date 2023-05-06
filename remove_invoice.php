<?php
require "functions.php";

$invoice_item_id = 1;

try {
    $dbcon = createDbConnection();
    $sql = "DELETE FROM invoice_items WHERE invoiceID = $invoice_item_id";
    $dbcon->exec($sql);
} catch (PDOException $pdoex) {
    returnError($pdoex);
}