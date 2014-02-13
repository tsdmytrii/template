steal(
    '//resources/plugins/ckeditor/ckeditor.js',
    '//resources/plugins/jlinq/jlinq.js',
    '//resources/plugins/uploadify/jquery.uploadify.v2.1.4.js',
    '//resources/plugins/uploadify/swfobject.js'
)
    .then(

        './css/mini_block.css',
        './controllers/mini_blocks_controller',
        './models/mini_blocks_model',
        './controllers/mini_block_controller',
        './models/mini_block_model'

    );