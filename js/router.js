App.Router.map(function(){
	this.route('home', { path: '/' });
	this.route('auth', { path: '/auth' });
});

App.HomeRoute = Ember.Route.extend({
	redirect: function(){
		if (!this.authenticated) {
			// this.transitionTo('auth');
		}
	}
});

App.AppAuthRoute = Ember.Route.extend({
	model: function(){

	},
	renderTemplate: function(){
		this.render('auth');
	}
});
