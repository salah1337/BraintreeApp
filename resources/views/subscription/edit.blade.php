@extends('../layouts.app')

@section('content')

<form method="POST" action="/subscription/update/{{ $id }}" class="container">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="patch" />
    <div class="form-group">
    <h4>You currently have the <span id="planId">{{ $planId }}</span></h4>
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

@section('js')
    <script>
        var planId = document.querySelector('#planId').innerHTML;
        document.querySelectorAll('option').forEach(opt => {
            if ( opt.value === planId ){
                opt.disabled = true
            }
        });
    </script>
@endsection