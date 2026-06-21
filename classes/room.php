<?php

class Room
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addRoom(
        $room,
        $capacity
    )
    {
        return $this->conn->query("
            INSERT INTO rooms

            (
                room_number,
                capacity,
                availability
            )

            VALUES

            (
                '$room',
                '$capacity',
                'Available'
            )
        ");
    }

    public function getRooms()
    {
        return $this->conn->query("
            SELECT *

            FROM rooms
        ");
    }

    public function editRoom(
        $id,
        $room,
        $capacity
    )
    {
        return $this->conn->query("
            UPDATE rooms

            SET

            room_number='$room',

            capacity='$capacity'

            WHERE room_id='$id'
        ");
    }

    public function deleteRoom($id)
    {
        return $this->conn->query("
            DELETE FROM rooms

            WHERE room_id='$id'
        ");
    }

    public function getVacantRooms()
    {
        return $this->conn->query("
            SELECT *

            FROM rooms

            WHERE availability='Available'
        ");
    }

    public function assignRoom(
        $user,
        $room
    )
    {
        return $this->conn->query("
            UPDATE users

            SET assigned_room='$room'

            WHERE id='$user'
        ");
    }
}

?>