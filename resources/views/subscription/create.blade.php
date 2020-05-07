@extends('../layouts.app')

@section('content')

<form method="POST" action="/subscription/create" class="container">
    {{ csrf_field() }}
    <div class="form-group">
      <label for="plansSelect">Plan</label>
      <select name="planId" id="plansSelect">
        <option value="monthly_plan">1 month</option>
        <option value="bi_yearly_plan">6 months</option>
        <option value="yearly_plan">12 months</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Sub</button>
</form>

@endsection
