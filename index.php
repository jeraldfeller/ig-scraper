<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Influencer Sorting App</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link rel="stylesheet" href="assets/dropzone.min.css">
    <script src="assets/jquery-3.4.1.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>
    <style>
        .profile-avatar {
            text-align: center;
            margin-bottom: 12px;
        }


        .profile-avatar .avatar img{
            border-radius: 50%;
            border: 2px solid red;
        }

        .form{
            margin-bottom: 24px;
        }

        .footer{
            position: fixed;
            bottom: 12px;
            right: 12px;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="row form">
                <div class="col-md-2">
                    <div class="card">
                        <form action="/target" class="dropzone"></form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <table class="table" id="ig-table">
                        <thead>
                        <tr>
                            <th>Data</th>
                            <th>IG Handle</th>
                            <th>Followers</th>
                        </tr>
                        </thead>
                        <tbody id="ig-table-body">

                        </tbody>
                    </table>
                </div>
                <div class="col-md-5" id="profile-container">
                    <div class="profile-avatar"></div>
                    <div class="timeline-container row"></div>
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
<script src="assets/dropzone.min.js"></script>
<script>

    var currentRequest = null;
    var data = [];
    var markData = [];
    var currentPage = 1;
    var currentElem = '';
    var curWindow = false;
    Dropzone.autoDiscover = false;

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

            this.on("complete", function(file) {
                    data = JSON.parse(file.xhr.response);
                    generateTable(1, 20, true);
            })

            this.on('error', function(){
                alert('Please upload ' + currentAsset + ' only.');
            });

        }
    });

    function generateTable(page, offset = 10, init = false){
        $data = paginate(data, offset, page)

        $html = '';
        $.each($data, function(i, d){
            $html += '<tr data-index="'+i+'">';
            $html += '<td class="td-mark" data-index="'+i+'"></td>';
            $html += '<td class="td-handle" data-handle="'+d.igUsername+'" id="'+d.igUsername+'">'+d.igUsername+'</td>';
            $html += '<td class="" data-handle="'+d.noFollowers+'">'+d.noFollowers+'</td>';
            $html += '</tr>';
        });

        $('#ig-table-body').append($html);

        if(init == true){
            selectText($data[0]['igUsername']);
            generateIgView($data[0]['igUsername']);
        }

        currentPage = page + 1;
    }

    async function generateIgView($handle){
        $('#profile-container .profile-avatar').html('Loading...');
        $('#profile-container .timeline-container').html('Loading...');
        $rsp = await getProfileData($handle);

        $json = JSON.parse($rsp);
        $html = '';

        $user = $json.graphql.user;

        $img = await getImage([$user.profile_pic_url]);
        $img = JSON.parse($img);
        $html += '<div class="avatar"><img src="'+$img[0]+'"></div>';

        $('#profile-container .profile-avatar').html($html);

        $timelines = $user.edge_owner_to_timeline_media.edges.slice(0, 9);

        $thumbsArr = [];
        $.each($timelines, async function(i, p){
            $thumbsArr.push(p.node.thumbnail_src);
        });


        $thumbs = await getImage($thumbsArr);
        $('#profile-container .timeline-container').html('');
        $thumbs = JSON.parse($thumbs);
        $.each($thumbs, function(i, val){
            $('#profile-container .timeline-container').append('<div class="col-md-4"><img src="'+val+'" class="img-thumbnail"></div>');
        });



    }


    function getProfileData($handle){
        return new Promise(function (resolve, reject) {
            var settings = {
                "url": "fetch.php?user="+$handle,
                "method": "GET",
                "timeout": 0,
                beforeSend: function () {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                }
            };

            currentRequest =  $.ajax(settings).done(function (response) {
                resolve(response);
            });
        });
    }

    async function getImage($imageUrl){
        return new Promise(function (resolve, reject) {
            var settings = {
                "url": "fetch-image",
                "method": "POST",
                "timeout": 0,
                "data": {
                    "imageUrl": $imageUrl
                },
                beforeSend: function () {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function (data) {
                    resolve(data);
                }
            };

            currentRequest = $.ajax(settings);

        });
    }

    function paginate(array, page_size, page_number) {
        // human-readable page numbers usually start with 1, so we reduce 1 in the first argument
        return array.slice((page_number - 1) * page_size, page_number * page_size);
    }

    function selectText(element) {
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

        return text;
    }


    function moveTarget(action){
        var row = currentElem.parentNode;
        if(action == 'down'){
            var nextElem = row.nextSibling.querySelectorAll(".td-handle");
            if(nextElem){
                var nextId = nextElem[0].id
            }
        }else{
            var prevElem = row.previousSibling.querySelectorAll(".td-handle");
            if(prevElem){
                var nextId = prevElem[0].id
            }
        }

        selectText(nextId);
        generateIgView(nextId);
    }

    function markTarget(mark){
        var row = currentElem.parentNode;
        var elem = row.querySelectorAll(".td-mark");
        elem[0].innerHTML = mark;

        data[row.dataset.index].mark = mark;

        markData.push({
            userid: data[row.dataset.index].userid,
            mark: mark
        })
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

    $(document).ready(function () {
        $('.btn-actions').click(function(){
            console.log(data);
           $action = $(this).attr('data-action');
           if($action != 'delete'){
               var settings = {
                   "url": "export.php",
                   "method": "POST",
                   "timeout": 0,
                   "data": {
                       "data": markData,
                       "action": $action,
                       "execute": 'save'
                   },
                   success: function (data) {
                       window.open('export.php?export=1&action='+$action);
                   }
               };

               $.ajax(settings);
           }else{
               var txt;
               var r = confirm("Are you sure do you want to delete data?");
               if (r == true) {
                   $('#ig-table-body').empty();
                   myDropzone.removeAllFiles(true);
               } else {

               }
           }

        });
    });
    $(window).on('scroll', function() {
        if ($(window).scrollTop() >= $('body').offset().top + $('body').outerHeight() - window.innerHeight) {
            generateTable(currentPage, offset = 20);
        }
    });
</script>
</body>
</html>


