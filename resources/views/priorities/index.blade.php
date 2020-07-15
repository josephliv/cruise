@extends('layouts.app', ['activePage' => 'priority-management', 'activeButton' => 'laravel', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Priorities'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Priorities') }}</h3>
                                    <p class="text-sm mb-0">
                                        {{ __('Set up the priorities here') }}
                                    </p>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ route('priorities.create') }}" class="btn btn-sm btn-default">{{ __('Add Priority') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="toolbar">
                            <!--        Here you can write extra buttons/actions for the toolbar              -->
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Condition') }}</th>
                                    <th>{{ __('Send To Specific Agent') }}</th>
                                    <th>{{ __('Send To Veteran') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </thead>
                                <!-- <tfoot>
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Condition') }}</th>
                                        <th>{{ __('Send To Agent') }}</th>
                                        <th>{{ __('Send To Veteran') }}</th>
                                        <th>{{ __('Priority') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                 </tfoot>-->
                                <tbody>
                                
                                    @foreach ($priorities as $priority)
                                        <tr>
                                            <td>{{ $priority->description }}</td>
                                            <td>{{ $priority->condition }}</td>
                                            <td>{{ trim($priority->send_to_email) != '' ? $priority->send_to_email : 'No'  }}</td>
                                            <td>{{ ($priority->send_to_veteran ? 'Yes': 'No') }}</td>
                                            <td>{{ $priority->priority }}</td>
                                            <td class="d-flex justify-content-end">
                                                    <a href="{{ route('priorities.edit', $priority->id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>

                                                    <form class="d-inline-block" action="{{ route('priorities.destroy', $priority->id) }}" method="POST">
                                                        @method('delete')
                                                        @csrf
                                                        <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this priority conditional?') }}') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                    </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatables').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                }

            });


            var table = $('#datatables').DataTable();

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });
        });
    </script>
@endpush