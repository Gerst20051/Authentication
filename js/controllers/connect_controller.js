App.ConnectController = Ember.Controller.extend({
	authenticated: false,
	failedAttempts: 0,

	authenticate: function(){
		if (this.credentialsValid()) {
			this.set('authenticated', true);
		} else {
			this.incrementProperty('failedAttempts');
		}
	},

	credentialsValid: function(){
		return false;
	},

	authenticatedDidChange: function(){
		if (this.get('authenticated')) {
			this.get('target').send('becomeAuthenticated');
		}
	}.observes('authenticated')
});
