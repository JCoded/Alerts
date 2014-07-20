<script type="text/javascript">
	function rememberAlertClose(id) {
		var data = {'id': id};
		if ( !window.jQuery ) {
			var jq = document.createElement('script'); 
			jq.type = 'text/javascript';
			jq.src = '//code.jquery.com/jquery-1.11.0.min.js';
			document.getElementsByTagName('head')[0].appendChild(jq);
		}
		jQuery.ajax({
			type: "POST",
			url: "{{ action('JCoded\Alerts\AlertController@rememberDismiss') }}",
			data: data
		});
	}
</script>