<?php
define( '_VALID_DIR_', 1 );

require "../config.php";
require $dr."classes/rgraph_twoline.php";

$userid = $_SESSION['userid'];

$eventid = 0;
if (ISSET($_GET['eventid']) && IS_NUMERIC($_GET['eventid'])) {
	$eventid = $_GET['eventid'];
}

echo "<script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.core.js\" ></script>
                             <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.context.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.annotate.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.tooltips.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.zoom.js\" ></script>
							<script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.resizing.js\" ></script>
							<script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.line.js\" ></script>
							<!--[if IE 8]><script src=\"".$GLOBALS['wb']."include/rgraph/excanvas/excanvas.compressed.js\"></script><![endif]-->
    ";

$graph = new twoline;
$graph->SetName("line4");
$graph->SetTitle("Event Tracking");
$graph->SetSQL("CALL sp_rgraph_task_progress($eventid,".$_SESSION['userid'].")");
echo $graph->Draw();

echo "<canvas id='line4' height='400' width='550'>[No canvas support]</canvas>";
die();

?>
<script>
        window.onload = function () {
var line4 = new RGraph.Line('line4', [0,300,500,600,100,200,400,500,700,800,400,100],
                                      [500,400,500,700,300,300,500,600,700,800,600,300],
                                      [400,200,400,500,300,300,400,500,400,100,400,300]);
            line4.Set('chart.key', ['2008', '2007', '2006']);
            line4.Set('chart.key.background', 'white');
            line4.Set('chart.key.shadow', true);
            line4.Set('chart.key.shadow.offsetx', 0);
            line4.Set('chart.key.shadow.offsety', 0);
            line4.Set('chart.key.shadow.blur', 15);
            line4.Set('chart.key.shadow.color', '#ccc');
            line4.Set('chart.key.rounded', true);
            line4.Set('chart.gutter', 45);

            if (!RGraph.isIE8()) {
                line4.Set('chart.zoom.mode', 'thumbnail');
            } else {
                line4.Set('chart.key.shadow.offsetx', 2);
                line4.Set('chart.key.shadow.offsety', 2);
            }

            line4.Set('chart.filled', true);
            line4.Set('chart.tickmarks', null);
            line4.Set('chart.background.barcolor1', 'white');
            line4.Set('chart.background.barcolor2', 'white');
            line4.Set('chart.background.grid.autofit', true);
            line4.Set('chart.title', 'A line chart (zoom, Y axis on the right)');
            line4.Set('chart.colors', ['rgba(169, 222, 244, 0.7)', 'red', '#ff0']);
            line4.Set('chart.fillstyle', ['#daf1fa', '#faa', '#ffa']);
            line4.Set('chart.labels', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
            line4.Set('chart.yaxispos', 'right');
            line4.Set('chart.linewidth', 5);
            line4.Draw();
}
    </script>
<canvas id='line4' height='400' width='550'>[No canvas support]</canvas>
