<?php
require_once("session.php");
var_dump($_SESSION);
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="styles/kendo.common.min.css" />
    <link rel="stylesheet" href="styles/kendo.black.min.css" />

    <script src="js/jquery.min.js"></script>
    <script src="js/kendo.ui.core.min.js"></script>
</head>
<body>

        <div id="main-div">
			<div id="menu">
				<ul>
					<li>Project Select</li>
					<li>Options</li>
					<li>Register</li>
					<li>Log In</li>
				</ul>
			</div>
            <div id="vertical">
                <div id="top-pane">
                    <table id="projectInfo">
					  <tr>
						<th>Name:</th>
						<th>Status:</th>		
						<th>Date:</th>
					  </tr>
					  <tr>
						<td>LIMS-test</td>
						<td>Completed</td>		
						<td>2015-01-18</td>
					  </tr>
					  <tr>
						<th>Wild Type:</th>
						<th>No of Mutations:</th>		
						<th>Interface Interval:</th>
					  </tr>
					  <tr>
						<td>A-37-K</td>
						<td>27</td>		
						<td>152.0</td>
					  </tr>
					</table>
                </div>
                <div id="bottom-pane">
					<div id="horizontal">
                        <div id="left-pane">
								<iframe id="jsmol-left" name="jsmol-left" frameborder=0 src="js/jsmol-14.2.12_2015.01.22/MYTEST.html"></iframe> 
                        </div>
                        <div id="right-pane">
							<ul id="mutationList">
								<li id="1">Structure1</li>
								<li id="2">Structure2</li>
								<li>A-37-A
									<div>Information about A-37-A Mutation...</div>
								</li>
								<li>A-38-K</li>
								<li>A-38-L
									<div>Information about A-38-L Mutation...</div>
								</li>
								<li>A-39
									<ul>
										<li>-> A
											<div>Info</div>
										</li>
										<li>-> D
											<div>Info</div>
										</li>
										<li>-> L
											<div>Info</div>
										</li>
									</ul>
								</li>
								<li>A-39-L</li>
								<li>A-39-K</li>
								<li>A-39-M</li>
								<li>A-39-N</li>
								<li>A-39-A</li>
								<li>A-39-C</li>
								<li>A-39-B</li>
								<li>A-39-F</li>
								<li>A-39-G</li>
								<li>A-39-H</li>
								<li>A-39-I</li>
								<li>A-39-J</li>
								<li>A-39-O</li>
								<li>A-39-P</li>
								<li>A-39-Q</li>
								<li>A-39-R</li>
								<li>A-39-W</li>
								<li>A-39-S</li>
								<li>A-39-T</li>
								<li>A-39-Y</li>
								<li>A-39-U</li>
								<li>A-39-V</li>
								<li>A-39-X</li>
							</ul>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {

					function onSelectMenu(e) {
						alert($(e.item).children(".k-link").text());
					}
					
					$("#menu ul").kendoMenu({
						select: onSelectMenu
					});
					
                    $("#vertical").kendoSplitter({
                        orientation: "vertical",
                        panes: [
                            { collapsible: true, resizable: false, size: "120px" },
							{ collapsible: false, resizable: false }
                        ]
                    });

                    $("#horizontal").kendoSplitter({
                        panes: [
                            { collapsible: false, resizable: false, size: "80%" },
                            { collapsible: true, resizable: false, size: "20%" }
                        ]
                    });
					
					function onSelectMutList(e) {
						if ($(e.item).is(".k-state-active")) {
							var that = this;
							window.setTimeout(function(){that.collapse(e.item);}, 1);
						}

						if ($(e.item).index() < 2) {
							document.getElementById("jsmol-left").contentWindow.load($(e.item).find("> .k-link").text());
						}
					}
					
					$("#mutationList").kendoPanelBar({
						expandMode: "single",
						select: onSelectMutList
					});
                });
            </script>

            <style scoped>
				#main-div {
                    height: 790px;
                    width: 1024px;
                    margin: 0 auto;
                }
				
				#menu {
					width: 100%;
				}
				
				#menu ul {
					overflow: hidden;
					margin: 0;
					padding: 0;
				}
				
				#menu ul li {
					list-style: none;
					float: left;
					text-align: center;
					//width: 25%; /* fallback for non-calc() browsers */
					width: calc(100% / 4);
					box-sizing: border-box;
				}
				
                #vertical {
                    height: 100%;
                    margin: 0;
					padding: 0;
                }
				
				#horizontal {
					height: 100%;
					width: 100%;
				}

                #top-pane { 
					background-color: rgba(60, 70, 80, 0.15);
					text-align: center;
				}
				
                #left-pane, #right-pane  { 
					background-color: rgba(60, 70, 80, 0.05);
				}
				
				#jsmol-left {
					height: 99%;
					width: 100%;
					margin: 0;
					padding: 0;
				}
				
				#mutationList {
					width: 99%;
					margin: 0;
					padding: 0;
				}
				
				#projectInfo {
					width: 100%;
					height: 100%;
					border: 1px solid black;
					-moz-box-sizing: border-box;
					-webkit-box-sizing: border-box;
					-ms-box-sizing: border-box;
					box-sizing: border-box;
				}
				
				#projectInfo th, td {
					border: 1px solid black;
				}
            </style>
        </div>


</body>
</html>