<?php
/** ensure this file is being included by a parent file */
//defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

/*
define( '_VALID_DIR_', 1 );
require_once "../../config.php";
require_once "../../db_config.php";
require_once "../../common_config.php";

echo "<script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.core.js\" ></script>
                             <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.context.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.annotate.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.tooltips.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.zoom.js\" ></script>


    <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.pie.js\" ></script>
    <!--[if IE 8]><script src=\"".$GLOBALS['wb']."include/rgraph/excanvas/excanvas.compressed.js\"></script><![endif]-->
    ";


$graph = new donut;
$graph->SetName("pie2");
$graph->SetTitle("Total Tasks Per User");
$graph->SetSQL("SELECT count(*) as total, um.full_name as legend
                FROM project_tasks pt, project_resource_tasks rt, project_resources pr, core_user_master um
                WHERE pt.project_id = 1
                AND pt.task_id = rt.task_id
                AND rt.resource_id = pr.resource_id
                AND pr.resource_user_id = um.user_id
                GROUP BY um.full_name");
echo $graph->Draw();

echo "<canvas id='pie2' width='450' height='350'>[No canvas support]</canvas>";
*/
class donut {
    public function __construct() {
        $this->colors            = array("red","green","pink","yellow","grey","cyan","blue","brown","orange");
        $this->chart             = "";
        $this->total_records     = 0;
        $this->name              = "donut1";
        $this->title             = "Unknown";
        $this->result_vals       = array();
        $this->result_vals_a     = "";
        $this->result_legend     = array();
        $this->result_perc       = array();
        $this->result_legend_a   = "";
        $this->result_gradient_a = "";
        $this->gradient          = "";
        $this->gradient_var      = "";
        $this->show_tooltips     = false;
        $this->key               = "";
    }
    public function SetName($name) {
        $this->name = $name;
    }
    public function SetTitle($title) {
        $this->title = $title;
    }
    public function SetSQL($sql) {
        $db = $GLOBALS['db'];
        $result = $db->Query($sql);
        $this->total_records = $db->NumRows($result);
        $total = 0;
        while ($row = $db->FetchArray($result)) {
            $this->result_vals[] = $row['total'];
            $this->result_vals_a .= $row['total'].",";
            $this->result_legend[] = $row['legend'];

            $this->result_legend_a .= "'".$row['legend']."',";
            $total += $row['total'];
        }
        for ($i=0;$i<$this->total_records;$i++) {
          $this->result_perc[] = round((($this->result_vals[$i] / $total)*100),1);
        }
        //echo $this->result_vals_a;
        $this->result_vals_a   = substr($this->result_vals_a,0,-1);
        $this->result_legend_a = substr($this->result_legend_a,0,-1);
        // GENERATE THE KEY
        $this->GenKey();
    }
    private function GradientFunction() {
        $this->chart .= "<script>
                        window.onload = function ()
                        {
                ";
        $this->chart .= "function getGradient(obj, color)
            {
                var gradient = obj.context.createRadialGradient(obj.canvas.width / 2, obj.canvas.height / 2, 0, obj.canvas.width / 2, obj.canvas.height / 2, 200);
                gradient.addColorStop(0, 'black');
                gradient.addColorStop(0.5, color);
                gradient.addColorStop(1, 'black');

                return RGraph.isIE8() ? color : gradient;
            }
        ";
    }
    private function InitialiseGraph() {
        $this->chart .= "var ".$this->name." = new RGraph.Pie('".$this->name."', [".$this->result_vals_a."]);\n";
        //$this->chart .= $this->gradient;
    }
    private function CombineOptions() {
        //$this->chart .= $this->name.".Set('chart.variant', 'donut');\n";
        $this->chart .= $this->name.".Set('chart.labels', [".$this->result_legend_a."]);\n";
        $this->chart .= $this->name.".Set('chart.title', \"".$this->title."\");\n";
        $this->chart .= $this->name.".Set('chart.gutter', 55);\n";
        //$this->chart .= $this->name.".Set('chart.strokestyle', 'rgba(0,0,0,0)');\n";
        //$this->chart .= $this->name.".Set('chart.colors', [".$this->result_gradient_a."]);\n";
        if ($this->show_tooltips) {
            $this->chart .= $this->name.".Set('chart.tooltips', [".$this->result_vals_a."]);\n";
        }
        $this->chart .= $this->name.".Set('chart.highlight.style', '3d');\n"; // Defaults to 3d anyway; can be 2d or 3d
        $this->chart .= "if (!RGraph.isIE8()) {
                            ".$this->name.".Set('chart.zoom.hdir', 'center');
                            ".$this->name.".Set('chart.zoom.vdir', 'up');
                            ".$this->name.".Set('chart.labels.sticks', true);
                            ".$this->name.".Set('chart.labels.sticks.color', '#aaa');
                            ".$this->name.".Set('chart.contextmenu', [['Zoom in', RGraph.Zoom]]);
                        }
                        \n";
        $this->chart .= $this->name.".Set('chart.key', ".$this->key.");\n";
        $this->chart .= $this->name.".Set('chart.linewidth', 5);\n";
        $this->chart .= $this->name.".Set('chart.labels.sticks', true);\n";
        $this->chart .= $this->name.".Set('chart.strokestyle', 'white');\n";
        $this->chart .= $this->name.".Draw();\n";
    }
    private function CombineGradients() {
        $this->chart .= $this->result_gradient_a;
    }
    private function GenerateColors() {
        $count = 0;
        for ($i=0;$i<$this->total_records;$i++) {
            // get a color and start over if required
            if (ISSET($this->colors[$count])) {
                $color = $this->colors[$count];
                $count++;
            }
            else {
                $color = $this->colors[0];
                $count = 0;
            }
            // add to the chart string
            $this->gradient_var .= "var gradient$count = getGradient(".$this->name.", '$color');\n";
            $this->result_gradient_a .= "gradient$count,";
        }
        $this->result_gradient_a = substr($this->result_gradient_a,0,-1);
    }
    private function CombineColors() {
        $this->chart .= $this->gradient_var;
    }
    private function GenKey() {
      $this->key .= "[";
      for ($i=0;$i<$this->total_records;$i++) {
        $this->key .= "'".$this->result_legend[$i]." (".$this->result_perc[$i]."%)',";
        //$this->result_vals[$i] = $row['total'];
        //$this->result_vals_a .= $row['total'].",";
        //$this->result_legend[]
      }
      $this->key = substr($this->key,0,-1);
      $this->key .= "]";

    }
    public function Draw() {
        $this->GradientFunction();
        $this->InitialiseGraph();
        $this->CombineGradients();
        $this->GenerateColors();
        $this->CombineColors();
        $this->CombineOptions();
        $this->chart .= "}\n</script>\n";
        return $this->chart;
    }
}
?>
