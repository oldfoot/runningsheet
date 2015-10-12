<?php
/** ensure this file is being included by a parent file */
//defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

define( '_VALID_DIR_', 1 );
require_once "../config.php";

echo "<script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.core.js\" ></script>
                             <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.context.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.annotate.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.tooltips.js\" ></script>
                            <script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.zoom.js\" ></script>
							<script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.common.resizing.js\" ></script>
							<script src=\"".$GLOBALS['wb']."include/rgraph/libraries/RGraph.line.js\" ></script>
							<!--[if IE 8]><script src=\"".$GLOBALS['wb']."include/rgraph/excanvas/excanvas.compressed.js\"></script><![endif]-->
    ";
$graph = new line;
$graph->SetName("line1");
$graph->SetTitle("Event Tracking");
$graph->SetSQL("CALL sp_rgraph_task_progress(1,1)");
echo $graph->Draw();

echo "<canvas id='line1' height='400'>[No canvas support]</canvas>";

class line {
    public function __construct() {
        $this->colors            = array("red","green","yellow");
        $this->chart             = "";
        $this->total_records     = 0;
        $this->name              = "donut1";
        $this->title             = "Unknown";
        $this->result_line1 = array();
		$this->result_line1 = array();
        $this->result_vals_line1 = "";
		$this->result_vals_line2 = "";
        $this->result_legend     = array();
        $this->result_perc       = array();
        $this->result_legend_a   = "";
        $this->result_gradient_a = "";
        $this->gradient          = "";
        $this->gradient_var      = "";
        $this->show_tooltips     = false;
        $this->key               = "";
		$this->total_line1 = 0;
		$this->total_line2 = 0;
		$this->legend_ingraph = "";
    }
	public function Draw() {
        $this->WindowOnLoad();
        $this->InitialiseGraph();
        $this->ChartOptions();
        
        $this->chart .= "}\n</script>\n";
        return $this->chart;
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
			$line1 = round(($row['line1']/3600),1) + $this->total_line1;
			$this->total_line1 = $line1;
			
			$line2 = round(($row['line2']/3600),1) + $this->total_line2;
			$this->total_line2 = $line2;
			
			$legend = $row['legend'];
			
            $this->result_line1[] = $line1;
			$this->result_line2[] = $line2;
            $this->result_vals_line1 .= $line1.",";
			$this->result_vals_line2 .= $line2.",";
            $this->result_legend[] = $row['legend'];

            $this->result_legend_a .= "'".$row['legend']."',";
			$this->legend_ingraph .= "'".substr($legend,0,10)."',";
            //$total += $row['total'];
        }		
        $this->result_vals_line1   = substr($this->result_vals_line1,0,-1);
		$this->result_vals_line2   = substr($this->result_vals_line2,0,-1);
		$this->legend_ingraph   = substr($this->legend_ingraph,0,-1);        
    }
    private function WindowOnLoad() {
        $this->chart .= "<script>
                        window.onload = function ()
                        {
					";        
    }
    private function InitialiseGraph() {
        $this->chart .= "var ".$this->name." = new RGraph.Line('".$this->name."', [".$this->result_vals_line2."], [".$this->result_vals_line1."]);\n";
        //$this->chart .= $this->gradient;
    }
    private function ChartOptions() {
        $this->chart .= $this->name.".Set('chart.title', \"".$this->title."\");\n";
		$this->chart .= $this->name.".Set('chart.colors', ['red', 'green']);\n";
		$this->chart .= $this->name.".Set('chart.tickmarks', ['circle', 'square']);\n";
		$this->chart .= $this->name.".Set('chart.linewidth', 1);\n";
		$this->chart .= $this->name.".Set('chart.background.barcolor1', 'white');\n";
		$this->chart .= $this->name.".Set('chart.background.barcolor2', 'white');\n";
		$this->chart .= $this->name.".Set('chart.background.grid.autofit', true);\n";
		$this->chart .= $this->name.".Set('chart.filled', true);\n";
		$this->chart .= $this->name.".Set('chart.fillstyle', ['#fcc', '#cfc']);\n";
		
		/*
		$this->chart .= $this->name.".Set('chart..tooltips', ['id:tooltip_china',      'id:tooltip_la',         'id:tooltip_plymouth',
                                         'id:tooltip_meadowhall', 'id:tooltip_sydney',     'id:tooltip_toronto',
                                         'id:tooltip_france',     'id:tooltip_norway',     'id:tooltip_sweden',
                                         'id:tooltip_spain',      'id:tooltip_deli',       'id:tooltip_congo',
                                         'id:tooltip_brazil',     'id:tooltip_california', 'id:tooltip_newyork',
                                         'id:tooltip_paris',      'id:tooltip_uk',         'id:tooltip_antartica',
                                         'id:tooltip_sahara', 'id:tooltip_zagreb']);\n";
		*/
		$this->chart .= "if (!RGraph.isIE8()) {                            
                            ".$this->name.".Set('chart.contextmenu', [['Zoom in', RGraph.Zoom], ['Cancel', function () {}]]);
							".$this->name.".Set('chart.zoom.delay', 10);
							".$this->name.".Set('chart.labels.frames', 25);
							".$this->name.".Set('chart.zoom.vdir', 'center');
                        }
                        \n";
		$this->chart .= $this->name.".Set('chart.text.angle', 45);\n";
		$this->chart .= $this->name.".Set('chart.gutter', 45);\n";
		$this->chart .= $this->name.".Set('chart.units.post', 'hr');\n";
		$this->chart .= $this->name.".Set('chart.labels.ingraph', [".$this->legend_ingraph."]);\n";
		$this->chart .= $this->name.".Set('chart.noaxes', true);\n";
		$this->chart .= $this->name.".Set('chart.background.grid', true);\n";
		$this->chart .= $this->name.".Set('chart.yaxispos', 'right');\n";
		// CALC THE MAX
		$max = $this->total_line1+10;
		if ($this->total_line2 > $this->total_line1) {
			$max = $this->total_line2 +10;
		}
		$this->chart .= $this->name.".Set('chart.ymax', $max);\n";
		$this->chart .= $this->name.".Set('chart.title.xaxis', 'Tasks');\n";
		$this->chart .= $this->name.".Set('chart.title.yaxis', 'Time');\n";
		$this->chart .= $this->name.".Set('chart..title.xaxis.pos', 0.5);\n";
		$this->chart .= $this->name.".Set('chart.title.yaxis.pos', 0.5);\n";		
        $this->chart .= $this->name.".Draw();\n";
    }        
}
?>
