@switch($status ?? '')
@case('success')
<span class="badge bg-success text-white">Success</span>
@break
@case('pending')
<span class="badge bg-warning text-white">Pending</span>
@break
@case('fail')
<span class="badge bg-danger text-white">Failed</span>
@break
@default
N/A
@endswitch