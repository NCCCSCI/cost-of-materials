<?php
declare(strict_types=1);
require_once 'inc/Material.php';
require_once 'inc/Section.php';
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
foreach ($csv as $full) {
    $holdArr = str_getcsv($full, "\t");

    /*
     * Legend
     * Term = $holdArr[2];
     * Course Code = $holdArr[4].$holdArr[5];
     * Section = $holdArr[6];
     * 
     */

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

//        echo "field ( ";
//        var_dump($priceData);
//        echo " )<br>";

    $section->addMaterial($holdArr[18], $holdArr[15], $holdArr[14], $priceData);

    /*
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
     */
    // $previousDesignator = $sectionDesignator;

    /*
      if ($previousDesignator.$previousCourse != $sectionDesignator.$courseCode) {

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

      }
     */
}

foreach ($sections as $sect) {
    if (!empty($sect)) {
        $curated[] = $sect;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Cost of Materials Estimator</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
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
                    <table id="table-0" class="table table-striped">
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
                            <?php foreach ($curated as $row): ?>
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
                    <table id="table-1" class="table table-striped">
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
                            <?php foreach ($curated as $row): ?>
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
                    <table id="table-2" class="table table-striped">
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
                            <?php foreach ($curated as $row): ?>
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
