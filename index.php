<?php
include "Model/Init.php";
include "Model/Model.php";
$model = new Model();
$counts = $model->getImportDataCount();
$inboxCount = 0;
$approvedCount = 0;
$rejectedCount = 0;
if($counts){
    $inboxCount = $counts['inboxCount'];
    $approvedCount = $counts['approvedCount'];
    $rejectedCount = $counts['rejectedCount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Influencer Sorting App</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link rel="stylesheet" href="assets/dropzone.min.css">
    <script src="assets/jquery-3.4.1.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        body{
            font-family: 'Roboto', sans-serif;
        }
        .profile-avatar {
            text-align: center;
            margin-bottom: 12px;
        }

        .profile-avatar .avatar img {
            border-radius: 50%;
            border: 2px solid #e5e5e5;
            min-height: 200px;
        }

        .form {
            margin-bottom: 24px;
        }

        .footer {
            position: fixed;
            bottom: 12px;
            right: 12px;
            text-align: right;
        }
        .igContainer{
            position: fixed;
            top: 0;
        }
        .header-logo{
            width: 100%;
            text-align: center;
        }
        .left-nav{
            height: 100vh;
            padding-top: 120px;
        }
        .left-nav .list-group li{
            background: none;
            color: #ffffff;
            border: none;
        }
        .dz-container{
            padding-top: 120px;
        }
        .ig-table{
            padding-top: 120px;
        }

        .no-followers-container{
            background: #e5e5e5;
            min-height: 90px;
            width: 90%;
            margin: auto;
            padding: 24px;
            text-align: center;
        }
        .handle-container{
            margin-top: 24px;
            background: #e5e5e5;
            min-height: 30px;
            width: 100%;
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: center;
            color: #000000;
        }

        .follower-handle-container{
            padding-top: 42px;
        }

        .timeline-container{
            height: 680px;
        }
        .timeline-container .col-md-4{
            /*max-height: 183px;*/
            margin-bottom: ;
        }
        .list-group-item:hover{
            cursor: pointer;
        }
        .list-group-item.active{
            background: #ffffff !important;
            color: #000000 !important;
        }
    </style>
</head>
<body>
<header class="header-nav">
    <nav class="navbar fixed-top navbar-light bg-dark">
        <div class="container-fluid">
            <div class="header-logo"><img style="width: 120px;" src="assets/vehla-logo.png"></div>
        </div>
    </nav>
</header>
<div class="overlay d-none" style="
    height: 100vh;
    position: absolute;
    background: rgba(0,0,0, .2);
    width: 100%;
    z-index: 99999;
    text-align: center;
    padding-top: 60px;
">Loading Data....
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 bg-dark left-nav">
            <ul class="list-group">
                <li class="list-group-item active" data-type="inbox">Inbox <span class="float-right inbox-no"><?php echo $inboxCount; ?></span></li>
                <li class="list-group-item" data-type="approved">Approved <span class="float-right approved-no"><?php echo $approvedCount; ?></span></li>
                <li class="list-group-item" data-type="rejected">No <span class="float-right no-no"><?php echo $rejectedCount; ?></span></li>
            </ul>
        </div>
        <div class="col-md-10 dz-container <?php if($counts) echo 'd-none'; ?>">
            <div class="row form">
                <div class="col-md-2">
                    <div class="card">
                        <form action="/target" class="dropzone"></form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10 ig-table <?php if(!$counts) echo 'd-none'; ?>">
            <div class="row">
                <div class="col-md-6">
                    <table class="table" id="ig-table">
                        <thead>
                        <tr>
                            <th class="d-none">Data</th>
                            <th>IG Handle</th>
                            <th>Followers</th>
                        </tr>
                        </thead>
                        <tbody id="ig-table-body">

                        </tbody>
                    </table>
                </div>
                <div class="col-md-5 row" id="profile-container-main">
                    <div class="col-md-4 follower">
                        <div class="no-followers-container">
                            <span class="noFollowers">478k</span>
                            <br>
                            <span>Followers</span>
                        </div>
                        <div class="handle-container"></div>
                    </div>
                    <div class="col-md-6"><div class="profile-avatar"></div></div>

                    <div class="timeline-container row col-md-12">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <button class="btn btn-primary btn-actions" data-action="approved">Download Approved</button>
        <button class="btn btn-warning btn-actions" data-action="rejected">Download Rejected</button>
        <button class="btn btn-danger btn-actions" data-action="delete">Delete Data</button>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <input type="password" class="form-control" placeholder="password" id="password">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="deleteData">Yes</button>
            </div>
        </div>
    </div>
</div>
<script src="assets/dropzone.min.js"></script>
<script>

    var currentRequest = null;
    var data = [];
    var markData = [];
    var currentPage = 1;
    var currentElem = '';
    var curWindow = false;
    Dropzone.autoDiscover = false;

    currentView = 'inbox';

    var myDropzone = new Dropzone("form", {
        autoProcessQueue: true,
        addRemoveLinks: true,
        clickable: true,
        uploadMultiple: false,
        url: 'upload.php',
        maxFilesize: 50000000,
        chunkSize: 50000000,
        maxThumbnailFilesize: 50,
        // acceptedFiles: 'image/*, video/*',
        parallelUploads: 3,
        init: function () {

            this.on('sending', function (file, xhr, formData) {

            });

            this.on("complete", function (file) {
                // data = JSON.parse(file.xhr.response);
                // $('.overlay').removeClass('d-none');
                // generateTable(1, 40, true);
                //
                // setTimeout(function(){
                //     $('.overlay').addClass('d-none');
                // }, 40000)
            })

            this.on('error', function () {
                alert('Please upload ' + currentAsset + ' only.');
            });

        }
    });

    var loading = true;

    function generateTable($data, $init = false) {
        $html = '';
        $containerHtml = '';
        $.each($data.data, function (i, d) {
            $html += '<tr data-index="' + i + '">';
            $html += '<td class="td-mark d-none" data-id="'+d.id+'" data-index="' + i + '"></td>';
            $html += '<td class="td-handle" data-id="'+d.id+'" data-no-follower="'+d.noFollowers+'" data-handle="' + d.igUsername + '" id="' + d.igUsername + '" data-userid="' + d.userid + '">' + d.igUsername + '</td>';
            $html += '<td class="" data-handle="' + d.noFollowers + '">' + d.noFollowers + '</td>';
            $html += '</tr>';


            if (init == true) {
                if (i == 0) {
                    $dnone = '';
                } else {
                    $dnone = 'd-none';
                }
            } else {
                $dnone = 'd-none';
            }
            $containerHtml += '<div id="' + d.userid + '" class="igContainer ' + $dnone + '"><div class="profile-avatar"></div><div class="timeline-container row"></div></div>';
        });

        $('#ig-table-body').append($html);

        if ($init == true) {
            selectText($data.data[0]['igUsername'], $data.data[0]['id']);
        }

        currentPage = $data.page + 1;
    }

    var timeLines = [];

    async function generateIgViewPreLoad($handle) {
        return new Promise(async function (resolve, reject) {
            $rsp = await
                getProfileData($handle);
            $json = JSON.parse($rsp);
            $user = $json.graphql.user;
            $img = await
                getImage([$user.profile_pic_url]);
            $img = JSON.parse($img);


            $timelines = $user.edge_owner_to_timeline_media.edges.slice(0, 9);
            timeLines[$user.id] = $timelines;

            resolve($img[0]);
        });


    }

    async function generateIgViewPreLoadTimeline($userid) {

        $timelines = timeLines[$userid];

        $thumbsArr = [];
        $.each($timelines, async function (i, p) {
            $thumbs = await getImage([p.node.thumbnail_src]);
            $thumbs = JSON.parse($thumbs);
            $.each($thumbs, function (i, val) {
                $('#' + $userid + ' .timeline-container').append('<div class="col-md-4"><img src="' + val + '" class="img-thumbnail"></div>');
            });
        });


    }

    async function generateIgView($id) {
        $('#profile-container .profile-avatar').html('Loading...');
        $('#profile-container .timeline-container').html('Loading...');
        $rsp = await getProfileData($id);

        $json = JSON.parse($rsp);
        $html = '';
        $html += '<div class="avatar"><img src="' + $json.avatar + '"></div>';

        $('#profile-container-main .profile-avatar').html($html);

        $timelines = $json.timeline;

        $thumbsArr = [];

        $('#profile-container-main .timeline-container').html('');
        for($i = 0; $i < $timelines.length; $i++){
            $('#profile-container-main .timeline-container').append('<div class="col-md-4"><img src="' + $timelines[$i].blob_data + '" class="img-thumbnail"></div>');
        }
    }


    function getProfileData($id) {
        return new Promise(function (resolve, reject) {
            var settings = {
                "url": "fetch-user.php?userid=" + $id,
                "method": "GET",
                "timeout": 0,
                beforeSend: function () {
                    // if (currentRequest != null) {
                    //     currentRequest.abort();
                    // }
                }
            };

            $.ajax(settings).done(function (response) {
                resolve(response);
            });
        });
    }



    async function getImage($imageUrl) {
        return new Promise(function (resolve, reject) {
            var settings = {
                "url": "fetch-image.php",
                "method": "POST",
                "timeout": 0,
                "data": {
                    "imageUrl": $imageUrl
                },
                beforeSend: function () {
                    // if (currentRequest != null) {
                    //     currentRequest.abort();
                    // }
                },
                success: function (data) {
                    resolve(data);
                }
            };

            $.ajax(settings);

        });
    }

    async function getData($page = 1, $status) {
        return new Promise(function (resolve, reject) {
            var settings = {
                "url": "get-data.php?page=" + $page+"&status="+$status,
                "method": "GET",
                "timeout": 0,
                beforeSend: function () {
                    // if (currentRequest != null) {
                    //     currentRequest.abort();
                    // }
                }
            };

            $.ajax(settings).done(function (response) {
                resolve(response);
            });
        });
    }

    function paginate(array, page_size, page_number) {
        // human-readable page numbers usually start with 1, so we reduce 1 in the first argument
        return array.slice((page_number - 1) * page_size, page_number * page_size);
    }

    function selectText(element, $id) {
        var doc = document
            , text = doc.getElementById(element)
            , range, selection
        ;
        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }

        currentElem = text;

        var handle = text.dataset.handle;
        var noFollowers = text.dataset.noFollower;

        $('.noFollowers').html(kFormatter(noFollowers));
        $('.handle-container').html('<a href="https://www.instagram.com/'+handle+'">@'+handle+'</a>');
        generateIgView($id);
        return text;
    }


    function moveTarget(action) {
        var row = currentElem.parentNode;
        if (action == 'down') {
            var nextElem = row.nextSibling.querySelectorAll(".td-handle");
            if (nextElem) {
                var nextId = nextElem[0].id
                var nextUserid = nextElem[0].dataset.userid;
                $id = nextElem[0].dataset.id;
            }
        } else {
            var prevElem = row.previousSibling.querySelectorAll(".td-handle");
            if (prevElem) {
                var nextId = prevElem[0].id
                var nextUserid = prevElem[0].dataset.userid;
                $id = prevElem[0].dataset.id;
            }
        }


        selectText(nextId, $id);

        $('.igContainer').addClass('d-none');
        $('#' + nextUserid).removeClass('d-none');
        // generateIgView(nextId);
    }

   async function markTarget(mark) {
        var row = currentElem.parentNode;
        var elem = row.querySelectorAll(".td-mark");

        // elem[0].innerHTML = mark;
        //
        // data[row.dataset.index].mark = mark;
        //
        // markData.push({
        //     userid: data[row.dataset.index].userid,
        //     mark: mark
        // })

       $id = elem[0].dataset.id;

       await markTargetData($id, mark);




       $approveCount = parseInt($('.approved-no').html());
       $inboxCount = parseInt($('.inbox-no').html());
       $rejectedCount = parseInt($('.no-no').html());
       if(currentView == 'inbox'){
           row.remove();
           $newIndex = $('#ig-table-body tr');
           $td = $newIndex[0].querySelectorAll(".td-handle");
           $newHandle = $td[0].dataset.handle;
           $newId = $td[0].dataset.id;
           selectText($newHandle, $newId);
           if(mark == 'Approved'){
                $('.approved-no').html($approveCount+1);
                $('.inbox-no').html($inboxCount-1)
           }else if(mark == 'Rejected'){
               $('.no-no').html($rejectedCount+1);
               $('.inbox-no').html($inboxCount-1)
           }
       }else if(currentView == 'approved'){
            if(mark == 'Rejected'){
                row.remove();
                $newIndex = $('#ig-table-body tr');
                $td = $newIndex[0].querySelectorAll(".td-handle");
                $newHandle = $td[0].dataset.handle;
                $newId = $td[0].dataset.id;
                selectText($newHandle, $newId);


                $('.no-no').html($rejectedCount+1);
                $('.approved-no').html($approveCount-1)
            }
       }else if(currentView == 'rejected'){
           if(mark == 'Approved'){
               row.remove();
               $newIndex = $('#ig-table-body tr');
               $td = $newIndex[0].querySelectorAll(".td-handle");
               $newHandle = $td[0].dataset.handle;
               $newId = $td[0].dataset.id;
               selectText($newHandle, $newId);

               $('.no-no').html($rejectedCount-1);
               $('.approved-no').html($approveCount+1)
           }
       }

    }


    async function markTargetData($id, $mark) {
        return new Promise(function (resolve, reject) {
            var settings = {
                "url": "mark.php?id=" + $id+"&mark="+$mark,
                "method": "GET",
                "timeout": 0,
                beforeSend: function () {
                    // if (currentRequest != null) {
                    //     currentRequest.abort();
                    // }
                }
            };

            $.ajax(settings).done(function (response) {
                resolve(response);
            });
        });
    }


    document.onkeydown = checkKey;

    function checkKey(e) {

        e = e || window.event;

        if (e.keyCode == '38') {
            // up arrow
            moveTarget('up');
        }
        else if (e.keyCode == '40') {
            // down arrow
            moveTarget('down');
        }
        else if (e.keyCode == '37') {
            markTarget('Approved');
        }
        else if (e.keyCode == '39') {
            // right arrow
            markTarget('Rejected');
        }

    }


    async function deleteData($password) {
        return new Promise(function (resolve, reject) {
            var settings = {
                "url": "delete.php?pass="+ $password,
                "method": "GET",
                "timeout": 0,
                beforeSend: function () {
                    // if (currentRequest != null) {
                    //     currentRequest.abort();
                    // }
                }
            };

            $.ajax(settings).done(function (response) {
                resolve(response);
            });
        });
    }

    $(document).ready(async function () {
        $('.btn-actions').click(function () {
            console.log(data);
            $action = $(this).attr('data-action');
            if ($action != 'delete') {
                window.open('export.php?export=1&action=' + $action);
            } else {
               $('#confirmModal').modal('show');
            }

        });

        $('#deleteData').click(async function(){
            $pass = $('#password').val();
            $rsp = await deleteData($pass);

            if($rsp == 1){
                location.reload();
            }else{
                alert('Incorrect password.');
            }
        });


        init();


        $('.list-group-item').click(async function(){
           $type = $(this).attr('data-type');
           currentView = $type;
           $('.list-group-item').removeClass('active');
           $(this).addClass('active');
            $('#ig-table-body').html('');
            $rsp = await getData(1, $type);
            generateTable(JSON.parse($rsp), true);
        });

    });
    $(window).on('scroll', async function () {
        if ($(window).scrollTop() >= $('body').offset().top + $('body').outerHeight() - window.innerHeight) {
            $rsp = await getData(currentPage, 'inbox');
            generateTable(JSON.parse($rsp));
        }
    });

    async function init(){
        $rsp = await getData(1, 'inbox');
        generateTable(JSON.parse($rsp), true);
        $('.left-nav').css('height', 'inherit');
    }

    function kFormatter(num) {
        return Math.abs(num) > 999 ? Math.sign(num)*((Math.abs(num)/1000).toFixed(1)) + 'k' : Math.sign(num)*Math.abs(num)
    }
</script>
</body>
</html>


