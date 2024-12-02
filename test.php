<?php

ini_set('display_errors', 1);
// $newarr = str_getcsv($row, "\t");
// $snippet = 
// if (str_getcsv($row, "\t"))
if (!($csv = file('tdata'))) {
    die('CSV read failed');
}
//var_dump($csv);

$currated = [];
if(isset($_POST["search"])) {
    foreach ($csv as $full) {
        $holdarr = str_getcsv($full, "\t");
        $row = [];
        if (in_array(strtoupper($_POST["search"]), $holdarr)) {
            $code = $holdarr[4] . $holdarr[5] . " - " . $holdarr[6];
            $row[] = $code;
            $row[] = $holdarr[15];
            $row[] = $holdarr[24];
            $row[] = $holdarr[25];
            $row[] = $holdarr[26];
            $row[] = $holdarr[27];
        }
    if(!empty($row)) {
        //var_dump($row);
        $currated[] = $row;
        }
    }
}
//var_dump($currated);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>NCC Bookstore</title>
        <link href="css/common.css" rel="stylesheet">
        <script src="js/table.js" defer></script>
    </head>
    <body>
        <div id="content" class="m-2">
            <h1>NCC Bookstore</h1>
            <div id="uinput">
                <form action="http://localhost:8080/bookstore/test.php" method="POST">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search">
                    <button type="submit">Submit</button>
                </form>
            </div>
            <table id="table" class="table">
                <thead>
                    <tr>
                        <th>Course - Section</th>
                        <th>Title / Supply Description</th>
                        <th>New Retail Price</th>
                        <th>Used Retail Price</th>
                        <th>New Rental Fee</th>
                        <th>Used Rental Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($currated as $row): ?>
                            <tr>
                                <td>
                                    <?= 
                                        implode('</td><td>', array_pad($row, 9, '')) 
                                    ?>
                                </td>
                            </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </body>
</html>