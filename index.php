<?php
session_start();

$designName = (isset($_SESSION['designName'])) ? $_SESSION['designName'] : '';
$designId = (isset($_SESSION['designId'])) ? $_SESSION['designId'] : '';
$designType = (isset($_SESSION['rowHeight'])) ? $_SESSION['rowHeight'] : '';
$message = (isset($_SESSION['message'])) ? $_SESSION['message'] : '';
$change = (isset($_SESSION['change'])) ? $_SESSION['change'] : 'false';
if ($message !== '') {
  echo "<script>alert('" . $message . "');</script>";
  unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Blocks</title>
  <link rel="stylesheet" href="assets/plugin/slick/slick.css">
  <link rel="stylesheet" href="assets/plugin/slick/slick-theme.css">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="main.css">
  <script src="assets/plugin/fontawesome/js/all.js"></script>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugin/slick/slick.min.js"></script>
  <script src="main.js"></script>
</head>
<body>
<div>
  <div class="logo-header" style="display: inline">
    <h2 style="margin-left: 60px; margin-top: 30px;float: left">BLOCKS</h2>
    <ul class="list-inline" id="groupController" style="float: right;visibility: hidden;">
      <li class='list-inline-item' style='padding-right:7px; border-right: 1px solid black'><a href="#">Manage Group</a>
      </li>
    </ul>
  </div>
  <div id="toolbox">
    <span class="plus first_icons controller" data-no="1" style="background-image: url('assets/img/3.PNG')"
          ondblclick="addToWK('c1')"></span>
    <span class="plus first_icons controller" data-no="2"
          style="background-image: url('assets/img/ASSETS/ICONAsset181.svg')" ondblclick="addToWK('c2')"></span>
    <span class="plus first_icons controller" data-no="3"
          style="background-image: url('assets/img/ASSETS/ICONAsset179.svg')" ondblclick="addToWK('c3')"></span>
    <span class="plus first_icons controller" data-no="4" id="last_controller"
          style="background-image: url('assets/img/ASSETS/ICONAsset182.svg')" ondblclick="addToWK('c4')"></span>

    <?php
    include("config.php");
    $query_getGroup = "select * from blockgroup order by toolboxOrder ASC";
    $result_getGroup = $conn->query($query_getGroup);
    $i = 4;

    while ($row = mysqli_fetch_assoc($result_getGroup)) {
      $i++;
      $iconPath = $row['iconPath'];
      $groupId = $row['id'];
      echo "<span class='plus first_icons TB' data-id='" . $groupId . "' data-no='" . $i . "' onclick='showGroupController(" . $groupId . ", " . $i . ")' 
      style='background-image: url(" . $iconPath . ")' ondblclick='addToWK(" . $groupId . ")'></span>";
    }
    ?>

  </div>
  <span class="arrowUp" id="arrowUp" onclick="arrowUp()"></span><br>
  <span class="arrowDown" id="arrowDown" onclick="arrowDown()"></span>

  <br>

  <div id="designController" style="display: inline">
    <ul class="nav" style="float: left">
      <?php
      if (isset($_SESSION['designName'])) {
        echo "<li class='nav-item' style='font-weight: bold'><a class='nav-link' href='#'>" . $_SESSION['designName'] . "</a></li>";
      } else {
        echo '<li class="nav-item" onclick="addNewDesign()"><a class="nav-link" href="#">New Design</a></li>';
      }
      ?>
      <li class="nav-item" onclick="openDesign()">
        <a class="nav-link" href="#">Open</a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#" onclick="closeDesign()">Close</a>
      </li>
      <li class="nav-item">
        <?php
        if (isset($_SESSION['designId'])) {
          echo '<a class="nav-link disabled" href="#" onclick="saveDesign(' . $_SESSION["designId"] . ')">Save</a>';
        } else {
          echo '<a class="nav-link disabled" href="#" onclick="saveDesign(0)">Save</a>';
        }
        ?>
      </li>
      <li class="nav-item dropdownList" style="position: relative;display: inline-block;">
        <?php
        if (isset($_SESSION['designId'])) {
          echo '<a class="nav-link disabled" href="#" onclick="editDesign(' . $_SESSION["designId"] . ')">Edit</a>';
        } else {
          echo '<a class="nav-link disabled" href="#" onclick="editDesign(0)">Edit</a>';
        }
        ?>
      </li>
    </ul>

    <ul class="list-inline" id="WKgroupController" style="float: right;visibility: hidden">
      <li class="list-inline-item"><a href="#">Remove</a></li>
    </ul>
  </div>


  <div id="workingBoard" style="display: none">
    <?php
    include('config.php');
    $query_getGroupWK = "select * from temp_wk_groups where designId='" . $designId . "' order by workingBoardOrder ASC";
    $result_getGroupWK = $conn->query($query_getGroupWK);

    $j = 0;

    while ($rowWK = mysqli_fetch_assoc($result_getGroupWK)) {
      $j++;
      $groupIconPath = $rowWK['groupIconPath'];
      $groupIdWK = $rowWK['id'];
      $WKorder = $rowWK['workingBoardOrder'];
      echo "<span class='plus first_icons WK' data-id='" . $groupIdWK . "' data-no='" . $j . "' onclick='showGroupControllerWK(" . $groupIdWK . ", " . $j . ")' 
      style='background-image: url(" . $groupIconPath . ")' ondblclick='removeFromWK(" . $groupIdWK . ", " . $j . ")'></span>";
    }
    ?>
  </div>

  <span class="arrowUp" style="display: none" id="arrowUpWK" onclick="arrowUpWK()"></span><br>
  <span class="arrowDown" style="display: none;" id="arrowDownWK" onclick="arrowDownWK()"></span>

</div>

<div id="div_canvas" style="width: 100%;top: -30px">
  <canvas id="grid">

  </canvas>
  <div id="grid_toolbox" style="display: none">
    <label id="span_zoom"><i class="fa fa-search"></i></label>

    <select name="zoom_level" id="zoom_grid_input" onchange="changeZoomGrid()">
      <?php
      for ($i = 30; $i <= 300; $i += 10) {
        if ($i == 100) {
          echo "<option value='" . $i . "' selected>" . $i . "%</option>";
          continue;
        }
        echo "<option value='" . $i . "'>" . $i . "%</option>";
      }
      ?>
    </select>

    <button id="btn_run"> RUN</button>
    <button id="btn_export"> EXPORT SVG</button>
  </div>
</div>

<div class="modal" id="addGroupModal" role="dialog">

  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="header">
          <h4>ADD NEW BLOCK GROUP</h4>
        </div>
        <div class="container modalRow">
          <form action="addBlockGroup.php" method="post" enctype="multipart/form-data" id="form_addGroup">

            <div class="row">

              <div class="col-md-4">
                <div class="div_dashedLine">

                </div>

                <div class="border_div">
                  <div class="form-group" id="addFormGroup">
                    <label class="form-label"><h5>Block Group Name</h5></label>
                    <input type="text" name="groupName" style="width: 88%"
                           id="groupName">
                    <input type="file" class="form-control inputfile" id="uploadGroupIcon"
                           name="groupIcon"/>
                    <label for="uploadGroupIcon" id="uploadBlockGroupLabel"><span><i
                          class="fa fa-upload"></i></span></label>

                    <input type="hidden" id="num_new" name="num_new">
                    <input type="file" class="inputfile newAddBlock" id="newMultiUpload" name="new[]" multiple>

                    <p id="groupName_valid">Please input a group name.</p>
                    <p id="groupIcon_valid">Please upload the group icon.</p>
                  </div>
                  <div id="preview_addGroupIcon"></div>
                  <div class="form-group">
                    <label><h5>Block Group Type</h5></label><br>
                    <!--                  <input type="hidden" id="blockGroupType">-->
                    <style>
                      .typeChoice {
                        display: block;
                        position: relative;
                        padding-left: 35px;
                        margin-bottom: 12px;
                        cursor: pointer;
                        font-size: 16px;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        user-select: none;
                      }

                      /* Hide the browser's default radio button */
                      .typeChoice input {
                        position: absolute;
                        opacity: 0;
                        cursor: pointer;
                        width: 0px;
                        height: 0px;
                      }

                      /* Create a custom radio button */
                      .checkmark {
                        position: absolute;
                        top: 0;
                        left: 0;
                        height: 25px;
                        width: 25px;
                        background-color: #eee;
                        border-radius: 0%;
                      }

                      /* On mouse-over, add a grey background color */
                      .typeChoice:hover input ~ .checkmark {
                        background-color: #ccc;
                      }

                      /* Create the indicator (the dot/circle - hidden when not checked) */
                      .checkmark:after {
                        content: "";
                        position: absolute;
                        display: none;
                      }

                      /* Show the indicator (dot/circle) when checked */
                      .typeChoice input:checked ~ .checkmark:after {
                        display: block;
                      }

                      /* Style the indicator (dot/circle) */
                      .typeChoice .checkmark:after {
                        top: 8px;
                        left: 8px;
                        width: 10px;
                        height: 10px;
                        border-radius: 0%;
                        background: darkgreen;
                      }
                    </style>
                    <label class="typeChoice">Loose
                      <input type="radio" class="addGroupType" checked="checked" name="groupType" value="loose">
                      <span class="checkmark"></span>
                    </label>
                    <label class="typeChoice">Entrance
                      <input type="radio" class="addGroupType" name="groupType" value="entrance">
                      <span class="checkmark"></span>
                    </label>
                    <label class="typeChoice">Bridge
                      <input type="radio" class="addGroupType" name="groupType" value="bridge">
                      <span class="checkmark"></span>
                    </label>
                    <label class="typeChoice">Exit
                      <input type="radio" class="addGroupType"  name="groupType" value="exit">
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <br>
                  <div id="uploadBlock" class="form-group">
                    <input type="file" class="form-control inputfile" id="addUploadBlocks"
                           name="file_uploadBlock[]" multiple>
                    <label for="addUploadBlocks">&nbsp;&nbsp;&nbsp;<span><i class="fa fa-upload"></i></span>
                      Upload Blocks</label>
                  </div>
                  <p>Upload your Block Set here. Each Block must be in a separate SVG file. You can
                    manage Blocks on the right.
                  </p>

                </div>

                <br><br>
                <div class="row button_group">
                  <div class="col-6">

                  </div>
                  <div class="col-6 save_cancel">
                    <button id="btn_save" type="button" onclick="addGroupSubmit()"> Save</button>
                    <button id="btn_cancel" type="button" data-dismiss="modal"> Cancel</button>
                  </div>
                </div>
          </form>
        </div>

        <div class="col-md-8">
          <div class="form-group">
            <label class="form-label" for="blockName"><h5 style="margin-left: 35px">Block Name</h5></label><br>
            <div class="row col_ManageBlock">
              <div class="col-lg-6 col-md-12">
                <input type="text" name="blockName" id="blockName" style="width: 100%;">
              </div>
              <div class="col-lg-6 col-md-12">

              </div>
            </div>
          </div>
          <div class="container" id="icon_toolbox">

          </div>

          <span class="arrowUp" id="arrowUpAddGroup" onclick="arrowUpAddGroup()"></span><br>
          <span class="arrowDown" id="arrowDownAddGroup" onclick="arrowDownAddGroup()"></span>

        </div>
      </div>
      </form>

    </div>

  </div>
</div>

</div>

</div>

<div class="modal" id="manageGroupModal" role="dialog">

  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="header">
          <h4>MANAGE BLOCK GROUP</h4>
        </div>
        <div class="container modalRow">
          <form action="modifyGroup.php" method="post" enctype="multipart/form-data" id="form_manageGroup">
            <div class="row">
              <div class="col-md-4">
                <div class="div_dashedLine">

                </div>
                <div class="border_div">
                  <div class="form-group">
                    <label class="form-label"><h5>Block Group Name</h5></label>
                    <input type="text" name="groupName" style="width: 88%"
                           id="manageGroupName">

                    <input type="file" class="form-control inputfile" id="manageUploadGroupIcon"
                           name="groupIcon"/>
                    <label for="manageUploadGroupIcon" id="manageUploadGroupLabel"><span><i
                          class="fa fa-upload"></i></span></label>
                    <input type="hidden" id="groupId" name="groupId">
                    <input type="hidden" id="oldGroupName" name="oldGroupName">
                    <input type="hidden" id="hiddenGroupType" name="groupType">
                    <input type="hidden" id="num_manage" name="num_manage">
                    <input type="file" class="inputfile manageAddBlock" id="manageMultiUpload" name="manage[]" multiple>
                  </div>
                  <div id="preview_manageGroupIcon"></div>
                  <br>
                  <div class="form-group">
                    <label><h5>Block Group Type</h5></label><br>
                    <style>
                      .typeChoice {
                        display: block;
                        position: relative;
                        padding-left: 35px;
                        margin-bottom: 12px;
                        cursor: pointer;
                        font-size: 16px;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        user-select: none;
                      }

                      /* Hide the browser's default radio button */
                      .typeChoice input {
                        position: absolute;
                        opacity: 0;
                        cursor: pointer;
                        width: 0px;
                        height: 0px;
                      }

                      /* Create a custom radio button */
                      .checkmark {
                        position: absolute;
                        top: 0;
                        left: 0;
                        height: 25px;
                        width: 25px;
                        background-color: #eee;
                        border-radius: 0%;
                      }

                      /* On mouse-over, add a grey background color */
                      .typeChoice:hover input ~ .checkmark {
                        background-color: #ccc;
                      }

                      /* Create the indicator (the dot/circle - hidden when not checked) */
                      .checkmark:after {
                        content: "";
                        position: absolute;
                        display: none;
                      }

                      /* Show the indicator (dot/circle) when checked */
                      .typeChoice input:checked ~ .checkmark:after {
                        display: block;
                      }

                      /* Style the indicator (dot/circle) */
                      .typeChoice .checkmark:after {
                        top: 8px;
                        left: 8px;
                        width: 10px;
                        height: 10px;
                        border-radius: 0%;
                        background: darkgreen;
                      }
                    </style>
                    <label class="typeChoice">Loose
                      <input type="radio" checked="checked" name="groupType" value="loose" class="manageGroupType">
                      <span class="checkmark"></span>
                    </label>
                    <label class="typeChoice">Entrance
                      <input type="radio" name="groupType" value="entrance" class="manageGroupType">
                      <span class="checkmark"></span>
                    </label>
                    <label class="typeChoice">Bridge
                      <input type="radio" name="groupType" value="bridge" class="manageGroupType">
                      <span class="checkmark"></span>
                    </label>
                    <label class="typeChoice">Exit
                      <input type="radio" name="groupType" value="exit" class="manageGroupType">
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <br>
                  <div id="uploadBlock" class="form-group">
                    <input type="file" class="form-control inputfile" id="manage_upload_block"
                           name="file_uploadBlock[]" multiple>
                    <label for="manage_upload_block">&nbsp;&nbsp;&nbsp;<span><i class="fa fa-upload"></i></span>
                      Upload Blocks</label>
                  </div>
                  <p>Upload your Block Set here. Each Block must be in a separate SVG file. You can
                    manage Blocks on the right.
                  </p>

                </div>

                <br><br>
                <div class="row button_group">
                  <div class="col-6" id="div_delGroup">
                    <button id="btn_manage_del" type="button" style="background-color: transparent;color:red">Delete
                      Group
                    </button>
                  </div>
                  <div class="col-6 save_cancel">
                    <button id="btn_manage_save" type="button" onclick="submitManageForm()"
                            style="background-color: green;margin-left: 15px;"> Save
                    </button>
                    <button id="btn_manage_cancel" type="button" style="background-color: transparent"
                            data-dismiss="modal"> Cancel
                    </button>
                  </div>
                </div>
                <!--            </form>-->
              </div>

              <div class="col-md-8">
                <!--              <form action="manageBlock.php" id="form_manageBlock" method="post" enctype="multipart/form-data">-->
                <div class="form-group">
                  <label class="form-label" for="blockName"><h5 style="margin-left: 35px">Block Name</h5></label><br>
                  <div class="row col_ManageBlock">
                    <input type="hidden" name="hiddenBlockId" id="hiddenBlockId">
                    <input type="hidden" name="hiddenBlockName" id="hiddenBlockName">
                    <input type="file" class="inputfile" id="hiddenReplace" name="replace[]">
                    <div class="col-lg-6 col-md-12" id="div_blockName">
                      <input type="text" name="blockName" id="blockName" style="width: 100%;">
                    </div>
                    <div class="col-lg-6 col-md-12">
                      <ul class="list-inline" id="icon_controllers" style="display:none">
                        <li class="list-inline-item" id="icon_replace">
                          <input type="file" class="form-control inputfile" id="file_replaceBlock"
                                 name="file_replaceBlock"/>
                          <label for="file_replaceBlock" style="font-size: 1em">
                            Replace Block</label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="container" id="manage_icon_toolbox">

                </div>
                <span class="arrowUp" id="arrowUpManageGroup" onclick="arrowUpManageGroup()"></span><br>
                <span class="arrowDown" id="arrowDownManageGroup" onclick="arrowDownManageGroup()"></span>

              </div>
            </div>
          </form>
        </div>

      </div>
    </div>

  </div>

</div>

<div class="modal designModal" id="newDesignModal" role="dialog">
  <div class="modal-dialog">
    <form action="design/addDesign.php" id="form_addDesign" method="post">
      <div class="modal-content">
        <div class="container">
          <div class="modal-header" style="border-bottom: 1px dashed lightgray">
            <h4>NEW DESIGN BOARD</h4>
          </div>
          <br>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label"><h5>New Board Title</h5></label>
              <input type="text" name="newBoardTitle" style="width: 100%"
                     id="newBoardTitle">
              <p id="designName_valid">Please input a new design name.</p>
            </div>
            <br>
            <div class="form-group">
              <label><h5>Block Group Type</h5></label><br>
              <style>
                .typeChoice {
                  display: block;
                  position: relative;
                  padding-left: 35px;
                  margin-bottom: 12px;
                  cursor: pointer;
                  font-size: 16px;
                  -webkit-user-select: none;
                  -moz-user-select: none;
                  -ms-user-select: none;
                  user-select: none;
                }

                /* Hide the browser's default radio button */
                .typeChoice input {
                  position: absolute;
                  opacity: 0;
                  cursor: pointer;
                  width: 0px;
                  height: 0px;
                }

                /* Create a custom radio button */
                .checkmark {
                  position: absolute;
                  top: 0;
                  left: 0;
                  height: 25px;
                  width: 25px;
                  background-color: #eee;
                  border-radius: 0%;
                }

                /* On mouse-over, add a grey background color */
                .typeChoice:hover input ~ .checkmark {
                  background-color: #ccc;
                }

                /* Create the indicator (the dot/circle - hidden when not checked) */
                .checkmark:after {
                  content: "";
                  position: absolute;
                  display: none;
                }

                /* Show the indicator (dot/circle) when checked */
                .typeChoice input:checked ~ .checkmark:after {
                  display: block;
                }

                /* Style the indicator (dot/circle) */
                .typeChoice .checkmark:after {
                  top: 8px;
                  left: 8px;
                  width: 10px;
                  height: 10px;
                  border-radius: 0%;
                  background: darkgreen;
                }
              </style>
              <label class="typeChoice">8 Rows Height
                <input type="radio" checked="checked" name="groupType_design" value="8" class="manageGroupType">
                <span class="checkmark"></span>
              </label>
              <label class="typeChoice">10 Rows Height
                <input type="radio" name="groupType_design" value="10" class="manageGroupType">
                <span class="checkmark"></span>
              </label>
              <label class="typeChoice">12 Rows Height
                <input type="radio" name="groupType_design" value="12" class="manageGroupType">
                <span class="checkmark"></span>
              </label>
              <p id="designType_valid">Please select a design type.</p>
            </div>
          </div>
          <br>
          <p>
            Upload your Block Set here. Each Block must be in a separate SVG file. You can
            manage Blocks on the right.
          </p><br><br><br>
          <div class="save_cancel button_group">
            <button class="btn_save_board" type="button" onclick="newDesignSubmit()">Save</button>
            <button class="btn_cancel_board" type="button" data-dismiss="modal">Cancel</button>
          </div>
          <hr>
          <hr>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal designModal" id="editDesignModal" role="dialog">
  <div class="modal-dialog">
    <form action="design/editDesign.php" id="form_editDesign" method="post">
      <div class="modal-content">
        <div class="container">
          <div class="modal-header" style="border-bottom: 1px dashed lightgray">
            <h4>EDIT DESIGN BOARD</h4>
          </div>
          <br>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label"><h5>Edit Board Title</h5></label>
              <input type="hidden" id="editDesignId" name="designId">
              <input type="hidden" id="oldDesignName" name="oldDesignName">
              <input type="hidden" id="oldDesignType" name="oldDesignType">
              <input type="text" name="editBoardTitle" style="width: 100%"
                     id="editBoardTitle">
            </div>
            <br>
            <div class="form-group">
              <label><h5>Block Group Type</h5></label><br>
              <style>
                .typeChoice {
                  display: block;
                  position: relative;
                  padding-left: 35px;
                  margin-bottom: 12px;
                  cursor: pointer;
                  font-size: 16px;
                  -webkit-user-select: none;
                  -moz-user-select: none;
                  -ms-user-select: none;
                  user-select: none;
                }

                /* Hide the browser's default radio button */
                .typeChoice input {
                  position: absolute;
                  opacity: 0;
                  cursor: pointer;
                  width: 0px;
                  height: 0px;
                }

                /* Create a custom radio button */
                .checkmark {
                  position: absolute;
                  top: 0;
                  left: 0;
                  height: 25px;
                  width: 25px;
                  background-color: #eee;
                  border-radius: 0%;
                }

                /* On mouse-over, add a grey background color */
                .typeChoice:hover input ~ .checkmark {
                  background-color: #ccc;
                }

                /* Create the indicator (the dot/circle - hidden when not checked) */
                .checkmark:after {
                  content: "";
                  position: absolute;
                  display: none;
                }

                /* Show the indicator (dot/circle) when checked */
                .typeChoice input:checked ~ .checkmark:after {
                  display: block;
                }

                /* Style the indicator (dot/circle) */
                .typeChoice .checkmark:after {
                  top: 8px;
                  left: 8px;
                  width: 10px;
                  height: 10px;
                  border-radius: 0%;
                  background: darkgreen;
                }
              </style>
              <label class="typeChoice">8 Rows Height
                <input type="radio" checked="checked" name="groupType_design" value="8" class="manageEditType">
                <span class="checkmark"></span>
              </label>
              <label class="typeChoice">10 Rows Height
                <input type="radio" name="groupType_design" value="10" class="manageEditType">
                <span class="checkmark"></span>
              </label>
              <label class="typeChoice">12 Rows Height
                <input type="radio" name="groupType_design" value="12" class="manageEditType">
                <span class="checkmark"></span>
              </label>
            </div>
          </div>
          <br>
          <br>
          <div class="row button_group">
            <div class="col-5" id="div_del_design">
              <button id="btn_design_del" type="button" style="background-color: transparent;color:red">
                Delete Design
              </button>
            </div>
            <div class="col-7 save_cancel" id="div_save_design">
              <!--              <div class="save_cancel button_group">-->
              <button id="btn_design_save" type="submit">Save</button>
              <button class="btn_cancel_board" type="button" data-dismiss="modal">Cancel</button>
              <!--              </div>-->
            </div>
          </div>

          <hr>
          <hr>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal designModal" id="openDesignModal" role="dialog">
  <div class="modal-dialog">
    <form action="design/openDesign.php" id="form_openDesign" method="post">
      <div class="modal-content">
        <div class="container">
          <div class="modal-header" style="border-bottom: 1px dashed lightgray">
            <h4>OPEN DESIGN BOARD</h4>
          </div>
          <br>
          <div class="modal-body">
            <div class="form-group">
              <label><h5>Select a Design Name</h5></label><br>
              <style>
                .typeChoice {
                  display: block;
                  position: relative;
                  padding-left: 35px;
                  margin-bottom: 12px;
                  cursor: pointer;
                  font-size: 16px;
                  -webkit-user-select: none;
                  -moz-user-select: none;
                  -ms-user-select: none;
                  user-select: none;
                }

                /* Hide the browser's default radio button */
                .typeChoice input {
                  position: absolute;
                  opacity: 0;
                  cursor: pointer;
                  width: 0px;
                  height: 0px;
                }

                /* Create a custom radio button */
                .checkmark {
                  position: absolute;
                  top: 0;
                  left: 0;
                  height: 25px;
                  width: 25px;
                  background-color: #eee;
                  border-radius: 0%;
                }

                /* On mouse-over, add a grey background color */
                .typeChoice:hover input ~ .checkmark {
                  background-color: #ccc;
                }

                /* Create the indicator (the dot/circle - hidden when not checked) */
                .checkmark:after {
                  content: "";
                  position: absolute;
                  display: none;
                }

                /* Show the indicator (dot/circle) when checked */
                .typeChoice input:checked ~ .checkmark:after {
                  display: block;
                }

                /* Style the indicator (dot/circle) */
                .typeChoice .checkmark:after {
                  top: 8px;
                  left: 8px;
                  width: 10px;
                  height: 10px;
                  border-radius: 0%;
                  background: darkgreen;
                }
              </style>

              <?php
              include("config.php");
              $query_getDesign = "select * from design order by id DESC";
              $result_getDesign = $conn->query($query_getDesign);

              while ($row = mysqli_fetch_assoc($result_getDesign)) {
                $openDesignName = $row['name'];
                $openDesignId = $row['id'];

                echo "<label class='typeChoice'>" . $openDesignName . "<input type='radio' name='openDesignId' class='openDesignName' " .
                  "value='" . $openDesignId . "'><span class='checkmark'></span></label>";
              }
              ?>
            </div>
          </div>
          <br><br><br>

          <div class="save_cancel button_group">
            <button class="btn_save_board" type="button" onclick="openDesignSubmit()">Open</button>
            <button class="btn_cancel_board" type="button" data-dismiss="modal">Cancel</button>
          </div>
          <hr>
          <hr>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal" id="deleteGroupModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="container">
        <div class="modal-header">
          <h4 style="color: red">Delete Group Failed</h4>
        </div>
        <div class="modal-body">
          <p>You cannot delete this group because it is used in the following projects</p>
          <ul id="designNamesList">

          </ul>
          <p>You need to delete the associated blocks in each projects to be able to delete this group.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-lg btn-danger" data-dismiss="modal"> OK</button>
        </div>
      </div>
    </div>
  </div>

</div>

</body>
<script>
    var change = '<?= $change ?>';

    //move groups up, down, right, left
    var boxWidth = $("#toolbox").width();

    var num_group_in_row = 0;
    if (boxWidth > 1500) num_group_in_row = 35;
    if (boxWidth == 1438) num_group_in_row = 36;
    if (boxWidth == 1238) num_group_in_row = 31;
    if (boxWidth == 938) num_group_in_row = 23;
    if (boxWidth == 898) num_group_in_row = 22;
    if (boxWidth == 838) num_group_in_row = 21;
    if (boxWidth == 658) num_group_in_row = 16;
    if (boxWidth == 478) num_group_in_row = 11;
    if (boxWidth == 421) num_group_in_row = 10;

    function moveLeft(id, index) {
        var firstIcons = $('.TB');
        var num_firstIcons = firstIcons.length + 4;
        if (index == num_firstIcons) {
            alert("The group can't move to the left");
            showGroupController(id, index);
        } else {
            $('.list_controller').removeAttr('onclick');
            var leftGroup = $(".TB[data-no=" + (index + 1) + "]");
            var leftGroupId = $(leftGroup).attr('data-id');
            // alert(leftGroupId);
            switchGroup(id, index, leftGroupId, index + 1);
        }
    }

    function moveRight(id, index) {
        if (index == 5) {
            alert("The group can't move to the right.");
            showGroupController(id, index);
        } else {
            $('.list_controller').removeAttr('onclick');
            var rightGroup = $(".TB[data-no=" + (index - 1) + "]");
            var rightGroupId = $(rightGroup).attr('data-id');
            switchGroup(id, index, rightGroupId, index - 1);
        }
    }

    function moveUp(id, index) {
        var index2 = index - num_group_in_row
        if (index2 < 5) {
            $('.list_controller').removeAttr('onclick');
            var upGroup = $("[data-no='5']");
            var upGroupId = $(upGroup).attr('data-id');
            $.ajax({
                url: 'group/moveUpGroup.php',
                data: {
                    id1: id,
                    id2: upGroupId,
                    index1: index,
                    index2: 5
                },
                type: 'post',
                success: function (result) {
                    reloadGroups(result, id, 5);
                }
            })
        } else {

            $('.list_controller').removeAttr('onclick');
            var upGroup = $("[data-no=" + index2 + "]");
            var upGroupId = $(upGroup).attr('data-id');
            $.ajax({
                url: 'group/moveUpGroup.php',
                data: {
                    id1: id,
                    id2: upGroupId,
                    index1: index,
                    index2: index2
                },
                type: 'post',
                success: function (result) {
                    reloadGroups(result, id, index2);
                }
            })
        }
    }

    function moveDown(id, index) {
        var firstIcons = $('.TB');
        var num_firstIcons = firstIcons.length + 4;
        if (index + num_group_in_row > num_firstIcons) {
            $('.list_controller').removeAttr('onclick');
            var downGroup = $("[data-no=" + (num_firstIcons) + "]");
            var downGroupId = $(downGroup).attr('data-id');
            $.ajax({
                url: 'group/moveDownGroup.php',
                data: {
                    id1: id,
                    id2: downGroupId,
                    index1: index,
                    index2: num_firstIcons
                },
                type: 'post',
                success: function (result) {
                    reloadGroups(result, id, num_firstIcons);
                }
            })
            // alert("The group can't move downwards.");
        } else {
            $('.list_controller').removeAttr('onclick');
            var downGroup = $("[data-no=" + (index + num_group_in_row) + "]");
            var downGroupId = $(downGroup).attr('data-id');
            $.ajax({
                url: 'group/moveDownGroup.php',
                data: {
                    id1: id,
                    id2: downGroupId,
                    index1: index,
                    index2: index + num_group_in_row
                },
                type: 'post',
                success: function (result) {
                    reloadGroups(result, id, index + num_group_in_row);
                }
            })
        }
    }

    //change place between two groups
    function switchGroup(id1, index1, id2, index2) {
        $.ajax({
            url: 'group/switchGroup.php',
            data: {
                id1: id1,
                index1: index1,
                id2: id2,
                index2: index2
            },
            type: 'post',
            success: function (result) {
                reloadGroups(result, id1, index2);
            }
        })
    }

    function switchGroupWK(id1, index1, id2, index2) {
        $.ajax({
            url: 'group/switchGroupWK.php',
            data: {
                id1: id1,
                index1: index1,
                id2: id2,
                index2: index2,
            },
            type: 'post',
            success: function (result) {
                var data = JSON.parse(result);
                reloadGroupsWK(data, id1, index2);
            }
        })
    }

    //replace groups in the toolbox with AJAX
    function reloadGroups(result, id, index) {
        $(".TB").remove();
        $(".addGroup").remove();
        $("#toolbox").append(result);
        showGroupController(id, index);
    }

    //replace groupWKs in the workingBoard with AJAX
    function reloadGroupsWK(data, id, index) {

        change = 'true';

        $(".WK").remove();
        $(".emptyWK").remove();

        var text = "";
        for (var i = 0; i < data.length; i++) {
            var order = data[i].workingBoardOrder;
            text += "<span class='plus first_icons WK' data-id='" + data[i].id + "' data-no='" + order + "' onclick='showGroupControllerWK(" +
                data[i].id + ", " + order + ")' style='background-image: url(" + data[i].groupIconPath + ")' ondblclick='removeFromWK(" + data[i].id + ", " + order + ")'></span>";
        }
        for (var j = 0; j < 300; j++) {
            text += "<span class='plus emptyWK' style='background-image: url(" + "assets/img/h.PNG" + ")'></span>";
        }

        $("#workingBoard").append(text);

        if (id == 0) {
            $('#WKgroupController').attr('visiblity', 'hidden');
        } else {
            showGroupControllerWK(id, index);
        }
    }

    //Management of Working Board
    function moveLeftWK(id, index) {
        var firstIconsWK = $('.WK');
        var num_firstIconsWK = firstIconsWK.length;
        if (index == num_firstIconsWK) {
            alert("The group can't move to the left");
            showGroupControllerWK(id, index);
        } else {
            $('.list_WKcontroller').removeAttr('onclick');
            var index2 = index + 1;
            var leftGroupWK = $(".WK[data-no=" + (index + 1) + "]");
            var leftGroupIdWK = parseInt($(leftGroupWK).attr('data-id'));
            switchGroupWK(id, index, leftGroupIdWK, index2);
        }
    }

    function moveRightWK(id, index) {
        if (index == 1) {
            alert("The group can't move to the right.");
            showGroupControllerWK(id, index);
        } else {
            $('.list_WKcontroller').removeAttr('onclick');
            var rightGroup = $(".WK[data-no=" + (index - 1) + "]");
            var rightGroupId = $(rightGroup).attr('data-id');
            switchGroupWK(id, index, rightGroupId, index - 1);
        }
    }

    function moveUpWK(id, index) {
        if (index - num_group_in_row < 1) {
            // $('.list_WKcontroller').removeAttr('onclick');
            var upGroup = $(".WK[data-no='1']");
            var upGroupId = $(upGroup).attr('data-id');
            $.ajax({
                url: 'group/moveUpGroupWK.php',
                data: {
                    id1: id,
                    id2: upGroupId,
                    index1: index,
                    index2: 1,
                    designId: designId
                },
                type: 'post',
                success: function (result) {
                    var data = JSON.parse(result);
                    reloadGroupsWK(data, id, 1);
                }
            })
        } else {
            // $('.list_WKcontroller').removeAttr('onclick');
            var upGroup = $(".WK[data-no=" + (index - num_group_in_row) + "]");
            var upGroupId = $(upGroup).attr('data-id');
            $.ajax({
                url: 'group/moveUpGroupWK.php',
                data: {
                    id1: id,
                    id2: upGroupId,
                    index1: index,
                    index2: index - num_group_in_row,
                    designId: designId
                },
                type: 'post',
                success: function (result) {
                    var data = JSON.parse(result);
                    reloadGroupsWK(data, id, index - num_group_in_row);
                }
            })
        }
    }

    function moveDownWK(id, index) {
        var firstIconsWK = $('.WK');
        var num_firstIconsWK = firstIconsWK.length;
        if (index + num_group_in_row > num_firstIconsWK) {
            // $('.list_WKcontroller').removeAttr('onclick');
            var downGroup = $(".WK[data-no=" + num_firstIconsWK + "]");
            console.log(downGroup);
            var downGroupId = $(downGroup).attr('data-id');
            $.ajax({
                url: 'group/moveDownGroupWK.php',
                data: {
                    id1: id,
                    id2: downGroupId,
                    index1: index,
                    index2: num_firstIconsWK,
                    designId: designId
                },
                type: 'post',
                success: function (result) {
                    var data = JSON.parse(result);
                    reloadGroupsWK(data, id, num_firstIconsWK);
                }
            })
        } else {
            // $('.list_WKcontroller').removeAttr('onclick');
            var downGroup = $("[data-no=" + (index + num_group_in_row) + "]");
            var downGroupId = $(downGroup).attr('data-id');
            $.ajax({
                url: 'group/moveDownGroupWK.php',
                data: {
                    id1: id,
                    id2: downGroupId,
                    index1: index,
                    index2: index + num_group_in_row,
                    designId: designId
                },
                type: 'post',
                success: function (result) {
                    var data = JSON.parse(result);
                    reloadGroupsWK(data, id, index + num_group_in_row);
                }
            })
        }
    }

    var designName = '<?= $designName?>';
    var designId = '<?= $designId?>';
    var designType = '<?= $designType?>';

    function removeFromWK(id, index) {
        $('.list-inline-item').removeAttr('onclick');
        $.ajax({
            url: 'group/removeFromWK.php',
            data: {
                WKgroupId: id,
                removeIndex: index,
                designId: designId
            },
            type: 'post',
            success: function (result) {
                change = 'true';
                var data = JSON.parse(result);
                reloadGroupsWK(data, 0, 0);
            }
        })
    }

    if (designName !== '') {
        $('a.disabled').removeClass('disabled');
        $('#workingBoard').show();
        $('#arrowUpWK').show();
        $('#arrowDownWK').show();
        //canvas grid show
        $('#grid_toolbox').show();
        $('#div_canvas_grid').show();
        updateCanvas();
    }

    if (designName === '') {
        $('#grid').hide();
    }

    function addToWK(id) {
        $('.list-inline-item').removeAttr('onclick');
        if (designName !== '') {
            $.ajax({
                url: 'group/addToWK.php',
                data: {
                    groupId: id,
                    designId: designId,
                    designName: designName
                },
                type: 'post',
                success: function (result) {

                    change = 'true';

                    var data = JSON.parse(result);
                    reloadGroupsWK(data, 0, 0);
                }
            })
        }
    }

    //about grid
    var zoom_changed = false;

    var animationFrame;

    var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
        window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;

    var cancelAnimationFrame = window.cancelAnimationFrame || window.mozCancelAnimationFrame;

    // change zoom of grid according to the zoom select
    function changeZoomGrid() {
        updateCanvas();
    }


    var vpx = 0
    var vpy = 0
    var vpw = window.innerWidth
    var vph = window.innerHeight

    var orig_width = 8000;
    var orig_height = 8000;

    var width = 8000
    var height = 8000
    var min_ratio = 0.3;
    var max_ratio = 3.0;

    function updateCanvas() {
        var zoom_box_value = $('#zoom_grid_input').val();
        var ratio = zoom_box_value / 100;

        var canvas = document.getElementById('grid');
        canvas.style.cursor = "pointer";
        var canvasContext = canvas.getContext('2d');

        canvasContext.clearRect(0, 0, canvas.width, canvas.height)

        canvasContext.lineWidth = 1
        canvasContext.strokeStyle = '#ccc'

        var step = 20 * ratio;

        var i = designType - 2;
        var j = designType - 2;

        // var timeX = 0, timeY = 0;
        for (var x = vpx; x < canvas.width; x += step) {

            i++;
            // timeX++;
            if (i == designType) {
                canvasContext.strokeStyle = 'black';
                i = 0;
            } else {
                canvasContext.strokeStyle = '#ccc';
            }

            canvasContext.beginPath()
            canvasContext.moveTo(x, vpy)
            canvasContext.lineTo(x, vpy + height)
            canvasContext.stroke()
        }
        for (var y = vpy; y < canvas.height; y += step) {

            j++;
            // timeY++;
            if (j == designType) {
                canvasContext.strokeStyle = 'black';
                j = 0;
            } else {
                canvasContext.strokeStyle = '#ccc';
            }

            canvasContext.beginPath()
            canvasContext.moveTo(vpx, y)
            canvasContext.lineTo(vpx + width, y)
            canvasContext.stroke()
        }
        // console.log("times", timeX, timeY);

        canvasContext.strokeRect(vpx, vpy, width, height)

        canvasContext.restore()
    }

    $(document).ready(function () {
        $('#grid').on('wheel', function (ev) {
            ev.preventDefault() // for stackoverflow

            var zoom_box_value = $('#zoom_grid_input').val();
            var ratio = zoom_box_value / 100;

            var step;

            if (ev.originalEvent.wheelDelta) {
                step = (ev.originalEvent.wheelDelta > 0) ? 0.1 : -0.1
            }

            if (ev.originalEvent.deltaY) {
                step = (ev.originalEvent.deltaY > 0) ? 0.1 : -0.1
            }

            if (!step) return false // yea..

            var new_ratio = ratio + step;

            if (new_ratio < min_ratio) {
                new_ratio = min_ratio
            }

            if (new_ratio > max_ratio) {
                new_ratio = max_ratio
            }

            // zoom center point
            var targetX = ev.originalEvent.clientX || (vpw / 2)
            var targetY = ev.originalEvent.clientY || (vph / 2)

            // percentages from side
            var pX = ((vpx * -1) + targetX) * 100 / width
            var pY = ((vpy * -1) + targetY) * 100 / height

            // update ratio and dimentsions
            ratio = new_ratio
            width = orig_width * new_ratio
            height = orig_height * new_ratio

            // translate view back to center point
            var x = ((width * pX / 100) - targetX)
            var y = ((height * pY / 100) - targetY)

            // don't let viewport go over edges
            if (x < 0) {
                x = 0
            }

            if (x + vpw > width) {
                x = width - vpw
            }

            if (y < 0) {
                y = 0
            }

            if (y + vph > height) {
                y = height - vph
            }

            vpx = x * -1
            vpy = y * -1


            var zoom_level = Math.round(ratio * 10) * 10;
            $('#zoom_grid_input').children('option[selected="selected"]').removeAttr('selected');
            var selectedElem = $('#zoom_grid_input').children('option[value=' + zoom_level + ']');
            $(selectedElem).prop('selected', true);

            updateCanvas();
        });

        var is_down, is_drag, last_drag;

        $('#grid').on('mousedown', function (ev) {
            is_down = true
            is_drag = false
            last_drag = {x: ev.clientX, y: ev.clientY}
        })

        $('#grid').on('mousemove', function (ev) {
            is_drag = true

            if (is_down) {
                var x = vpx - (last_drag.x - ev.clientX)
                var y = vpy - (last_drag.y - ev.clientY)

                if (x <= 0 && vpw < x + width) {
                    vpx = x
                }

                if (y <= 0 && vph < y + height) {
                    vpy = y
                }

                last_drag = {x: ev.clientX, y: ev.clientY}

                updateCanvas();
            }
        });

        $('#grid').on('mouseup', function (ev) {
            is_down = false;
            last_drag = null;

            is_drag = false;

        });

        $(window).on('resize', function () {
            initializeCanvas();
        }).trigger('resize')

        initializeCanvas();
    });

    function initializeCanvas() {
        var zoom_box_value = $('#zoom_grid_input').val();
        var ratio = zoom_box_value / 100;

        if (ratio > 1) {
            $('#grid').prop({
                width: window.innerWidth * ratio,
                height: 600 * ratio,
            })
        } else {
            $('#grid').prop({
                width: window.innerWidth,
                height: 600,
            })
        }

        updateCanvas();
    }
</script>
</html>
