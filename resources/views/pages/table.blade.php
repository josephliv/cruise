@extends('layouts.app', ['activePage' => 'table', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION', 'navName' => 'Table List', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card strpied-tabled-with-hover">
                        <div class="card-header  text-center">
                            <h3 class="card-title ">Reports</h3>
                            <p class="card-category ">Here you can view the progress of each agent.</p>
                            <div class="p-4">
                                <label for="time-set">Run the report by dates: </label>
                                <input type="date" id="from-date" name="from-date"> to <input type="date" id="to-date" name="to-date">
                                <input type="submit">
                            </div>
                        </div>
                        <div class="card-body table-full-width table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Leads sent</th>
                                    <th>Time/date</th>
                                    <th>Leads Rejected</th>
                                    <th>Details </th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        <td><a href="link to this agents settings" class="btn btn-info">View</td>
                                    </tr> 
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        <td><a href="link to this agents settings" class="btn btn-info">View</td>
                                    </tr> 
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        <td><a href="link to this agents settings" class="btn btn-info">View</td>
                                    </tr> 
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">Agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        <td><a href="link to this agents settings" class="btn btn-info">View</td>
                                    </tr>
                                    <tr>
                                        <td><span id="agent-id">agent id</span></td>
                                        <td><span id="agent-name">Agent name</span></td>
                                        <td><span id="leads-sent">5</span></td>
                                        <td><span id="time-sent">3:00PM - Jul/05/20</span> </td>
                                        <td><span id="leads-rejected">2</span></td>
                                        <td><a href="link to this agents settings" class="btn btn-info">View</td>
                                    </tr>     
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection