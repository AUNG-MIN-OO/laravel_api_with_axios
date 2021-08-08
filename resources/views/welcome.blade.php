<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Api Axios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body class="mt-0">
<div aria-live="polite" aria-atomic="true" style="position: relative;z-index: 2000">
    <div class="toast text-white" style="position: absolute; top: 0; right: 20px;background-color: dodgerblue">
        <div class="toast-header" style="background-color: white;color: dodgerblue">
            <div class="d-flex justify-content-between align-items-center">
                <strong class="mr-4">New Notifications</strong>
                <small>Just now!</small>
            </div>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <span id="successMsg"></span>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="updateBox" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Post</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="editForm">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="title" placeholder="Title" class="form-control">
                        <span id="titleError" class="text-danger font-weight-bold"></span>
                    </div>
                    <div class="form-group">
                        <textarea rows="10" name="description" placeholder="Description" class="form-control"></textarea>
                        <span id="descError" class="text-danger font-weight-bold"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-8">
                <h1>Posts</h1>
                <table class="table table-stripped table-dark">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="tableBody">

                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <h1>Add Post</h1>
                <form name="myForm">
                    <div class="form-group">
                        <input type="text" name="title" placeholder="Title" class="form-control">
                        <span id="titleError" class="text-danger font-weight-bold"></span>
                    </div>
                    <div class="form-group">
                        <textarea rows="10" name="desc" placeholder="Description" class="form-control"></textarea>
                        <span id="descError" class="text-danger font-weight-bold"></span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Now</button>
                </form>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
{{--    axios link connect--}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    //Read
    var tableBody = document.getElementById("tableBody");
    axios.get('api/posts')
    .then(response => {
        response.data.forEach(function (item){
            tableBody.innerHTML += '<tr>'+
                                        '<td>'+item.id+'</td>'+
                                        '<td>'+item.title+'</td>'+
                                        '<td>'+item.description+'</td>'+
                                        '<td>'+
                                            '<button class="btn btn-warning mr-2 btn-sm" data-toggle="modal" data-target="#updateBox" onclick="editBtn('+item.id+')">Edit'+'</button>'+
                                            '<button class="btn btn-danger btn-sm"  onclick="deleteBtn('+item.id+')">Delete'+'</button>'+
                                        '</td>'+
                                    '<tr>';
        });
    })
    .catch(error => {
        if (error.response.status === 404){
            console.log(error.response.config.url+' is unavailable')
        }
    })

    // create
    var myForm = document.forms['myForm'];
    var title = myForm['title'];
    var description = myForm['desc'];

    myForm.onsubmit = function (e){
        e.preventDefault();
        axios.post('/api/posts',{
            title : title.value,
            description : description.value,
        })
        .then(response => {
            if (response.data.msg == "Post is successfully created"){
                // console.log(response);
                $(document).ready(function(){
                    $(".toast").toast({ delay: 3000 });
                    $(".toast").toast('show');
                });
                document.getElementById('successMsg').innerHTML = response.data.msg;
                tableBody.innerHTML += '<tr>'+
                    '<td>'+response.data[0].id+'</td>'+
                    '<td>'+response.data[0].title+'</td>'+
                    '<td>'+response.data[0].description+'</td>'+
                    '<td>'+
                    '<button class="btn btn-warning mr-2 btn-sm" data-toggle="modal" data-target="#updateBox" onclick="editBtn('+response.data[0].id+')">Edit'+'</button>'+
                    '<button class="btn btn-danger btn-sm"  onclick="deleteBtn('+response.data[0].id+')">Delete'+'</button>'+
                    '</td>'+
                    '<tr>';
            }else{
                if (response.data.msg.title == "The title field is required."){
                    document.getElementById('titleError').innerHTML = response.data.msg.title;
                }else{
                    document.getElementById('titleError').innerHTML = "";
                }
                if (response.data.msg.description == "The description field is required."){
                    document.getElementById('descError').innerHTML = response.data.msg.description;
                }else{
                    document.getElementById('descError').innerHTML = "";
                }
            }
        })
        .catch(error => {
            console.log(error.response)
        })
    }

    // Edit
    var editForm = document.forms['editForm'];
    var editTitle = editForm['title'];
    var editDesc = editForm['description'];
    var updatePostId;

    function editBtn(postId){
        updatePostId = postId;
        axios.get('/api/posts/'+postId)
        .then(response => {
            // console.log(response,response.data['title'],response.data.description);
            editTitle.value = response.data.title;
            editDesc.value = response.data.description;
        })
        .catch(error => console.log(error));
    }

    //Update
    var updateBox = document.getElementById('updateBox');
    editForm.onsubmit = function (e){
        e.preventDefault();
        axios.put('/api/posts/'+updatePostId,{
            title : editTitle.value,
            description : editDesc.value
        })
        .then(response => {
            console.log(response);
            $(document).ready(function(){
                $(".toast").toast({ delay: 3000 });
                $(".toast").toast('show');
            });
            document.getElementById('successMsg').innerHTML = response.data.message;
            $('#updateBox').modal('hide');
        })
        .catch(err => console.log(err));
    }

    //Delete
    function deleteBtn(postId){
        axios.delete('api/posts/'+postId)
        .then(response => {
            // console.log(response);
            $(document).ready(function(){
                $(".toast").toast({ delay: 3000 });
                $(".toast").toast('show');
            });
            document.getElementById('successMsg').innerHTML = response.data.message;

            tableBody.innerHTML += '<tr>'+
                '<td>'+response.data[0].id+'</td>'+
                '<td>'+response.data[0].title+'</td>'+
                '<td>'+response.data[0].description+'</td>'+
                '<td>'+
                '<button class="btn btn-warning mr-2 btn-sm" data-toggle="modal" data-target="#updateBox" onclick="editBtn('+response.data[0].id+')">Edit'+'</button>'+
                '<button class="btn btn-danger btn-sm"  onclick="deleteBtn('+response.data[0].id+')">Delete'+'</button>'+
                '</td>'+
                '<tr>';
        })
        .catch(err => console.log(err));
    }

</script>
</body>
</html>
