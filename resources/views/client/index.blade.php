@extends('master')

@section('content')

<div class="row">
 <div class="col-md-12">
  <br />
  <h3 align="center">Liste des clients</h3>
  <br />
  @if($message = Session::get('success'))
  <div class="alert alert-success">
   <p>{{ $message }}</p>
  </div>
  @endif
  <div align="right">
   <a href="{{ route('client.create') }}" class="btn btn-primary">Add</a>
   <br />
   <br />
  </div>
  <table class="table table-bordered table-striped">
   <tr>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Edit</th>
    <th>Delete</th>
   </tr>
   @foreach($clients as $row)
   <tr>
    <td>{{ $row['first_name'] }}</td>
    <td>{{ $row['last_name'] }}</td>
    <td>
      <!-- Fix for route parameter -->
      <a href="{{ route('client.edit', $row['id']) }}" class="btn btn-warning">Edit</a>
    </td>
    <td>
     <form method="post" class="delete_form" action="{{ route('client.destroy', $row['id']) }}">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete</button>
     </form>
    </td>
   </tr>
   @endforeach
  </table>
 </div>
</div>

<script>
$(document).ready(function(){
 $('.delete_form').on('submit', function(){
  return confirm("Voulez-vous vraiment supprimer ce client?");
 });
});
</script>

@endsection
