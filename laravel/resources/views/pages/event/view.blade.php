@extends('app')

@section('content')
    <section class="event" data-id="{{ $event->id }}">
        <div class="pull-right relative" style="z-index: 1">
            @can('create-event')
                <a href="/event/{{ $event->id }}/clone" class="btn btn-primary">Clone Event</a>
            @endcan

            @can('edit-event')
                <a href="/event/{{ $event->id }}/edit" class="btn btn-primary">Edit Event</a>
            @endcan

            @can('delete-event')
                <a href="/event/{{ $event->id }}/delete" class="btn btn-danger">Delete Event</a>
            @endcan
        </div>

        <h1 class="relative">
            Viewing Event: {{ $event->name }}

        </h1>
        <hr>

        @if($event->image)
            <img class="pull-right" src="/files/event/{{ $event->image }}">
        @endif

        <div>
            <label>Start Date</label>
            {{ $event->start_date }}
        </div>

        <div>
            <label>End Date</label>
            {{ $event->end_date }}
        </div>

        @if($event->description)
            <label>Description</label>
            <p>{!! nl2br(e($event->description)) !!}</p>
        @endif

        @if($event->featured)
<?php 
//set up ticketing status info
$lottoComplete = DB::table('nectrtix.global')
                     ->select(DB::raw("CASE WHEN ticketing_live = b'1' THEN 1 ELSE 0 END as lottery_complete"))
                     ->first()->lottery_complete;

$ticketStatusQuery = DB::table('nectrtix.users')
                         ->crossJoin('nectrtix.global')
                         ->select(DB::raw("fullname,dob,status,ticket_order as tid,(select count(*) from nectrtix.users where status = 'waitlist' and ticket_order < tid) + 1 as waitlist_count,CASE WHEN date_info<=signup_cutoff THEN 1 ELSE 0 END as is_in_lottery,CASE WHEN is_admin = b'1' OR is_board = b'1' THEN 1 ELSE 0 END as show_admin"))
                         ->where('email',Auth::user()->email);

$has_account = $ticketStatusQuery->exists();

