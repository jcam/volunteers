<?php

use App\Helpers;

?>

@extends('app')

@section('content')
    <h1>Additional Profile Information</h1>
    <hr>

    {!! Form::open(['url' => 'profile/edit']) !!}
        <input type="hidden" name="type" value="data">

        @include('partials/form/text',
        [
            'name' => 'full_name',
            'label' => 'Preferred Full Name',
            'placeholder' => 'Your preferred name in the Default World',
            'help' => "Required. Your full name is used for reporting and volunteer listings",
            'value' => (is_null($user->data)) ? '' : $user->data->full_name
        ])

        @include('partials/form/text',
        [
            'name' => 'burner_name',
            'label' => 'Burner Name',
            'placeholder' => 'Your name on the Playa',
            'help' => "This name will be shown to other users when you sign up for a shift. Leave blank if you don't have one",
            'value' => Helpers::displayName($user)
        ])

        @include('partials/form/text',
        [
            'name' => 'camp',
            'label' => 'Your Camp',
            'placeholder' => 'Camp Creative Name',
            'help' => "Enter your camp name if you have one, or 'open camping' if not",
            'value' => (is_null($user->data)) ? '' : $user->data->camp
        ])

        @include('partials/form/text',
        [
            'name' => 'phone',
            'label' => 'Phone Number',
            'value' => (is_null($user->data)) ? '' : $user->data->phone
        ])

        @include('partials/form/text',
        [
            'name' => 'emergency_name',
            'label' => 'Emergency Contact',
            'value' => (is_null($user->data)) ? '' : $user->data->emergency_name
        ])

        @include('partials/form/text',
        [
            'name' => 'emergency_phone',
            'label' => 'Emergency Phone Number',
            'value' => (is_null($user->data)) ? '' : $user->data->emergency_phone
        ])

        @include('partials/form/text',
        [
            'name' => 'birthday',
            'label' => 'Birthday',
            'placeholder' => 'YYYY-MM-DD',
            'help' => 'This will only be used as a part of the event census and at the gate',
            'value' => (is_null($user->data)) ? '' : $user->data->birthday
        ])
<link rel="stylesheet" href="/css/default.css" id="theme_base">
<link rel="stylesheet" href="/css/default.date.css" id="theme_date">
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="/js/picker.js"></script>
<script src="/js/picker.date.js"></script>
<script src="/js/legacy.js"></script>
<script type="text/javascript">
$('#birthday-field').pickadate({
  format: 'yyyy-mm-dd',
  selectYears: 100,
  selectMonths: true,
  min: new Date(1920,0,1),
  max: new Date(2019,9,14)
});
</script>

        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="/profile" class="btn btn-primary">Cancel</a>
    {!! Form::close() !!}
@endsection
