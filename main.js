var new_num = 0;
var new_num_array = [];
var replace_array = [];
var replace_id = [];

$(document).ready(function () {



    var addGroupIcon = "";
    var addBlockIcon = "";
    // var manageBlockIcon = "";
    var workingCell = "";
    for (var i = 1; i <= 300; i++) {
        addGroupIcon += "<span class='plus addGroup' onclick='addBlockGroup()' style='background-image: url(" + "assets/img/ASSETS/ICONAsset178.svg" + ")'></span>" + "\n";
        addBlockIcon += "<input type='file' name='new" + i + "' class='inputfile hiddenUpload' id='new" + i + "'><label for='new" + i + "' class='plus addBlock' style='background-image: url(" + "assets/img/ASSETS/ICONAsset175.svg" + ")'></label>" + "\n";
        // manageBlockIcon += "<input type='file' name='manage" + i + "' class='inputfile manageHiddenUpload' id='manage" + i + "'>";
        workingCell += "<span class='plus emptyWK' style='background-image: url(" + "assets/img/h.PNG" + ")'></span>" + "\n";
    }

    $('#toolbox').append(addGroupIcon);
    $('#icon_toolbox').append(addBlockIcon);
    $('#workingBoard').append(workingCell);
    // $('#manage_icon_toolbox').append(manageBlockIcon);

    // $('#zoom_grid_input').on('change', function () {
    //     gridShow();
    // });

    $('#addGroupModal').on("hidden.bs.modal", function () {
        $('#groupName_valid').hide();
        $('#groupIcon_valid').hide();
        $('.previewAddBlocks').remove();
        $('.newUploadBlocks').remove();
        $('#preview_addGroupIcon').empty();
        $('#groupName').val('');
        $('#uploadGroupIcon').val('');
        $('#addUploadBlocks').val('');
        new_num = 0;
        new_num_array = [];
    });

    $('#manageGroupModal').on("hidden.bs.modal", function () {
        new_num = 0;
        new_num_array = [];
        replace_array = [];
        replace_id = [];
    });

    $('#uploadGroupIcon').change(function () {
        $('#preview_addGroupIcon').empty();
        $('#preview_addGroupIcon').append("<img src='" + URL.createObjectURL(event.target.files[0]) + "'>");
    });

    $('#addUploadBlocks').change(function () {
        var num_uploads = document.getElementById("addUploadBlocks").files.length;
        for (var i = 0; i < num_uploads; i++) {
            $('#icon_toolbox').prepend("<span class='plus newUploadBlocks' style='background-image: url(" + URL.createObjectURL(event.target.files[i]) + ")'></span>");
            new_num_array.push(document.getElementById("addUploadBlocks").files[i]);
        }
    });

    $('#manage_upload_block').change(function () {
        var num_uploads = document.getElementById("manage_upload_block").files.length;
        $('.manageAddBlock').remove();

        for (var i = 0; i < num_uploads; i++) {
            $('#manage_icon_toolbox').append("<span class='plus manageUploadBlocks' style='background-image: url(" + URL.createObjectURL(event.target.files[i]) + ")'></span>")
            var upload = document.getElementById("manage_upload_block").files[i];
            new_num_array.push(upload);
            new_num++;
        }
        manage_appendLabels();
    });

//add blocks in the addGroup Modal one by one clicking plus button
    $('.hiddenUpload').change(function () {
        var id = $(this).attr('id');
        var uploads = document.getElementById(id).files[0];
        new_num_array.push(uploads);
        $('#icon_toolbox').prepend("<span class='plus newUploadBlocks' style='background-image: url(" + URL.createObjectURL(event.target.files[0]) + ")'></span>");
        new_num++;
    });

    $('#manageUploadGroupIcon').change(function () {
        $('#preview_manageGroupIcon').empty();
        $('#preview_manageGroupIcon').append("<img src='" + URL.createObjectURL(event.target.files[0]) + "'>");
    });

});

