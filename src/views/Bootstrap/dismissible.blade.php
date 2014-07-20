<div class="alert alert-{{ $alert_class }} alert-dismissible {{ $div_classes }}" role="alert">
	<button type="button" class="close" data-dismiss="alert"
				@if(isset($id))
				onclick="rememberAlertClose({{ $id }})"
				@endif
			  >
		<span aria-hidden="true">&times;</span>
		<span class="sr-only">Close</span>
	</button>
	{{ $message }}
</div>