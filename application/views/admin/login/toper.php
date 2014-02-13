<script type="text/javascript">
    steal(
        'resources/plugins/bootstrap/css/bootstrap.css'
    ).then(
        'components/admin/core/css/global.css'
    );
</script>

<style>
    body {
        padding-top: 40px;
        padding-bottom: 40px;
    }
    .container.well {
        max-width: 450px;
        box-shadow: 0 2px 40px 2px rgba(0, 0, 0, 0.8);
        -moz-box-shadow: 0 2px 40px 2px rgba(0, 0, 0, 0.8);
        -webkit-box-shadow: 0 2px 40px 2px rgba(0, 0, 0, 0.8);
    }
    .form-signin {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin-heading {
        text-align: center;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
        margin-bottom: 10px;
    }
    .form-signin .checkbox {
        font-weight: normal;
    }
    .form-signin .form-control {
        position: relative;
        font-size: 16px;
        height: auto;
        padding: 10px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }
    .form-signin input[type="text"] {
        margin-bottom: -1px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
</head>