<?php
// includes/booking_rules.php

function hasConflict($conn, $facility_id, $date, $start, $end, $exclude_booking_id = null) {

    $sql = "
        SELECT COUNT(*) AS cnt FROM bookings
        WHERE facility_id = ?
        AND booking_date = ?
        AND status IN ('approved', 'pending')
        AND (? < end_time AND ? > start_time)
    ";

    if ($exclude_booking_id !== null) {
        $sql .= " AND booking_id != ?";
    }

    $stmt = $conn->prepare($sql);

    if ($exclude_booking_id !== null) {
        $stmt->bind_param("isssi", $facility_id, $date, $start, $end, $exclude_booking_id);
    } else {
        $stmt->bind_param("isss", $facility_id, $date, $start, $end);
    }

    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result['cnt'] > 0;
}
