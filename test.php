<?php


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
foreach ($csv as $full) {
    /* $holdarr is a temporary container
     * for one line of data. */
    $holdarr = str_getcsv($full, "\t");
    /* $row is holding the snippets of
     * data we want out of this line
     * of $csv ($holdarr). */
    $row = [];
    $row[] = $holdarr[2];
    $code = $holdarr[4] . $holdarr[5] . " - " . $holdarr[6];
    $row[] = $code;
    $row[] = $holdarr[7];
    $row[] = $holdarr[15];
    $row[] = floatval($holdarr[24]);
    $row[] = floatval($holdarr[25]);
    $row[] = floatval($holdarr[26]);
    $row[] = floatval($holdarr[27]);

    /* If $row is NOT empty from this line of $csv
     * shunt the array $row into the array $currated,
     * making a 2D array.
     * By making it a 2D array of data, each row is
     * automatically sectioned, falling neatly
     * into the table. */
    if (!empty($row)) {
        //var_dump($row);
        $currated[] = $row;
    }
}

//var_dump($currated);
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
                        <th>Course - Section</th>
                        <th>Section Note</th>
                        <th>Title / Supply Description</th>
                        <th>New Retail Price</th>
                        <th>Used Retail Price</th>
                        <th>New Rental Fee</th>
                        <th>Used Rental Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currated as $row): ?>
                        <tr>
                            <td>
                                <?=
                                    // array_pad can help with empty data.
                                    implode('</td><td>', array_pad($row, 8, ''))
                                ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </body>
</html>