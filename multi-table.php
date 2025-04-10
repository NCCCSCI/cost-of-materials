<?php
declare(strict_types=1);
require_once 'inc/Material.php';
require_once 'inc/Section.php';
require_once 'inc/Semester.php';
// Read in tdata, check for a successful read.
if (!($csv = file('LATEST.txt'))) {
    die('CSV read failed');
}
$curated = [];
// $vertArr = [];

$sectCode = "";
$sections = [];
$previousCourse = "";
$previousDesignator = "";
array_shift($csv);
array_pop($csv);
$semesters = [];
//"spring" => new Semester("Spring 2024"),
//"summer" => new Semester("Summer 2024"),
//"fall" => new Semester("Fall 2024")
$i = 0;
foreach ($csv as $full) {
    $holdArr = str_getcsv($full, "\t");

    /*
     * Legend
     * Term = $holdArr[2];a
     * Course Code = $holdArr[4].$holdArr[5];
     * Section = $holdArr[6];
     * 
     */

    $term = trim($holdArr[2]);
    $courseCode = trim($holdArr[4] . $holdArr[5]);
    $sectionDesignator = trim($holdArr[6]);
    $crn = trim($holdArr[28]);
    
    
    
    // reading in detail, debug
//    if ($courseCode == "ACCT202N") {
//        $counter = 0;
//        foreach ($holdArr as $h) {
//            var_dump($h);
//            echo "<br>";
//            echo "field ".strval($counter)."<br>";
//            $counter += 1;
//        }
//    }

    $follett = $holdArr[29];
    $required = $holdArr[10];

    if (isset($sections[$term . $courseCode . $sectionDesignator])) {
        $section = $sections[$term . $courseCode . $sectionDesignator];
    } else {
        $section = new Section($term, $courseCode, $sectionDesignator, $crn, $follett);
        $sections[$term . $courseCode . $sectionDesignator] = $section;
    }
    if ($required == "RQ" || $required == "CH") {
        $priceData = [
            floatval($holdArr[24]),
            floatval($holdArr[25]),
            floatval($holdArr[26]),
            floatval($holdArr[27])];
        $section->addMaterial($holdArr[18], $holdArr[15], $holdArr[14], $priceData);
    } 
//    else {
//        $priceData = [];
//    }

    

    if (isset($semesters[$term])) {
        $semester = $semesters[$term];
    } else {
        $semester = new Semester($term);
        $semesters[$term] = $semester;
    }
    $semester->addSection($section);
}

//foreach($semesters as $k => $v) {
//    foreach($v->sections as $s) {
//        var_dump($s);
//    }
//}

$currentSemester = getCurrentSemester();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Cost of Materials Estimator</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.2/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/fc-5.0.4/fh-4.0.1/datatables.min.css" rel="stylesheet" integrity="sha384-Yqb7s+1ovGygzMrZmyIIjlJpIYbE7A3Cgz1nFlEQHKoLuAXzIEIXRj1mnkQMxfkc" crossorigin="anonymous">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.2/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/fc-5.0.4/fh-4.0.1/datatables.min.js" integrity="sha384-oTXLyFEJWR1YPkdywPXt2tZpQB92Hgueppcpjq5ulX1xstE3Rbe7R7VoIUTWQibU" crossorigin="anonymous"></script>
        <link href="css/common.css" rel="stylesheet">
        <script src="js/multi-table.js" defer></script>
    </head>
    <body>
        <div id="content" class="m-2">
            <h1>CCSNH Cost of Materials Estimator</h1>
            <ul class="nav nav-tabs mb-4" role="tablist">
                <?php foreach ($semesters as $k => $s): ?>
                <li class="nav-item">
                    <a class="nav-link<?= strpos($k,$currentSemester) !== false ? ' active' : '' ?>" data-bs-toggle="tab" href="#<?= str_replace(' ','',$k) ?>">
                        <?= "$k" ?>
                    </a>
                </li>
                <?php endforeach ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($semesters as $k => $s): ?>
                <div class="tab-pane container<?= strpos($k,$currentSemester) !== false ? ' active' : '' ?>" id='<?= str_replace(' ','',$k) ?>'>
                    <table id="table-<?= str_replace(' ','',$k) ?>" class="table table-striped">
                        <thead>
                            <tr>
                                <th>CRN</th>
                                <th>Course</th>
                                <th>Section</th>
                                <th>Lowest Cost of Materials</th>
                                <th>Highest Cost of Materials</th>
                                <th>Follett Access</th>
                                <th>Certainty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($s->sections as $row): ?>
                            <?php if ($row->minTotal() == 0.0): ?>
                            <tr class="bg-success bg-opacity-25">
                            <?php else: ?>
                            <tr>
                            <?php endif; ?>
                                <td>
                                    <?=
                                    $row->crn;
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
                                    "$" . number_format($row->minTotal(), 2);
                                    ?>
                                </td>
                                <td>
                                    <?=
                                    "$" . number_format($row->maxTotal(), 2);
                                    ?>
                                </td>
                                <td>
                                    <?=
                                    $row->follett;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($row->uncertain) {
                                       echo "N"; 
                                    } else {
                                       echo "Y";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach ?>
            </div>
        </div>
    </body>
</html>
