<?php
if (!($csv = file('tdata'))) {
    die('CSV read failed');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>NCC - TT</title>
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
            <h1>Tally Tool - View Demo</h1>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Store Number</th>
                        <th>Campus</th>
                        <th>Term</th>
                        <th>Division</th>
                        <th>Department</th>
                        <th>Course Number</th>
                        <th>Section</th>
                        <th>Section Note</th>
                        <th>Adoption Status</th>
                        <th>Choice Minimum</th>
                        <th>Class Usage Indicator</th>
                        <th>Item Type Indicator</th>
                        <th>ISBN</th>
                        <th>UPC</th>
                        <th>Author / Manufacturer</th>
                        <th>Title / Supply Description</th>
                        <th>Edition</th>
                        <th>Copyright</th>
                        <th>Publisher / Vendor</th>
                        <th>eBook Type</th>
                        <th>eBook Format</th>
                        <th>Rental Flag</th>
                        <th>Item ID</th>
                        <th>Parent Item ID</th>
                        <th>New Retail Price</th>
                        <th>Used Retail Price</th>
                        <th>New Rental Fee</th>
                        <th>Used Rental Fee</th>
                        <th>Course Reference Number</th>
                        <th>Instructor Last Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($csv as $row): ?>
                        <tr>
                            <td>
                                <?= implode('</td><td>', array_pad(str_getcsv($row, "\t"), 30, '')) ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </body>
</html>