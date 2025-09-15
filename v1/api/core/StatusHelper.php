<?php


class StatusHelper
{
    public static function getStatusMapping()
    {
        return [
            'status_classes' => [
                2  => "status-warning",
                3  => "status-warning",
                9  => "status-success",
                10 => "status-gray",
                11 => "status-info",
                12 => "status-blue",
                13 => "status-green",
                8  => "status-danger",
                14 => "status-pink",
                15 => "status-gold",
                58 => "status-purple",
            ],
            'bg_colors' => [
                2  => "#fff3cd",
                3  => "#fff3cd",
                9  => "#d4edda",
                10 => "#e9ecef",
                11 => "#d1ecf1",
                12 => "#cce5ff",
                13 => "#d4edda",
                8  => "#f8d7da",
                14 => "#ffe6f0",
                15 => "#fff8d1",
                58 => "#ede7f6",
            ],
            'file_status_text' => [
                "2" => "Deposit Completed",
                //"16" => "Deposit Completed - Final Payment by Credit Card",
                "3" => "Paid in Full by Credit Card",
                "9" => "In Progress",
                "10" => "Quotation Sent - Waiting for Response",
                "11" => "To Be Assigned",
                "12" => "To Be Revised",
                "15" => "Confirmed & Waiting for Credit Card",
                "13" => "Confirmed",
                "58" => "All Services Booked",
                "8" => "Abandoned",
                "14" => "Need to Follow Up",
                "17" => "Information Request",
                "51" => "Need to follow up with a call",
                "52" => "Waiting on vendor",
                "53" => "No response from client",
                "54" => "Client canceled trip",
                "55" => "Emailed client for more information",
            ],
            'file_types' => [
                "1" => "FIT",
                "5" => "IVS",
                "6" => "DII",
                "2" => "Group",
                "7" => "Visits Italy",
                "9" => "Wine Tours Italia",
                "8" => "ILT",
                "3" => "Special Group",
                "4" => "Online",
                "10" => "Motivation",
                "11" => "Information Request",
                "12" => "Transfers & Tours",
                "13" => "FAM Trip",
                "14" => "Summit Retreats",
                "15" => "Honeymoon",
                "16" => "Transfers",
                "17" => "Zicasso"
            ]
        ];
    }

    public static function getStatusInfo($status, $fileType = null)
    {
        $mapping = self::getStatusMapping();

        $statusText = $mapping['file_status_text'][$status] ?? '-';
        $statusClass = $mapping['status_classes'][$status] ?? "status-default";
        $bgColor = $mapping['bg_colors'][$status] ?? "#f8f9fa";

        $fileTypeText = $fileType ? ($mapping['file_types'][$fileType] ?? '-') : '-';

        return [
            'text' => $statusText,
            'class' => $statusClass,
            'bg_color' => $bgColor,
            'file_type_text' => $fileTypeText
        ];
    }
}