function addGroupSubmit() {

    var groupName = $('#groupName').val();
    var groupIcon = $('#uploadGroupIcon').val();
    var groupTypeElem = $(".addGroupType:checked");
    var groupType = $(groupTypeElem).val();

    $('#num_new').val(new_num);
    if (groupName == '') {
        $('#groupName_valid').show();
    } else {
        $('#groupName_valid').hide();
    }
    if (groupIcon == '') {
        $('#groupIcon_valid').show();
    } else {
        $('#groupIcon_valid').hide();
    }
    if (groupIcon !== '' && groupName !== '') {

        var form_data = new FormData();
        form_data.append("groupName", groupName);
        form_data.append("numNew", new_num);
        form_data.append("groupIcon", document.getElementById('uploadGroupIcon').files[0]);
        form_data.append("groupType", groupType);

        var uploadBlocks = document.getElementById('addUploadBlocks').files.length;
        for (var i = 0; i < uploadBlocks; i++) {
            form_data.append("file_uploadBlock[]", document.getElementById('addUploadBlocks').files[i]);
        }

        for (var j = 0; j < new_num_array.length; j++) {
            form_data.append("new[]", new_num_array[j]);
        }

        console.log(new_num_array);

        $.ajax({
            url: 'addBlockGroup.php',
            type: 'post',
            data: form_data,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function (result) {

                console.log(result);

                $('#addGroupModal').modal('hide');
                $(".TB").remove();
                $(".addGroup").remove();
                var txt = '';
                for (var i = 0; i < result.length; i++) {
                    txt += "<span class='plus first_icons TB' data-id='" + result[i].id + "' data-no='" + result[i].toolboxOrder + "' " +
                        "onclick='showGroupController(" + result[i].id + ", " + result[i].toolboxOrder + ")' " +
                        "style='background-image: url(" + result[i].iconPath + ")' ondblclick='addToWK(" + result[i].id + ")'></span>";
                    if (i == result.length - 1) {
                        var newId = result[i].id;
                        var newIndex = result[i].toolboxOrder;
                    }
                }
                for (var i = 1; i <= 300; i++) {

                    txt += "<span class='plus addGroup' onclick='addBlockGroup()' style='background-image: url(" + "assets/img/ASSETS/ICONAsset178.svg" + ")'></span>" + "\n";
                }
                $('#toolbox').append(txt);
                showGroupController(newId, newIndex);
            }
        })
    }
}

function submitManageForm() {
    var groupName = $('#manageGroupName').val();
    var groupIcon = $('#manageUploadGroupIcon').val();
    var groupId = $('#groupId').val();
    var oldGroupName = $('#oldGroupName').val();
    var groupType = $('#hiddenGroupType').val();
    $('#num_manage').val(new_num);
    var blockId = $('#hiddenBlockId').val();
    var hiddenBlockName = $('#hiddenBlockName').val();
    var blockName = $('#blockName').val();

    var form_data = new FormData();

    form_data.append("groupId", groupId);
    form_data.append("groupName", groupName);
    form_data.append("oldGroupName", oldGroupName);
    form_data.append("groupIcon", document.getElementById('manageUploadGroupIcon').files[0]);
    form_data.append("groupType", groupType);
    var uploadBlocks = document.getElementById('manage_upload_block').files.length;
    for (var i = 0; i < uploadBlocks; i++) {
        form_data.append("file_uploadBlock[]", document.getElementById('manage_upload_block').files[i]);
    }
    form_data.append("hiddenBlockId", blockId);
    form_data.append("hiddenBlockName", hiddenBlockName);
    form_data.append("blockName", blockName);
    form_data.append("file_replaceBlock", document.getElementById('file_replaceBlock').files[0]);

    // for (var i = 0; i < replace_array.length; i++) {
    //     form_data.append("replace[]", replace_array[i]);
    // }

    form_data.append("num_manage", new_num);

    for (var j = 0; j < new_num_array.length; j++) {
        form_data.append("manage[]", new_num_array[j]);
    }

    // for (var k = 0; k < replace_id.length; i++) {
    //     form_data.append("replaceId[]", replace_array[k]);
    // }

    console.log(form_data);

    $.ajax({
        url: 'modifyGroup.php',
        type: 'post',
        data: form_data,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (result) {


            $('#manageGroupModal').modal('hide');
            $('.TB').remove();
            $('.addGroup').remove();
            var txt = '';
            for (var i = 0; i < result.length; i++) {
                txt += "<span class='plus first_icons TB' data-id='" + result[i].id + "' data-no='" + result[i].toolboxOrder + "' " +
                    "onclick='showGroupController(" + result[i].id + ", " + result[i].toolboxOrder + ")' " +
                    "style='background-image: url(" + result[i].iconPath + ")' ondblclick='addToWK(" + result[i].id + ")'></span>";
                if (result[i].id == groupId) {
                    var newId = result[i].id;
                    var newIndex = result[i].toolboxOrder;
                }
            }
            for (var i = 1; i <= 300; i++) {
                txt += "<span class='plus addGroup' onclick='addBlockGroup()' style='background-image: url(" + "assets/img/ASSETS/ICONAsset178.svg" + ")'></span>" + "\n";
            }
            $('#toolbox').append(txt);
            showGroupController(newId, newIndex);
        }
    })
}

