<?php
declare(strict_types=1);
require_once 'inc/Material.php';
require_once 'inc/Section.php';
// Read in tdata, check for a successful read.
if (!($csv = file('LATEST.txt'))) {
    die('CSV read failed');
}
$currated = [];
$vertArr = [];

$sectCode = "";
$sections = [];
$previousCourse = "";
$previousDesignator = "";
array_shift($csv);
array_pop($csv);
foreach ($csv as $full) {
    $holdArr = str_getcsv($full, "\t");
    
    $term = trim($holdArr[2]);
    $courseCode = trim($holdArr[4].$holdArr[5]);
    $sectionDesignator = trim($holdArr[6]);
    
    // $row = [];
    // Term $holdArr[2];
    // $row[] = $courseCode;
    // $row[] = $sectionDesignator;
    // Section Note $holdArr[7];
    // Title $holdArr[15];
    // New Retail floatval($holdArr[24]);
    // Used Retail floatval($holdArr[25]);
    // New Rent floatval($holdArr[26]);
    // Used Rent = floatval($holdArr[27]);
    
    if ($previousDesignator.$previousCourse != $sectionDesignator.$courseCode) {
        // echo "there".PHP_EOL;
        // foreach ($vertArr as $v) {
        if (isset($sections[$term . $courseCode . $sectionDesignator])) {
            $section = $sections[$term . $courseCode . $sectionDesignator];
        } else {
            $section = new Section($term, $courseCode, $sectionDesignator);
            $sections[$term . $courseCode . $sectionDesignator] = $section;
        }
        $priceData = [
            floatval($holdArr[24]),
            floatval($holdArr[25]),
            floatval($holdArr[26]),
            floatval($holdArr[27])];

        $section->addMaterial($holdArr[18], $holdArr[15], $holdArr[14], $priceData);
        // }
        
        
        
        $vertArr = [];
        
        $vertArr[] = [
            $holdArr[2], // Term [0]
            $courseCode, // Course Code [1]
            $sectionDesignator, // Section Designator [2]
            $holdArr[7], // Section Note [3]
            $holdArr[14], // Author [4]
            $holdArr[15], // Title [5]
            $holdArr[18], // Publisher [6]
            floatval($holdArr[24]), // New Retail [7]
            floatval($holdArr[25]), // Used Retail [8]
            floatval($holdArr[26]), // New Rental [9]
            floatval($holdArr[27]), // Used Rental [10]
            0.0, // Min Price Tally [11]
            0.0 // Max Price Tally [12]
            ];
        $previousDesignator = $sectionDesignator;
    }
    else {
        $vertArr[] = [
            $holdArr[2], // Term [0]
            $courseCode, // Course Code [1]
            $sectionDesignator, // Section Designator [2]
            $holdArr[7], // Section Note [3]
            $holdArr[14], // Author [4]
            $holdArr[15], // Title [5]
            $holdArr[18], // Publisher [6]
            floatval($holdArr[24]), // New Retail [7]
            floatval($holdArr[25]), // Used Retail [8]
            floatval($holdArr[26]), // New Rental [9]
            floatval($holdArr[27]), // Used Rental [10]
            0.0, // Min Price Tally [11]
            0.0 // Max Price Tally [12]
            ];
        
        // Maybe build up the tally here?
    }
    
}

foreach ($sections as $sect) {
    if (!empty($sect)) {
        $currated[] = $sect;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>NCC Bookstore</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/fc-4.3.0/fh-3.4.0/kt-2.11.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/fc-4.3.0/fh-3.4.0/kt-2.11.0/sc-2.3.0/sl-1.7.0/datatables.min.js"></script>
        <link href="css/common.css" rel="stylesheet">
        <script src="js/table.js" defer></script>
    </head>
    <body>
        <div id="content" class="m-2">
            <h1>NCC Bookstore</h1>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Term</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Lowest Cost of Materials</th>
                        <th>Highest Cost of Materials</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currated as $row): ?>
                        <tr>
                            <td>
                                <?=
                                   $row->term;
                                ?>
                            </td>
                            <td>
                                <?=
                                   $row->courseCode;
                                ?>
                            </td>
                            <td>
                                <?=
                                   $row->section;
                                ?>
                            </td>
                            <td>
                                <?=
                                   "$".number_format($row->minTotal(),2);
                                ?>
                            </td>
                            <td>
                                <?=
                                   "$".number_format($row->maxTotal(),2);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </body>
</html>