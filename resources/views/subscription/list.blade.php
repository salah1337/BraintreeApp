@extends('../layouts.app')

@section('content')

<div class="container">
    <div class="row">
    <p style="display: none" id="planId">{{ $planId }}</p>
      <div id="monthly_plan" class="plans col-sm">
        <h3>
          1 Month 
        </h3>
        <p>
          Lorem ipsum dolor, sit amet consectetur adipisicing elit. Inventore, quam?
        </p>
        <button class="btn btn-primary"><a class="text text-white" href="/subscription/switch/{{ $id }}/monthly_plan">Move To</a></button>
      </div>
      <div id="bi_yearly_plan" class="plans col-sm">
        <h3>
          6 Months
        </h3>
        <p>
          Save 5$, sit amet consectetur adipisicing elit. Inventore, quam?
        </p>
        <button class="btn btn-primary"><a class="text text-white" href="/subscription/switch/{{ $id }}/bi_yearly_plan">Move To</a></button>
      </div>
      <div id="yearly_plan" class="plans col-sm">
        <h3>
          12 Months
        </h3>
        <p>
          Save 10$, sit amet consectetur adipisicing elit. Inventore, quam?
        </p>
        <button class="btn btn-primary"><a class="text text-white" href="/subscription/switch/{{ $id }}/yearly_plan">Move To</a></button>
      </div>
      <div id="eee" class="plans col-sm">
        <h3>
          e
        </h3>
        <p>
          eeeeeeeeeeeeeeeeeeeeeeeeeeeeee
        </p>
        <button class="btn btn-primary"><a class="text text-white" href="/subscription/switch/{{ $id }}/eee">Move To</a></button>
      </div>
    </div>
  </div>

@endsection

@section('js')
    <script>
        var planId = document.getElementById('planId').innerHTML;
        document.querySelectorAll('.plans').forEach(plan => {
            if ( plan.getAttribute('id') === planId ){
                plan.children[2].innerHTML = 'Active'
                plan.children[2].disabled = true
            };
        });
    </script>
@endsection