var canvas = document.getElementById('grid');

function addBlockGroup() {
    $('#addGroupModal').modal();
}

function addNewDesign() {
    $('#newDesignModal').modal();
}

function showGroupController(id, index) {
    //highlight selected group
    var selectedGroup = $(".TB[data-id=" + id + "]");
    $(".TB").css("border", "1px solid black");
    $(selectedGroup).css("border", "3px solid blue");

    var groupControllerElem = "<li class='list-inline-item list_controller' style='padding-right:7px; border-right: 1px solid black' onclick='showGroup(" + id + "," + index + ")'><a href='#'>Manage Group</a></li>" +
        "<li class='list-inline-item list_controller' style='padding-right:7px; border-right: 1px solid black' onclick='moveLeft(" + id + "," + index + ")'><a href='#'>Move Left</a></li>" +
        "<li class='list-inline-item list_controller' style='padding-right:7px; border-right: 1px solid black' onclick='moveRight(" + id + "," + index + ")'><a href='#'>Move Right</a></li>" +
        "<li class='list-inline-item list_controller' style='padding-right:7px; border-right: 1px solid black' onclick='moveUp(" + id + "," + index + ")'><a href='#'>Move Up</a></li>" +
        "<li class='list-inline-item list_controller' id='lastGroupController' onclick='moveDown(" + id + "," + index + ")'><a href='#'>Move Down</a></li>";
    $('#groupController').empty();
    $('#groupController').css('visibility', 'visible');
    $('#groupController').append(groupControllerElem);
}

function showGroupControllerWK(id, index) {
    //highlight selected groupWK
    var selectedGroupWK = $(".WK[data-id=" + id + "]");
    $(".WK").css("border", "1px solid black");
    $(selectedGroupWK).css("border", "3px solid blue");

    var groupControllerElem = "<li class='list-inline-item list_WKcontroller' style='padding-right:7px; border-right: 1px solid black' onclick='removeFromWK(" + id + "," + index + ")'><a href='#'>Remove</a></li>" +
        "<li class='list-inline-item list_WKcontroller' style='padding-right:7px; border-right: 1px solid black' onclick='moveLeftWK(" + id + "," + index + ")'><a href='#'>Move Left</a></li>" +
        "<li class='list-inline-item list_WKcontroller' style='padding-right:7px; border-right: 1px solid black' onclick='moveRightWK(" + id + "," + index + ")'><a href='#'>Move Right</a></li>" +
        "<li class='list-inline-item list_WKcontroller' style='padding-right:7px; border-right: 1px solid black' onclick='moveUpWK(" + id + "," + index + ")'><a href='#'>Move Up</a></li>" +
        "<li class='list-inline-item list_WKcontroller' id='lastGroupController' onclick='moveDownWK(" + id + "," + index + ")'><a href='#'>Move Down</a></li>";
    $('#WKgroupController').empty();
    $('#WKgroupController').css('visibility', 'visible');
    $('#WKgroupController').append(groupControllerElem);
}

