steal(
    //load jmvc components
    'jquery/controller',			// a widget factory
    'jquery/controller/subscribe',		// subscribe to OpenAjax.hub
    'jquery/view/tmpl',			// client side templates
    'jquery/controller/view',		// lookup views with the controller's name
    'jquery/model',			// Ajax wrappers
    'jquery/dom/fixture',			// simulated Ajax requests
    'jquery/dom/form_params'
)
.then(
    './css/global.css',
    '//resources/plugins/bootstrap/css/bootstrap.css',
    '//resources/plugins/bootstrap/css/bootstrap-theme.css',
    'resources/plugins/jquery.notify/jNotify.jquery.css'
)
.then(
    '//resources/plugins/bootstrap/js/modal.js',
    '//resources/plugins/bootstrap/js/transition.js',
    '//resources/plugins/jquery.notify/jNotify.jquery.js',
    '//resources/plugins/jquery.cookie.js',
    '//resources/plugins/jlinq/jlinq.js',
    '//resources/plugins/jquery.validation/jquery.validate.js'
)
.then(
    '//resources/helpers/admin_functions.js',
    './navigation/navigation.js'
)
.then(function(){
    $.ajaxSetup({
        type:'post',
        dataType:'json',
        cache: false,
        error: function(xhr){
            if (xhr.status == 500) {
                show_error(lang.error_code[xhr.status]);
            }
            else if (xhr.status == 404) {
                show_error(lang.error_code[xhr.status]);
            }
            else if(xhr.status == 401){
                document.location.href = base_url+'admin/login';
            }
            else {
                show_error(xhr.status);
            }
        }

    });
});

