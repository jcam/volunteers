<?php

use App\Helpers;

$class = "slot empty";
$href = "/slot/{$slot->id}/view";
$name = "";

// If there is no slot user, display a link to the take shift page
if(is_null($slot->user))
{
    $start = strtotime($slot->start_time);
    $end = strtotime($slot->end_time);

    $name = date("h:i a", $start) . " - " . date("h:i a", $end);
}

// If there is a slot user, set the class to taken
else
{
    $class = "slot taken";
    $name = Helpers::displayName($slot->user);

    // If the slot is taken by the current user, display a link to the release page
    if($slot->user->id === Auth::user()->id)
    {
        $class = "slot taken-by-current-user";
    }
}

if($href)
{
    $href = "href='{$href}'";
}

// If the event has passed, remove any links
$start_date = new \Carbon\Carbon($slot->start_date);

if($start_date->lt(\Carbon\Carbon::now()))
{
    if (!Auth::user()->hasRole('department-lead') && !Auth::user()->hasRole('admin'))
    {
        $href = "";
    }
}

// If the user doesn't have a ticket, don't let them sign up
//@if($event->featured) <----- this doesn't work, but we don't want to check ticketing if not featured
if ((isset($has_account) && !$has_account) || (isset($ticketStatus) && $ticketStatus->status != 'unpaid' && $ticketStatus->status != 'paid'))
{
    $href = "";
}
//  waitlist_count > 30 && $ticketStatus->status == 'waitlist' || $ticketStatus)
//@endif

?>

<span class="slot-wrap" data-start="{{ $slot->start_time }}" data-duration="{{ $schedule->duration }}" data-row="{{ $slot->row }}">
    <a {!! $href !!} class="{{ $class }}" data-id="{{ $slot->id }}" title="{{ $name }}">{{ $name }}</a>
</span>