function showGroup(id, index) {

    var btn_delGroupElem = '<button id="btn_manage_del" type="button" style="background-color: transparent;color:red" ' +
        'onclick="deleteGroup(' + id + "," + index + ')">' + 'Delete Group' + '</button>';
    $('#div_delGroup').empty();
    $('#div_delGroup').append(btn_delGroupElem);
    //place groupIconPath in the preview box
    var current_iconPath = $(".TB[data-id='" + id + "']").css('background-image');
    current_iconPath = current_iconPath.replace('url(', '').replace(')', '').replace(/\"/gi, "");

    $('#preview_manageGroupIcon').empty();
    $('#preview_manageGroupIcon').append("<img src='" + current_iconPath + "'>");

    $.ajax({
        url: 'getGroupData.php',
        data: {groupId: id},
        type: 'post',
        success: function (success) {
            var data = JSON.parse(success);
            var num = data['num'];
            var blockData = data['blockData'];
            var blocks = "";
            for (var i = 0; i < blockData.length; i++) {
                var blockIcon = blockData[i].iconPath;
                var blockId = blockData[i].id;
                blocks += "<span class='plus savedBlocks' data-id='" + blockId + "' onclick='manageBlock(" + blockId + ")' " +
                    "style='background-image: url(" + blockIcon + ")'></span>" + "\n";
            }
            $('#manage_icon_toolbox').empty();
            $('#manage_icon_toolbox').append(blocks);

            manage_appendLabels();

            //set the value of inputs before submitting
            $('#groupId').val(id);
            var groupName = data['groupData']['name'];
            $('#manageGroupName').attr('placeholder', groupName);
            $('#manageGroupName').val(groupName);
            $('#oldGroupName').val(groupName);

            //customize group type checkbox(desable check in the checkbox)
            var groupType = data['groupData']['type'];
            $('#hiddenGroupType').val(groupType);
            var typeElements = $('.manageGroupType');
            for (var j = 0; j < typeElements.length; j++) {
                $(typeElements[j]).removeAttr('checked');
                var typeValue = $(typeElements[j]).val();
                if (typeValue == groupType) {
                    $(typeElements[j]).prop('checked', true);
                }
                $(typeElements[j]).attr('disabled', 'disabled');
            }
            //
            $('#manageGroupModal').modal();
        }
    })
}

//Event after clicking the current block in the MANAGE BLOCK GROUP MODAL
function manageBlock(id) {

    //highlight selected block
    $('.savedBlocks').css('border', 'none');
    var selectedBlock = $(".savedBlocks[data-id='" + id + "']");
    $(selectedBlock).css('border', '2px solid blue');

    //show Replace Block and Delete Block options
    var controllerList = "<li class='list-inline-item' id='icon_replace'>" +
        "<input type='file' onchange='previewReplace(" + id + ")' class='form-control inputfile' id='file_replaceBlock' name='file_replaceBlock'/>" +
        "<label for='file_replaceBlock' style='font-size: 1em'>Replace Block</label></li>" +
        "<li class='list-inline-item' id='icon_delete' onclick='deleteBlock(" + id + ")'>" +
        "<a href='#' style='color:red'>Delete Block</a>" + "</li>";
    $('#icon_controllers').empty();
    $('#icon_controllers').append(controllerList);//attach Delete Block Option
    $('#icon_controllers').show();

    $.ajax({
        url: 'getBlockData.php',
        data: {blockId: id},
        type: 'post',
        success: function (result) {
            var data = JSON.parse(result);
            var blockName = data.name;
            $('#blockName').remove();
            var text = "<input type='text' name='blockName' id='blockName' style='width: 100%'" +
                "placeholder='" + blockName + "' value='" + blockName + "'>";
            $('#div_blockName').empty();
            $('#div_blockName').append(text);
            $('#hiddenBlockId').val(id);
            $('#hiddenBlockName').val(blockName);
        }
    })
}

function deleteGroup(id, index) {
    if (confirm("Do you want to delete it?")) {
        $.ajax({
            url: 'deleteGroup.php',
            data: {
                groupId: id,
                toolboxOrder: index
            },
            type: 'post',
            success: function (success) {
                var result = JSON.parse(success);
                console.log(result);
                if (!result['success']) {
                    // alert('You cannot delete this group because it is used in the previous projects.');
                    $('#designNamesList').empty();
                    for (var i = 0; i < result['designNames'].length; i++) {
                        $('#designNamesList').append("<li>" + result['designNames'][i] + "</li>");
                    }
                    // $('#manageGroupModal').modal('hide');
                    $('#deleteGroupModal').modal('show');
                } else {
                    alert('The group was deleted successfully.');
                    reloadGroups(result['data'], id, index);
                    $('#manageGroupModal').modal('hide');
                }
            }
        })
    }
}

function deleteBlock(id) {
    if (confirm("Do you want to delete it?")) {
        $.ajax({
            url: 'deleteBlock.php',
            data: {blockId: id},
            type: 'post',
            success: function (result) {
                alert(result);
                $(".savedBlocks[data-id='" + id + "']").remove();
            }
        })
    }
}

//DESIGN MANAGEMENT
function openDesign() {

    $('#openDesignModal').modal();
}

function openDesignSubmit() {
    if (change == 'true' && designId != '') {
        if (confirm('You have made changes to this project, would you like to save these changes?')) {
            var id = parseInt(designId);
            $.ajax({
                url: 'design/saveDesign.php',
                data: {designId: id},
                type: 'post',
                success: function (result) {
                    alert('The design was saved successfully.');
                    // window.location.reload();
                    $('#form_openDesign').submit();
                }
            })
        } else {
            $('#form_openDesign').submit();
        }
    } else {
        $('#form_openDesign').submit();
    }
}

function closeDesign() {
    if (change == 'true') {
        if (confirm('You have made changes to this project, would you like to save these changes?')) {
            $.ajax({
                url: 'design/saveDesign.php',
                data: {designId: designId},
                type: 'post',
                success: function (result) {
                    $.ajax({
                        url: 'design/closeDesign.php',
                        type: 'post',
                        success: function (result) {
                            window.location.reload();
                        }
                    });
                }
            })
        } else {
            $.ajax({
                url: 'design/closeDesign.php',
                type: 'post',
                success: function (result) {
                    window.location.reload();
                }
            });
        }
    } else {
        $.ajax({
            url: 'design/closeDesign.php',
            type: 'post',
            success: function (result) {
                window.location.reload();
            }
        });
    }
}

function saveDesign(id) {
    if (confirm('Do you want to save this design?')) {
        $.ajax({
            url: 'design/saveDesign.php',
            data: {designId: id},
            type: 'post',
            success: function (result) {
                alert('The design was saved successfully.');
                window.location.reload();
            }
        })
    }
}

function editDesign(id) {
    $('#editBoardTitle').attr('placeholder', designName);
    $('#editBoardTitle').val(designName);
    $('#editDesignId').val(id);
    $('#oldDesignName').val(designName);
    $('#oldDesignType').val(designType);

    //replace the delete button
    $('#div_del_design').empty();
    var btn_delDesignElem = '<button id="btn_design_del" type="button" style="background-color: transparent;color:red" ' +
        'onclick="deleteDesign(' + id + ')">' + 'Delete Design' + '</button>';
    $('#div_del_design').append(btn_delDesignElem);

    var typeElements = $('.manageEditType');
    for (var j = 0; j < typeElements.length; j++) {
        $(typeElements[j]).removeAttr('checked');
        var typeValue = $(typeElements[j]).val();
        if (typeValue == designType) {
            $(typeElements[j]).prop('checked', true);
        }
        // $(typeElements[j]).attr('disabled', 'disabled');
    }

    $('#editDesignModal').modal();
}

function deleteDesign(id) {
    if (confirm('Do you want to delete this design?')) {
        $.ajax({
            url: 'design/deleteDesign.php',
            data: {
                designId: id
            },
            type: 'post',
            success: function (result) {
                if (result) {
                    alert("The design was deleted successfully.");
                    window.location.reload();
                }
            }
        })
    }

}

function arrowDown() {
    $("#toolbox").animate({scrollTop: $('#toolbox').scrollTop() + 45}, 300);
}

function arrowUp() {
    $("#toolbox").animate({scrollTop: $('#toolbox').scrollTop() - 45}, 300);
}

function arrowDownWK() {
    $("#workingBoard").animate({scrollTop: $('#workingBoard').scrollTop() + 45}, 300);
}

function arrowUpWK() {
    $("#workingBoard").animate({scrollTop: $('#workingBoard').scrollTop() - 45}, 300);
}

function arrowUpAddGroup() {
    $("#icon_toolbox").animate({scrollTop: $('#icon_toolbox').scrollTop() - 45}, 300);
}

function arrowDownAddGroup() {
    $("#icon_toolbox").animate({scrollTop: $('#icon_toolbox').scrollTop() + 45}, 300);
}

function arrowUpManageGroup() {
    $("#manage_icon_toolbox").animate({scrollTop: $('#manage_icon_toolbox').scrollTop() - 45}, 300);
}

function arrowDownManageGroup() {
    $("#manage_icon_toolbox").animate({scrollTop: $('#manage_icon_toolbox').scrollTop() + 45}, 300);
}


function previewReplace(id) {
    var selectedBlock = $(".savedBlocks[data-id='" + id + "']");

    var replace = document.getElementById('file_replaceBlock').files[0];
    replace_array.push(replace);
    replace_id.push(id);
    $('#icon_controllers').append("<input type='hidden' name ='replaceId[]' value='" + id + "'>");

    $(selectedBlock).css('background-image', 'url(' + URL.createObjectURL(event.target.files[0]) + ')');
}

function newDesignSubmit() {
    var designName = $('#newBoardTitle').val();
    var designType = $('.manageEditType').val();
    if (designName == '') {
        $('#designName_valid').show();
    } else {
        $('#designName_valid').hide();
    }
    if (designType == '') {
        $('#designType_valid').show();
    } else {
        $('#designType_valid').hide();
    }
    if (designName !== '' && designType !== '') {
        $('#form_addDesign').submit();
    }
}

function manage_appendLabels() {
    var blocks = "";
    for (var i = 1; i <= 300; i++) {

        blocks += "<input type='file' onchange='manageUploadChange(" + i + ")' name='manage" + i + "' class='inputfile manageHiddenUpload' id='manage" + i + "'>" +
            "<label for='manage" + i + "' class='plus manageAddBlock' style='background-image: url(" + "assets/img/ASSETS/ICONAsset175.svg" + ")'></label>";
    }
    $('#manage_icon_toolbox').append(blocks);
}

function manageUploadChange(id) {
    var manageId = "manage" + id;
    var upload = document.getElementById(manageId).files[0];
    new_num_array.push(upload);
    $('.manageAddBlock').remove();
    $('#manage_icon_toolbox').append("<span class='plus manageUploadBlocks' style='background-image: url(" + URL.createObjectURL(event.target.files[0]) + ")'></span>");
    manage_appendLabels();
    new_num++;
}