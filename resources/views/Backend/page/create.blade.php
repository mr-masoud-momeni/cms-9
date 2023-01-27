<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrapesJS Preset Webpage</title>

    <link href="{{asset('/backend/css/Grapes/grapes.css')}}" rel="stylesheet">
    <script src="{{asset('/backend/js/Grapes/grapes.js')}}"></script>

</head>
<style>
    body,
    html {
        height: 100%;
        margin: 0;
    }
</style>
</head>
<body>
<div id="gjs" class="test" style="height:0px; overflow:hidden" data-id="{{$page->id}}">

    {!! csrf_field() !!}
    {!! $page->html !!}
    <style>
        {{$page->css}}
    </style>
</div>
<div id="blocks"></div>
<script type="text/javascript">

    var Url = window.location.href;
    const LandingPage = {
        html: '{{$page->html}}',
        css: '{!! $page->css !!}',
        components: '{{$page->components}}',
        style: '{!! $page->styles !!}',
    };
    var editor = grapesjs.init({
        fromElement: false,
        allowScripts: 1,
        components: LandingPage.components || LandingPage.html,
        style: LandingPage.style || LandingPage.css,
        height: '100%',
        showOffsets: 1,
        noticeOnUnload: 0,
        storageManager: { autoload: 0 },
        container: '#gjs',
        fromElement: true,
        plugins: ['gjs-preset-webpage' , 'grapesjs-custom-code' , 'grapesjs-script-editor'],
        pluginsOpts: {
            'gjs-preset-webpage': {},
            'grapesjs-custom-code': {},
            'grapesjs-script-editor': { /* options */ }
        },
        storageManager: {
            type: 'remote',
            stepsBeforeSave: 1,
            urlStore: 'http://localhost:8000/admin/page',
            urlLoad: '' ,
            params: { _some_token: '' },
            headers: { Authorization: 'Basic ...' },
        }
    });
    editor.StorageManager.get('remote').set({ urlLoad: Url });
    var id = "{{$page->id}}";
    editor.StorageManager.get('remote').set({ params: { _some_token: id } });
    editor.BlockManager.add('h1-block', {
        label: 'Heading',
        content: '<h1>Put your title here</h1>',
        category: 'Basic',
        attributes: {
            title: 'Insert h1 block'
        }
    });
    editor.BlockManager.add('gjs-pn-pa',{
        label: 'test',
        category: 'Basic',
        content: `<section>
          <h1>This is a simple title</h1>
          <div>This is just a Lorem text: Lorem ipsum dolor sit amet</div>
        </section>`,
    });
    editor.BlockManager.add('gjs-pn-paww',{
        label: 'testwwwwwww',
        category: 'Basic',
        content: `<section>
          <h1>This is a simple title</h1>
          <div>This is just a Lorem text: Lorem ipsum dolor sit amet</div>
        </section>`,
    });
    editor.BlockManager.remove('gjs-pn-paww');

</script>

</body>
</html>
