<?php
declare(strict_types=1);
require_once 'inc/Material.php';
require_once 'inc/Section.php';
require_once 'inc/Semester.php';
// Read in tdata, check for a successful read.
if (!($csv = file('LATEST'))) {
    die('CSV read failed');
}
$currated = [];

$sectCode = "";
$sections = [];
$previousCourse = "";
$previousDesignator = "";
array_shift($csv);
array_pop($csv);
$semesters = [
    new Semester("Spring 2024"),
    new Semester("Summer 2024"),
    new Semester("Fall 2024")
    ];
foreach ($csv as $full) {
    $holdArr = str_getcsv($full, "\t");

    $term = trim($holdArr[2]);
    $courseCode = trim($holdArr[4] . $holdArr[5]);
    $sectionDesignator = trim($holdArr[6]);
    $crn = trim($holdArr[28]);
    if (str_contains($holdArr[7], "free")) {
        $openStax = "Y";
    } else {
        $openStax = "N";
    }
    $follett = $holdArr[29];

    if (isset($sections[$term . $courseCode . $sectionDesignator])) {
        $section = $sections[$term . $courseCode . $sectionDesignator];
    } else {
        $section = new Section($term, $courseCode, $sectionDesignator, $crn, $openStax, $follett);
        $sections[$term . $courseCode . $sectionDesignator] = $section;
    }
    if ($holdArr[10] == "RQ") {
        $priceData = [
            floatval($holdArr[24]),
            floatval($holdArr[25]),
            floatval($holdArr[26]),
            floatval($holdArr[27])];
    } else {
        $priceData = [];
    }

    $section->addMaterial($holdArr[18], $holdArr[15], $holdArr[14], $priceData);

}

foreach ($sections as $sect){
    if($sect->term == "Spring 2024") {
        $semesters[0]->addSection($sect);
    } elseif ($sect->term == "Summer 2024") {
        $semesters[1]->addSection($sect);
    } elseif ($sect->term == "Fall 2024") {
        $semesters[2]->addSection($sect);
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
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" aria-current="page" href="#spring">Spring</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#summer">Summer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#fall">Fall</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane container active" id='spring'>
                    <table id="table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Term</th>
                                <th>Course</th>
                                <th>Section</th>
                                <th>CRN</th>
                                <th>OER</th>
                                <th>Follett Access</th>
                                <th>Lowest Cost of Materials</th>
                                <th>Highest Cost of Materials</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($currated as $row): ?>
                                <?php if ($row->minTotal() == 0.0): ?>
                                    <tr class="bg-success bg-opacity-25">
                                    <?php else: ?>
                                    <tr>
                                    <?php endif; ?>
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
                                        $row->crn;
                                        ?>
                                    </td>
                                    <td>
                                        <?=
                                        $row->openStax;
                                        ?>
                                    </td>
                                    <td>
                                        <?=
                                        $row->follett;
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
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane container" id='summer'>
                    <table id="table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Term</th>
                                <th>Course</th>
                                <th>Section</th>
                                <th>CRN</th>
                                <th>OER</th>
                                <th>Follett Access</th>
                                <th>Lowest Cost of Materials</th>
                                <th>Highest Cost of Materials</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($currated as $row): ?>
                                <?php if ($row->minTotal() == 0.0): ?>
                                    <tr class="bg-success bg-opacity-25">
                                    <?php else: ?>
                                    <tr>
                                    <?php endif; ?>
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
                                        $row->crn;
                                        ?>
                                    </td>
                                    <td>
                                        <?=
                                        $row->openStax;
                                        ?>
                                    </td>
                                    <td>
                                        <?=
                                        $row->follett;
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
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane container" id='fall'>
                    <table id="table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Term</th>
                                <th>Course</th>
                                <th>Section</th>
                                <th>CRN</th>
                                <th>OER</th>
                                <th>Follett Access</th>
                                <th>Lowest Cost of Materials</th>
                                <th>Highest Cost of Materials</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($currated as $row): ?>
                                <?php if ($row->minTotal() == 0.0): ?>
                                    <tr class="bg-success bg-opacity-25">
                                    <?php else: ?>
                                    <tr>
                                    <?php endif; ?>
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
                                        $row->crn;
                                        ?>
                                    </td>
                                    <td>
                                        <?=
                                        $row->openStax;
                                        ?>
                                    </td>
                                    <td>
                                        <?=
                                        $row->follett;
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
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>