@extends('frontend.layout.frontend')

@push('body-tag')
	class="privacy-page"
@endpush

@section('main-content')
	<div class="policy-wrap">
	    <h1 class="text-center">{{ $title }}</h1>

	  <!--***** Container ********-->
	  <article class="container">
	      <div class="policy-box">{!! $page->getDescription() !!}</div> 
	  </article>
	</div>
@endsection