<?php

require_once('../../config.php');
require_login();

$courseid = required_param('id', PARAM_INT);
$context = context_course::instance($courseid);

require_capability('local/notifications:view', $context);

$PAGE->set_url(new moodle_url('/local/notifications/index.php', ['id' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('notifications', 'local_notifications'));
$PAGE->set_heading(get_string('notifications', 'local_notifications'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

$notifications = $DB->get_records('local_notifications', ['courseid' => $courseid]);

echo $OUTPUT->heading(get_string('notifications', 'local_notifications'));

echo html_writer::link(
    new moodle_url('/local/notifications/edit.php', ['courseid' => $courseid]),
    get_string('addnotification', 'local_notifications'),
    ['class' => 'btn btn-primary']
);

$table = new html_table();
$table->head = ['Title', 'Date', 'Sent', 'Actions'];

foreach ($notifications as $n) {
    $editurl = new moodle_url('/local/notifications/edit.php', ['id' => $n->id]);
    $deleteurl = new moodle_url('/local/notifications/delete.php', ['id' => $n->id]);
    $row = [
        $n->title,
        userdate($n->senddate),
        $n->sent ? 'Yes' : 'No',
        html_writer::link($editurl, 'Edit') . ' | ' . html_writer::link($deleteurl, 'Delete')
    ];
    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
