$.Model('Navigation_model', {
	get_componen_types: function(success){
            $.ajax({
                url: base_url+'admin/component_types/get_component_types_for_nav',
                success: this.callback(success)
            });
	}
}, {});