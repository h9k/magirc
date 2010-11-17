<?php

/* yadl_spaceid - Skip Stamping */

// This script returns a JSON dataset of up to 1396 records in 14 columns
// "extid","name","date","price","number","address","company","desc","age","title","phone","email","zip","country"

#header('Content-type: application/json');

// Define defaults
$results = -1; // default get all
$startIndex = 0; // default start at 0
$sort = null; // default don't sort
$dir = 'asc'; // default sort dir is asc
$sort_dir = SORT_ASC;

// How many records to get?
if(@strlen($_GET['results']) > 0) {
    $results = $_GET['results'];
}

// Start at which record?
if(@strlen($_GET['startIndex']) > 0) {
    $startIndex = $_GET['startIndex'];
}

// Sorted?
if(@strlen($_GET['sort']) > 0) {
    $sort = $_GET['sort'];
}

// Sort dir?
if((@strlen($_GET['dir']) > 0) && ($_GET['dir'] == 'desc')) {
    $dir = 'desc';
    $sort_dir = SORT_DESC;
}
else {
    $dir = 'asc';
    $sort_dir = SORT_ASC;
}

// Return the data ($data should be defined before including this file)
returnData($data, $results, $startIndex, $sort, $dir, $sort_dir);

function returnData($allRecords, $results, $startIndex, $sort, $dir, $sort_dir) {
    // Need to sort records
    if(!is_null($sort)) {

        // Obtain a list of columns
        foreach ($allRecords as $key => $row) {
            $sortByCol[$key] = $row[$sort];
        }

        // Valid sort value
        if(@count($sortByCol) > 0) {
            // Sort the original data
            // Add $allRecords as the last parameter, to sort by the common key
            array_multisort($sortByCol, $sort_dir, $allRecords);
        }
    }

    // Invalid start value
    if(is_null($startIndex) || !is_numeric($startIndex) || ($startIndex < 0)) {
        // Default is zero
        $startIndex = 0;
    }
    // Valid start value
    else {
        // Convert to number
        $startIndex += 0;
    }

    // Invalid results value
    if(is_null($results) || !is_numeric($results) ||
            ($results < 1) || ($results >= count($allRecords))) {
        // Default is all
        $results = count($allRecords);
    }
    // Valid results value
    else {
        // Convert to number
        $results += 0;
    }

    // Iterate through records and return from start index
    $data = array();
    $lastIndex = $startIndex+$results;
    if($lastIndex > count($allRecords)) {
        $lastIndex = count($allRecords);
    }
    for($i=$startIndex; $i<($lastIndex); $i++) {
        $data[] = $allRecords[$i];
    }

    // Create return value
    $returnValue = array(
        #'iTotalDisplayRecords'=>count($data),
        #'iTotalRecords'=>count($allRecords),
        'aaData'=>$data
    );

    // JSONify
    //print json_encode($returnValue);

    // Use Services_JSON
    require_once('lib/json/JSON.php');
    $json = new Services_JSON();
    echo ($json->encode($returnValue)); // Instead of json_encode
}

?>
