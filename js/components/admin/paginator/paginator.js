if(steal.options.env=='development'){
    steal(function(){
        if (steal.isRhino !== undefined) {
            steal({
                src:'components/user/core/production',
                packaged: false
            });
        }
    })
    .then(
        './css/paginator.css',
        './controllers/paginator_controller',
        './views/paginator.tmpl',

        {src:'jquery', packaged:false},
        {src:'jquery/view', packaged:false},
        {src:'jquery/view/tmpl', packaged:false}
        );


} else {
    steal(
        'components/user/paginator/production/production.css',
        'components/user/paginator/production/production.js'
    );
}