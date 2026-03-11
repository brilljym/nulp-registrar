<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('queue-updates', function () {
    return true; // Allow anyone to listen to queue updates
});

Broadcast::channel('registrar-notifications', function () {
    return true; // Allow anyone to listen to registrar notifications
});

Broadcast::channel('queue-display-updates', function () {
    return true; // Allow anyone to listen to queue display updates
});

Broadcast::channel('real-time-updates', function () {
    return true; // Allow anyone to listen to real-time updates
});

Broadcast::channel('new-onsite-requests', function () {
    return true; // Allow anyone to listen to new onsite requests
});

Broadcast::channel('onsite-request-updates', function () {
    return true; // Allow anyone to listen to onsite request updates
});

Broadcast::channel('new-student-requests', function () {
    return true; // Allow anyone to listen to new student requests
});