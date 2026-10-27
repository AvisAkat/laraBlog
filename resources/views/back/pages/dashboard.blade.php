@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page Title Here')
@section('content')

	<div class="xs-pd-20-10 pd-ltr-20">
		@livewire('admin.dashboard')
	</div>
@endsection  