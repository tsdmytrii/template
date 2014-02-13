$.Model('UserModel', {

	setUser: function(data, success){
		$.ajax({
			url:base_url+'admin/users/set_user',
			data:data,
		//	fixture:'add_user.json',
			success:this.callback(success)
		});
	},

	getUser: function(id, success){
		$.ajax({
			url:base_url+'admin/users/get_user',
			data:{
				id:id
			},
			success:this.callback(success)
		});
	},

	deleteUser: function(id, success){
		$.ajax({
			url:base_url+'admin/users/delete_user',
			data:{
				id:id
			},
			success:this.callback(success)
		});
	}

}, {});