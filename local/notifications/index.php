<?php

require_once('../../config.php');

$courseid = required_param('id', PARAM_INT);

$context = context_course::instance($courseid);

require_login($courseid);
require_capability('local/notifications:view', $context);

$PAGE->set_url(new moodle_url('/local/notifications/index.php', ['id' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('coursenotifications', 'local_notifications', format_string($DB->get_field('course', 'fullname', ['id' => $courseid]))));
$PAGE->set_heading(get_string('coursenotifications', 'local_notifications', format_string($DB->get_field('course', 'fullname', ['id' => $courseid]))));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('notifications', 'local_notifications'));

if (has_capability('local/notifications:manage', $context)) {
    echo $OUTPUT->single_button(
        new moodle_url('/local/notifications/edit.php', ['courseid' => $courseid]),
        get_string('addnewnotification', 'local_notifications'),
        'get',
        ['class' => 'btn-primary', 'style' => 'margin-bottom: 20px']
    );
    echo '<div class="m-t-2"></div>';
}

$notifications = $DB->get_records('local_notifications', ['courseid' => $courseid], 'senddate ASC');

if (empty($notifications)) {
    echo $OUTPUT->notification(get_string('nonotifications', 'local_notifications'), 'info');
} else {
    $table = new html_table();
    $table->attributes = ['class' => 'generaltable'];

    $table->head = [
        get_string('subject', 'local_notifications'),
        get_string('senddate', 'local_notifications'),
        get_string('status', 'local_notifications'),
        get_string('actions', 'local_notifications')
    ];

    foreach ($notifications as $n) {
        $actions = [];

        if (has_capability('local/notifications:manage', $context)) {
            $editurl = new moodle_url('/local/notifications/edit.php', ['id' => $n->id, 'courseid' => $n->courseid]);
            $actions[] = html_writer::link($editurl, get_string('edit', 'local_notifications'), ['title' => get_string('editnotification', 'local_notifications', get_string('editing', 'local_notifications'))]);
        }

        if (has_capability('local/notifications:manage', $context)) {
            $deleteurl = new moodle_url('/local/notifications/delete.php', ['id' => $n->id, 'courseid' => $n->courseid, 'sesskey' => sesskey()]);
            $actions[] = html_writer::link(
                $deleteurl,
                get_string('delete', 'local_notifications'),
                [
                    'title' => get_string('deletenotification', 'local_notifications'),
                    'data-confirm' => get_string('confirmdelete', 'local_notifications'),
                    'data-confirm-text' => get_string('confirmdelete', 'local_notifications'),
                    'data-confirm-title' => get_string('deletenotification', 'local_notifications'),
                    'class' => 'confirm-link'
                ]
            );
        }

        $status_text = '';
        if ($n->sent) {
            $status_text = get_string('sentsuccess', 'local_notifications', userdate($n->sent));
        } else {
            $status_text = get_string('notsentstatus', 'local_notifications');
        }

        $row = [
            format_string($n->title),
            get_string('scheduledfor', 'local_notifications', userdate($n->senddate)),
            $status_text,
            implode(' | ', $actions)
        ];
        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();