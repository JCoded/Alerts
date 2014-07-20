<div data-alert class="alert-box {{ $alert_class }} {{ $div_classes }}">
	{{ $message }}
	<a href="#" class="close"
		@if(isset($id))
		onclick="rememberAlertClose({{ $id }})"
		@endif
	  >&times;</a>
</div>