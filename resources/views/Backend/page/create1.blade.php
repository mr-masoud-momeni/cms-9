<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrapesJS Preset Webpage</title>

    <link href="{{asset('/backend/css/Grapes/grapes.min.css')}}" rel="stylesheet">
    <script src="{{asset('/backend/js/Grapes/grapes.min.js')}}"></script>

</head>
<style>
    .gjs-block {
        width: auto;
        height: auto;
        min-height: auto;
    }
    #gjs {
        border: 3px solid #444;
    }

    /* Reset some default styling */
    .gjs-cv-canvas {
        top: 0;
        width: 100%;
        height: 100%;
    }
</style>
</head>
<body>
<div id="gjs">
    <h1>Hello World Component!</h1>
</div>
<div id="blocks"></div>
</body>
</html>
<script type="text/javascript">

    const editor = grapesjs.init({
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: '#gjs',
        // Get the content for the canvas directly from the element
        // As an alternative we could use: `components: '<h1>Hello World Component!</h1>'`,
        fromElement: true,
        // Size of the editor
        height: '300px',
        width: 'auto',
        // Disable the storage manager for the moment
        storageManager: false,
        // Avoid any default panel
        panels: { defaults: [] },
        blockManager: {
            appendTo: '#blocks',
            blocks: [
                {
                    id: 'section', // id is mandatory
                    label: '<b>Section</b>', // You can use HTML/SVG inside labels
                    attributes: { class:'gjs-block-section' },
                    content: `<section>
          <h1>This is a simple title</h1>
          <div>This is just a Lorem text: Lorem ipsum dolor sit amet</div>
        </section>`,
                }, {
                    id: 'text',
                    label: 'Text',
                    content: '<div data-gjs-type="text">Insert your text here</div>',
                }, {
                    id: 'image',
                    label: 'Image',
                    // Select the component once it's dropped
                    select: true,
                    // You can pass components as a JSON instead of a simple HTML string,
                    // in this case we also use a defined component type `image`
                    content: { type: 'image' },
                    // This triggers `active` event on dropped components and the `image`
                    // reacts by opening the AssetManager
                    activate: true,
                }
            ]
        },
    });
    editor.BlockManager.add('gjs-pn-pa',{
        label: 'test',
        category: 'Basic',
        content: `<section>
          <h1>This is a simple title</h1>
          <div>This is just a Lorem text: Lorem ipsum dolor sit amet</div>
        </section>`,
    });
    const removed = editor.BlockManager.remove('gjs-pn-pa');
</script>
