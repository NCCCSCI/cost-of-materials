<?php

declare(strict_types=1);
require_once 'inc/Material.php';
require_once 'inc/Section.php';
// Read in tdata, check for a successful read.
if (!($csv = file('tdata'))) {
    die('CSV read failed');
}

/*Prepare the currated array:
 * To present only the data for comparing
 * cost of materials, we need to currate
 * the full table into a desired output.
 * $currated will be used instead of $csv
 * in formatting the table in html. */
$currated = [];
$vertArr = [];
$sectCode = "";
$sections = [];
foreach ($csv as $full) {
    /* $holdarr is a temporary container
     * for one line of data. */
    $holdArr = str_getcsv($full, "\t");
    /* $row is holding the snippets of
     * data we want out of this line
     * of $csv ($holdarr). */
    $row = [];
    $row[] = $holdArr[2];
    $courseCode = trim($holdArr[4].$holdArr[5]);
    $sectionDesignator = trim($holdArr[6]);
    $row[] = $courseCode;
    $row[] = $sectionDesignator;
    $row[] = $holdArr[7];
    $row[] = $holdArr[15];
    $row[] = floatval($holdArr[24]);
    $row[] = floatval($holdArr[25]);
    $row[] = floatval($holdArr[26]);
    $row[] = floatval($holdArr[27]);
    if(empty($sectCode)) {
        $sectCode = $courseCode;
        // var_dump($sectCode.$courseCode);
    }
    if ($sectCode == $courseCode && !empty($sectCode)) {
        // echo "here".PHP_EOL;
        $vertArr[] = [$holdArr[4].$holdArr[5], $holdArr[6], $holdArr[14], $holdArr[15], $holdArr[18], [$holdArr[24], $holdArr[25], $holdArr[26], $holdArr[27]]];
    }
    else {
        // echo "there".PHP_EOL;
        foreach ($vertArr as $v) {
            if (isset($sections[$v[0] . $v[1]])) {
                $section = $sections[$v[0] . $v[1]];
            } else {
                $section = new Section($v[0], $v[1]);
                $sections[$v[0] . $v[1]] = $section;
            }
            // var_dump($section);
            $priceData = [];
            $priceData[] = floatval($v[4]);
            
            $section->addMaterial($v[4], $v[3], $v[2], $priceData);
        }
        //$row[] = $section->minTotal();
        //$row[] = $section->maxTotal();
        // $row[] = $materials->culprit;
        // var_dump($vertArr[0][4]);
        $vertArr = [];
        $sectCode = $courseCode;
        $vertArr[] = [$holdArr[4].$holdArr[5], $holdArr[6], $holdArr[14], $holdArr[15], $holdArr[18], [$holdArr[24], $holdArr[25], $holdArr[26], $holdArr[27]]];
    }
    
    
    
    /* To determine the lowest and highest prices, 
     * we grab from holdarr data from index
     * 24, 25, 26 and 27.
     * The four available prices for materials.
     */

    /* If $row is NOT empty from this line of $csv
     * shunt the array $row into the array $currated,
     * making a 2D array.
     * By making it a 2D array of data, each row is
     * automatically sectioned, falling neatly
     * into the table. */
    if (!empty($row)) {
        $currated[] = $row;
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
                        <th>Section Note</th>
                        <th>Title / Supply Description</th>
                        <th>New Retail Price</th>
                        <th>Used Retail Price</th>
                        <th>New Rental Fee</th>
                        <th>Used Rental Fee</th>
                        <th>Lowest Cost of Materials</th>
                        <th>Highest Cost of Materials</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currated as $row): ?>
                        <tr>
                            <td>
                                <?=
                                    // array_pad can help with empty data.
                                    implode('</td><td>', array_pad($row, 11, ''))
                                ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </body>
</html>