<?php

class Billing
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addBill(
        $user,
        $month,
        $amount
    )
    {
        return $this->conn->query("
            INSERT INTO billing

            (
                user_id,
                month,
                amount,
                status
            )

            VALUES

            (
                '$user',
                '$month',
                '$amount',
                'Unpaid'
            )
        ");
    }

    public function getBills($user)
    {
        return $this->conn->query("
            SELECT *

            FROM billing

            WHERE user_id='$user'
        ");
    }

    public function editBill(
        $id,
        $month,
        $amount
    )
    {
        return $this->conn->query("
            UPDATE billing

            SET

            month='$month',

            amount='$amount'

            WHERE billing_id='$id'
        ");
    }

    public function deleteBill($id)
    {
        return $this->conn->query("
            DELETE FROM billing

            WHERE billing_id='$id'
        ");
    }
}

?>