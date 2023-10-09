@extends('admin.layouts.app')

@section('content')
<div class="container">

    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label">Merge Location</h3>
            </div>
        </div>

            <form id="formMergeCollege" method="post" action="{{ route('admin.locations.mergelocations') }}">
            @csrf
            @include('admin.pages.locations.partials.mergeform')
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">{{ $custom_title }}</button>
                <a href="{{ route('admin.colleges.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

       </div>
    </div>
@endsection
@push('extra-js-scripts')
<script>
    $(document).ready(function(){
      
      var location_ids = '{{$selectedLocationIds}}';
      if(location_ids != ''){
        $('select').selectpicker('val', location_ids.split(",")); //split them and set value
        $('select').selectpicker('refresh') //refresh ..selectpicker
      }else{
        $('select').selectpicker('val', ''); //split them and set value
        $('select').selectpicker('refresh')
      }
    });
</script>
@endpush