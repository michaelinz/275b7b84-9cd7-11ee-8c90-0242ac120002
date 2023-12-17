<?php

namespace App\Utils;

class Utils
{

    public static function parseTime($dateString)
    {
        // Create a DateTime object from the string
        $date = \DateTime::createFromFormat('d/m/Y H:i:s', $dateString);

        // Check if the date was created successfully
        if ($date) {
            // Format the date
            $formattedDate = $date->format('jS F Y h:i A');
            return $formattedDate;
        } else {
            return null;
        }

    }

    public static function praseDate($dateString)
    {
        // Create a DateTime object from the string
        $date = \DateTime::createFromFormat('d/m/Y H:i:s', $dateString);

        // Check if the date was created successfully
        if ($date) {
            // Format the date
            $formattedDate = $date->format('jS F Y');
            return $formattedDate;
        } else {
            return null;
        }

    }
}
