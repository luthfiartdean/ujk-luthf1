@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-md-12">
    <h3>Services</h3>
    <div id="services-list"></div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    fetchServices().then(renderServicesList);
  });
</script>
@endsection