<?php

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getResidents()
    {
        return $this->conn->query("
            SELECT *

            FROM users

            WHERE role='user'
        ");
    }

    public function getUser($id)
    {
        return $this->conn->query("
            SELECT *

            FROM users

            WHERE id='$id'
        ");
    }

    public function updateProfile(
        $id,
        $fullname,
        $email
    )
    {
        return $this->conn->query("
            UPDATE users

            SET

            fullname='$fullname',

            email='$email'

            WHERE id='$id'
        ");
    }

    public function deleteResident($id)
    {
        return $this->conn->query("
            DELETE FROM users

            WHERE id='$id'
        ");
    }

    public function uploadStudentID(
        $id,
        $filename
    )
    {
        return $this->conn->query("
            UPDATE users

            SET student_id='$filename'

            WHERE id='$id'
        ");
    }
}

?>