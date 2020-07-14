@extends('layouts.app', ['activePage' => 'table', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Table List', 'activeButton' => 'laravel'])

@section('content')
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<style type="text/css">

/*table{
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

table thead th {
 text-align: left;
} 


table tbody{
    height: 200px;
    overflow-y: scroll;
}

 table thead{
     width:100%;
     display: table;
 }*/

</style>
</head>
    <div class="content">
        <div class="container-fluid">
            <div class="col-12 mt-2">
                @include('alerts.success')
                @include('alerts.errors')
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card striped-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Leads</h3>
                            <p class="card-category ">Here you can view or delete the leads.</p>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Sender </th>
                                    <th>Subject Line </th>
                                    <th>Time/date</th>
                                    <th>Options</th>
                                </thead>
                                <tbody>
                                @foreach ($leadMails as $leadMail)
                                    <tr>
                                        <td><span id="mail-from">{{$leadMail->id}}</span></td>
                                        <td><span id="mail-from">{{$leadMail->email_from}}</span></td>
                                        <td style="width: 50px;"><span id="mail-subject">{{$leadMail->subject}}</span></td>
                                        
                                        <td><span id="mail-date">{{$leadMail->received_date}}</span> </td>
                                        <td class="d-flex justify-content-end">
                                                    <a href="{{route('leads.download', $leadMail->id)}}" target="_blank" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-paperclip"></i></a>
                                                    <a data-toggle="modal" data-id="{{$leadMail->id}}" data-target="#leadsModal" class="btn btn-link btn-warning getbody d-inline-block"><i class="fa fa-file"></i></a>

                                                    <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this Lead?') }}') ? window.location.href='{{ route('leads.destroy', $leadMail->id) }}' : ''"s><i class="fa fa-times"></i></a>
                                            </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $leadMails->links() }}
                        </div>
                    </div>
                </div>
        </div>
    </div>


    <div class="modal fade " id="leadsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="leadsModalBody" style="border:solid darkgray 1px!important; padding:25px; min-height:400px">
        ...
        </div>
    </div>
    </div>

    <script>
    </script>
@endsection