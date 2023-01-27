<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>jQuery Menu Editor - Demo Bootstrap 4</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"/>
    <link rel="stylesheet" href="{{asset('/backend/css/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css')}}">
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-expand-lg fixed-top navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="https://www.linkedin.com/in/david-ticona-saravia/" target="_blank">بازگشت به منوها</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container" style="margin-top: 100px;">
    <div class="row">
        {{--{{array_flat($arrays)}}--}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header"><h5 class="float-left">Menu</h5>
                    <div class="float-right">
                        <button id="btnReload" type="button" class="btn btn-outline-secondary">
                            <i class="fa fa-play"></i> Load Data</button>
                    </div>
                </div>
                <div class="card-body">
                    <ul id="myEditor" class="sortableLists list-group">
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header">JSON Output
                    <div class="float-right">
                        <button id="btnOutput" type="button" class="btn btn-success"><i class="fas fa-check-square"></i> Output</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group"><textarea id="out" class="form-control" cols="50" rows="10"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">Edit item</div>
                <div class="card-body">
                    <form id="frmEdit" class="form-horizontal">
                        <div class="form-group">
                            <label for="text">Text</label>
                            <div class="input-group">
                                <input type="text" class="form-control item-menu" name="text" id="text" placeholder="Text">
                                <div class="input-group-append">
                                    <button type="button" id="myEditor_icon" class="btn btn-outline-secondary"></button>
                                </div>
                            </div>
                            <input type="hidden" name="icon" class="item-menu">
                        </div>
                        <div class="form-group">
                            <label for="href">URL</label>
                            <input type="text" class="form-control item-menu" id="href" name="href" placeholder="URL">
                        </div>
                        <div class="form-group">
                            <label for="target">Target</label>
                            <select name="target" id="target" class="form-control item-menu">
                                <option value="_self">Self</option>
                                <option value="_blank">Blank</option>
                                <option value="_top">Top</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">Tooltip</label>
                            <input type="text" name="title" class="form-control item-menu" id="title" placeholder="Tooltip">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" id="btnUpdate" class="btn btn-primary" disabled><i class="fas fa-sync-alt"></i> Update</button>
                    <button type="button" id="btnAdd" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
                </div>
            </div>
        </div>
        <form method="post" action="{{route('menu.update',['id'=>$menu->id])}}" id="menu-submit" >
            {!! csrf_field() !!}
            {{method_field('patch')}}
            <input type="hidden" name="content" value="" id="menu-content">
            <input type="hidden" name="id" value="{{$menu->id}}" >
            <button type="submit" class="btn btn-success">ذخیره منو</button>
        </form>
    </div>
</div>
<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="{{asset('/backend/js/menu/jquery-menu-editor.js')}}"></script>
<script type="text/javascript" src="{{asset('/backend/js/menu/bootstrap-iconpicker/js/iconset/fontawesome5-3-1.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/backend/js/menu/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js')}}"></script>
<script>
    jQuery(document).ready(function () {
        /* =============== DEMO =============== */
        // menu items
        var arrayjson = [{"href":"http://home.com","icon":"fas fa-home","text":"Home", "target": "_top", "title": "My Home"},{"icon":"fas fa-chart-bar","text":"Opcion2"},{"icon":"fas fa-bell","text":"Opcion3"},{"icon":"fas fa-crop","text":"Opcion4"},{"icon":"fas fa-flask","text":"Opcion5"},{"icon":"fas fa-map-marker","text":"Opcion6"},{"icon":"fas fa-search","text":"Opcion7","children":[{"icon":"fas fa-plug","text":"Opcion7-1","children":[{"icon":"fas fa-filter","text":"Opcion7-1-1"}]}]}];
        // icon picker options
        var iconPickerOptions = {searchText: "Buscar...", labelHeader: "{0}/{1}"};
        // sortable list options
        var sortableListOptions = {
            placeholderCss: {'background-color': "#cccccc"}
        };

        var editor = new MenuEditor('myEditor', {listOptions: sortableListOptions, iconPicker: iconPickerOptions});
        editor.setForm($('#frmEdit'));
        editor.setUpdateButton($('#btnUpdate'));
        editor.setData({!! $menu->content !!});
        $('#btnReload').on('click', function () {
            editor.setData(arrayjson);
        });

        $('#btnOutput').on('click', function () {
            var str = editor.getString();
            $("#out").text(str);
        });

        $("#btnUpdate").click(function(){
            editor.update();
        });

        $('#btnAdd').click(function(){
            editor.add();
        });
        /* ====================================== */


        $( "#menu-submit" ).submit(function( event ) {
            var str = editor.getString();
            if(str != "[]"){
                $("#menu-content").val(str);
            }
        });


        /** PAGE ELEMENTS **/
        $('[data-toggle="tooltip"]').tooltip();
        $.getJSON( "https://api.github.com/repos/davicotico/jQuery-Menu-Editor", function( data ) {
            $('#btnStars').html(data.stargazers_count);
            $('#btnForks').html(data.forks_count);
        });
    });
</script>
</body>
</html>
