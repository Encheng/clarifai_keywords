<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Clarifai Analysis</title>

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- jQuery (Bootstrap 的 JavaScript 插件需要引入 jQuery) -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<script>
    $(document).ready(function(){
        $("#selectType").change(function(){

            if($(this).val() == 'excel'){
                for(var i=1;i<=3;i++){
                    $("#mongodb"+i).hide();
                    $("#mongodb"+i).children("input").val("");//console.log($("#mongodb"+i).children("input").attr("value",""));
                }
            }
            if($(this).val() == 'mongodb' || $(this).val() == 'ExcelMongodb'){
                $("#mongodb1").show();
                $("#mongodb2").show();
                $("#mongodb3").show();
            }
        });

        $('input[name ^= mongodb]').on('keyup', function() {
            var test = $(this).val();
            $(this).attr('value',test);
            //console.log($(this));
        });
    })


</script>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <span class="navbar-brand mb-0 h1">Peter Tools</span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Dropdown
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Disabled</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>
<div class="container">
    <h1>Clarifai 圖檔關鍵字 解析</h1>
    <form action="keyword.php" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        <div class="form-group">
            <label for="filePath">圖檔路徑</label>
            <input type="text" class="form-control" id="filePath" name="filePath" aria-describedby="emailHelp" placeholder="filePath - 預設路徑 E:/pic_claifai_test/ (jpg檔案格式)">
        </div>
        <div class="form-group" id="mongodb1" style="display: none;">
            <label for="filePath">MongoDB Host</label>
            <input type="text" class="form-control" id="mongodbHost" name="mongodbHost" aria-describedby="emailHelp" placeholder="Host - 預設 127.0.0.1" value="">
        </div>
        <div class="form-group" id="mongodb2" style="display: none;">
            <label for="filePath">MongoDB DBname</label>
            <input type="text" class="form-control" id="mongodbDBname" name="mongodbDBname" aria-describedby="emailHelp" placeholder="DBname - 預設 test" value="">
        </div>
        <div class="form-group" id="mongodb3" style="display: none;">
            <label for="filePath">MongoDB collection</label>
            <input type="text" class="form-control" id="mongodbCollection" name="mongodbCollection" aria-describedby="emailHelp" placeholder="Collection - 預設 clarifai_keywords , 記得先建立資料表" value="">
        </div>
        <label for="exampleFormControlSelect1">輸出方式</label>
        <div class="dropdown">
            <select class="form-control" id="selectType" name="selectType">
                <option value="excel">Excel輸出</option>
                <option value="mongodb">MongoDB寫入</option>
                <option value="ExcelMongodb">Excel+MongoDB</option>
            </select>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="開始解析">
        </div>
    </form>
</div>
</body>
<style>
    .container{
        margin-top: 100px;
    }
    .dropdown{
        margin-bottom: 10px;
    }


</style>
</html>