if($has_account) {
  $ticketStatus = $ticketStatusQuery->first();

  $longstatus = "";
  switch ($ticketStatus->status) {
  case 'waitlist':
    if ($lottoComplete) {
      $longstatus = "You're currently on the waitlist. Your waitlist position is $ticketStatus->waitlist_count. If you <b>DO NOT</b> want to go, <a href='/cgi-bin/ticketing.pl?action=I%20don%27t%20want%20a%20ticket&confirm=checked'>click here to <b>release</b> your spot</a>";
    } elseif ($ticketStatus->is_in_lottery) {
      $longstatus = "You're ready to be randomized!";
    } else {
      $longstatus = "You missed the lottery cutoff, but you'll be added to the waitlist soon!";
    }
    break;
  case 'noinfo':
    $longstatus = "You need to update your registration! <a href='/cgi-bin/ticketing.pl'>Go here to fill in your info!</a>";
    break;
  case 'unpaid':
    $longstatus = "You have a ticket offer! <a href='/cgi-bin/ticketing.pl?target=sales'>Click here to purchase!</a><p>If you <b>DO NOT</b> want your ticket, <a href='/cgi-bin/ticketing.pl?action=I%20don%27t%20want%20a%20ticket&confirm=checked'> click here to <b>release</b> your ticket offer to the waitlist.</a>";
    break;
  case 'paid':
    $longstatus = "You have paid for your ticket! See you in the woods! <a href='/cgi-bin/ticketing.pl?target=sales'>Click here to go back to the ticket sale page</a> (for refunds etc)";
    break;
  case 'nothanks':
    $longstatus = "You have declined your ticket/waitlist registration. <a href='/cgi-bin/ticketing.pl?action=Put%20me%20back%20on%20the%20waitlist'>Click here to go back onto the waitlist</a> or <a href='mailto:nectr-tickets@tryfix.org'>Send us an email for help if you did this by mistake</a>";
    break;
  case 'refunded':
    $longstatus = "You have refunded your ticket. <a href='/cgi-bin/ticketing.pl?action=Put%20me%20back%20on%20the%20waitlist'>Click here to go back onto the waitlist</a> or <a href='mailto:nectr-tickets@tryfix.org'>Send us an email for help if you did this by mistake</a>";
    break;
  case 'lapsed':
    $longstatus = "You failed to pay for your ticket before the deadline in your ticket offer. <a href='/cgi-bin/ticketing.pl?action=Put%20me%20back%20on%20the%20waitlist'>Click here to go back onto the waitlist</a>";
    break;
  case 'minor':
    $longstatus = "You are registered as a minor. Your parent or guardian will need to sign a waiver for you at the gate.";
    break;
  default:
    $longstatus = $ticketStatus->status;
  }
/*      switch ($status) {
  case 'waitlist':
    $longstatus = "You are on the waitlist. Unfortunately the event has already begun and no more tickets will be released. We hope you are able to join us next year!";
    break;
  case 'noinfo':
    $longstatus = "You have not completed your registration with this email. Unfortunately the event has already begun and no more tickets will be released. We hope you are able to join us next year!";
    break;
  case 'unpaid':
    $longstatus = "You are on the waitlist. Unfortunately the event has already begun and no more tickets will be released. We hope you are able to join us next year!";
    break;
  case 'paid':
    $longstatus = "You have paid for your ticket! See you in the woods!";
    break;
  case 'nothanks':
    $longstatus = "You have declined your ticket/waitlist registration.";
    break;
  case 'refunded':
    $longstatus = "You have refunded your ticket.";
    break;
  case 'lapsed':
    $longstatus = "You failed to pay for your ticket before the deadline in your ticket offer. Unfortunately the event has already begun and no more tickets will be released. We hope you are able to join us next year!";
    break;
  default:
    $longstatus = $status;
  }*/
  $groupQuery = DB::table('nectrtix.groups')
    ->leftJoin('nectrtix.users','groups.id','users.groupid')
    ->select(DB::raw('groups.name, CASE WHEN groups.owneruserid=users.id THEN 1 ELSE 0 END as is_groupowner, groupstatus'))
    ->where('email',Auth::user()->email);

  if ($has_account) {
    if ($groupQuery->exists()) {
      $groupRsp = $groupQuery->first();
      if ($groupRsp->is_groupowner == 1) {
        $groupStatus = 'You are the owner of the ' . $groupRsp->name . ' group.';
      } else {
        $groupStatus = 'You are ' . $groupRsp->groupstatus . ' in the ' . $groupRsp->name . ' group.';
      }
    } else {
      $groupStatus = 'You are not in a group.';
    }
  } else {
    $groupStatus = 'Account not yet registered for sales';
  }
}
?>

