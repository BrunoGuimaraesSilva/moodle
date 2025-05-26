<?php

require_once('../../config.php');
require_once('forms.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

if ($id) {
    $notification = $DB->get_record('local_notifications', ['id' => $id], '*', MUST_EXIST);
    $courseid = $notification->courseid;
} else {
    $notification = null;
    if (!$courseid) {
        print_error('missingcourseid', 'local_notifications');
    }
}

$context = context_course::instance($courseid);
require_login($courseid);
require_capability('local/notifications:manage', $context);

$mform = new local_notifications_form(null, ['courseid' => $courseid]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/notifications/index.php', ['id' => $courseid]));
} else if ($data = $mform->get_data()) {
    $record = new stdClass();
    $record->id = $data->id;
    $record->courseid = $data->courseid;
    $record->title = $data->title;
    $record->message = $data->message['text'];
    $record->messageformat = $data->message['format']; 
    $record->senddate = $data->senddate;
    $record->sent = 0;

    if ($record->id) {
        $DB->update_record('local_notifications', $record);
        \core\notification::success(get_string('notificationupdated', 'local_notifications'));
    } else {
        $record->timecreated = time();
        $record->usermodified = $USER->id;
        $DB->insert_record('local_notifications', $record);
        \core\notification::success(get_string('notificationsaved', 'local_notifications'));
    }
    redirect(new moodle_url('/local/notifications/index.php', ['courseid' => $courseid]));
}

$toform = $notification;
if (!$toform) {
    $toform = new stdClass();
    $toform->id = 0;
    $toform->courseid = $courseid;
}

$mform->set_data($toform);

$PAGE->set_url(new moodle_url('/local/notifications/edit.php', ['id' => $id, 'courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('editnotification', 'local_notifications', $id ? get_string('editing', 'local_notifications') : get_string('adding', 'local_notifications')));
$PAGE->set_heading(get_string('editnotification', 'local_notifications', $id ? get_string('editing', 'local_notifications') : get_string('adding', 'local_notifications')));

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();