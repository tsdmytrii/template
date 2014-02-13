steal(
    '//components/admin/paginator/paginator.js',

    '//resources/plugins/bootstrap/js/collapse.js',
    '//resources/plugins/date.time.picker/bootstrap-datetimepicker.js',
    '//resources/plugins/date.time.picker/bootstrap-datetimepicker.min.css',

//    '//resources/plugins/jquery.file.upload/js/vendor/jquery.ui.widget.js',
    '//resources/plugins/ckeditor/ckeditor.js'
)
.then(
    '//resources/plugins/ckeditor/adapters/jquery.js',

//    '//resources/plugins/jquery.file.upload/css/jquery.fileupload-ui.css',
//    '//resources/plugins/jquery.file.upload/css/jquery.fileupload.css',

    '//resources/plugins/jquery.synctranslit.js'
//    ,

//    '//resources/plugins/jquery.file.upload/js/jquery.iframe-transport.js',
//    '//resources/plugins/jquery.file.upload/js/jquery.fileupload.js'
)
.then(

    //load resources
    './css/article.css',
    './controllers/articles_controller',
    './models/articles_model',
    './controllers/article_controller',
    './models/article_model',
    './models/article_seo_model'

);