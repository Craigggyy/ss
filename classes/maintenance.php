<?php

class Maintenance
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function submitRequest(
        $user,
        $description,
        $image
    )
    {
        return $this->conn->query("
            INSERT INTO maintenance_requests

            (
                user_id,
                description,
                image,
                status
            )

            VALUES

            (
                '$user',
                '$description',
                '$image',
                'Pending'
            )
        ");
    }

    public function getRequests()
    {
        return $this->conn->query("
            SELECT *

            FROM maintenance_requests
        ");
    }

    public function updateStatus(
        $id,
        $status
    )
    {
        return $this->conn->query("
            UPDATE maintenance_requests

            SET status='$status'

            WHERE request_id='$id'
        ");
    }

    public function deleteRequest($id)
    {
        return $this->conn->query("
            DELETE FROM maintenance_requests

            WHERE request_id='$id'
        ");
    }
}

?>