<?php if (!$has_account) { ?>
      <h3>You have not yet registered for a ticket! <a href="/cgi-bin/ticketing.pl">Click here to register!</a></h3>
<?php } else { ?>
      <div id="reginfo">
        <h3>Ticket Registration Information (Must match your ID at gate!):</h3>
        <h4><a href="http://www.nectrct.org/?page_id=54">Go here to read more about ticketing and find answers</a></h4>
        <p>Name: <b><?php echo e($ticketStatus->fullname);?></b></p>
        <p>Date of Birth: <b><?php echo e($ticketStatus->dob);?></b></p>
        <p>Status: <b><?php echo $longstatus;?></b></p>
<?php if (!$lottoComplete && $ticketStatus->is_in_lottery) { ?>
        <p>Families and groups who must go together may group themselves to be randomized together<br>
      Group: <b><?php echo $groupStatus;?></b> <a href="/cgi-bin/ticketing.pl?action=Manage%20Groups">Click here to manage groups</a></p>
<?php } ?>
<?php if ($ticketStatus->show_admin) { ?>
        <p><a href="/cgi-bin/ticketing.pl?action=Secret%20Admin%20Interface">Click here for the ticketing admin panel</a></p>
<?php } ?>
      </div>
<?php } ?>
        @endif

        @can('read-department')
            <a href="/event/{{ $event->id }}/departments" class="btn btn-primary">View All Departments</a>
        @endcan

        @can('read-shift')
            <a href="/event/{{ $event->id }}/shifts" class="btn btn-primary">View All Shifts</a>
        @endcan

        @can('create-schedule')
            <a href="/event/{{ $event->id }}/schedule/create" class="btn btn-primary">Add a Shift to the Schedule</a>
        @endcan

        <div class="clearfix"></div>
        <hr>

        @if($event->departments->count() && (Gate::check('read-shift') || $lottoComplete))
            <h2>Available Shifts</h2>

            <form class="form-inline event-filter">
                Filter:

                <div class="form-group">
                    <select class="form-control filter-days">
                        <option value="all">Show All Days</option>

                        @foreach($event->days(true) as $day)
                            <option value="{{ $day->date->format('Y-m-d') }}">{{ $day->name }} - {{ $day->date->format('Y-m-d') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <select class="form-control filter-departments">
                        <option value="all">Show All Departments</option>

                        @foreach($event->departments->sortBy('name') as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <hr>

            <div class="days">
                @foreach($event->days(true) as $day)
                    <div class="day" data-date="{{ $day->date->format('Y-m-d') }}">
                        <div class="heading">
                            <h3>{{ $day->name }}</h3>
                            &mdash; <i>{{ $day->date->format('Y-m-d') }}</i>
                        </div>

                        <div class="shift-wrap">
                            @include('partials/event/timegrid')

                            <div class="department-wrap">
                                @foreach($event->departments->sortBy('name') as $department)
                                    <?php

                                    if($department->slots->where('start_date', $day->date->format('Y-m-d'))->isEmpty())
                                        continue;

                                    ?>

                                    <div class="department" data-id="{{ $department->id }}">
                                        <div class="title">
                                            <b>{{ $department->name }}</b>

                                            @if($department->description)
                                                <span class="description">
                                                    <span class="glyphicon glyphicon-question-sign"></span>

                                                    <div class="tip hidden">
                                                        {!! nl2br(e($department->description)) !!}

                                                        <hr>
                                                        <a class="btn btn-primary">Close</a>
                                                    </div>
                                                </span>
                                            @endif

                                            @can('edit-department')
                                                <span class="edit">
                                                    <a href="/department/{{ $department->id }}/edit">
                                                        <span class="glyphicon glyphicon-pencil"></span>
                                                    </a>
                                                </span>
                                            @endcan
                                        </div>

                                        <ul class="shifts">
                                            @foreach($department->schedule as $schedule)
                                                <?php

                                                if($schedule->slots->where('start_date', $day->date->format('Y-m-d'))->isEmpty())
                                                    continue;

                                                ?>

                                                <li class="shift row" data-rows="{{ $schedule->volunteers }}">
                                                    <div class="title col-sm-2">
                                                        <b>{{ $schedule->shift->name }}</b>

                                                        @if($schedule->shift->description)
                                                            <span class="description">
                                                                <span class="glyphicon glyphicon-question-sign"></span>

                                                                <div class="tip hidden">
                                                                    {!! nl2br(e($schedule->shift->description)) !!}

                                                                    <hr>
                                                                    <a class="btn btn-primary">Close</a>
                                                                </div>
                                                            </span>
                                                        @endif

                                                        @can('edit-schedule')
                                                            <span class="edit">
                                                                <a href="/schedule/{{ $schedule->id }}/edit">
                                                                    <span class="glyphicon glyphicon-pencil"></span>
                                                                </a>
                                                            </span>
                                                        @endcan
                                                    </div>

                                                    <div class="slots col-sm-10">
                                                        @foreach($schedule->slots->where('start_date', $day->date->format('Y-m-d')) as $slot)
                                                            @include('partials/event/slot')
                                                        @endforeach
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div> <!-- / .department-wrap -->
                        </div> <!-- / .shift-wrap -->
                    </div>
                @endforeach
            </div>

            <hr>

            @can('create-department')
                <a href="/event/{{ $event->id }}/department/create" class="btn btn-primary">Create Department</a>
            @endcan

            @can('create-shift')
                <a href="/event/{{ $event->id }}/shift/create" class="btn btn-primary">Create Shift</a>
            @endcan
        @endif
    </section>
@endsection
