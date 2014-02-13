$.Model('Integrators_model', {

    set_integrator: function(data, success){
        $.ajax({
            url: base_url+'admin/integrators/set_integrator',
            data: data,
            success: this.callback(success)
        });
    },

    get_integrators: function(data, success){
        $.ajax({
            url: base_url+'admin/integrators/get_integrators',
            data: data,
            success: this.callback(success)
        });
    },

    delete_integrator: function(data, success){
        $.ajax({
            url: base_url+'admin/integrators/delete_integrator',
            data: data,
            success: this.callback(success)
        });
    },

    set_integrator_placeholder: function(data, success){
        $.ajax({
            url: base_url+'admin/integrators/set_integrator_placeholder',
            data: data,
            success: this.callback(success)
        });
    },

    get_all_integrator_placeholder: function(data, success){
        $.ajax({
            url: base_url+'admin/integrators/get_all_integrator_placeholder',
            data: data,
            success: this.callback(success)
        });
    },

    delete_integrator_placeholder: function(data, success){
        $.ajax({
            url: base_url+'admin/integrators/delete_integrator_placeholder',
            data: data,
            success: this.callback(success)
        });
    }

}, {});