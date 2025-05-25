<?php

require_once('../../config.php');
require_once($CFG->libdir.'/formslib.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

if ($id) {
    $notification = $DB->get_record('local_notifications', ['id' => $id], '*', MUST_EXIST);
    $courseid = $notification->courseid;
} else {
    $notification = null;
}

$context = context_course::instance($courseid);
require_login($courseid);
require_capability('local/notifications:manage', $context);

class notification_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('text', 'title', 'Title');
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required');

        $mform->addElement('editor', 'message', 'Message');
        $mform->addRule('message', null, 'required');

        $mform->addElement('date_time_selector', 'senddate', 'Send Date');
        $mform->addRule('senddate', null, 'required');

        $this->add_action_buttons();
    }
}

$mform = new notification_form(null, ['id' => $id]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/notifications/index.php', ['id' => $courseid]));
} else if ($data = $mform->get_data()) {
    $record = (object)[
        'id' => $data->id,
        'courseid' => $data->courseid,
        'title' => $data->title,
        'message' => $data->message['text'],
        'senddate' => $data->senddate,
        'sent' => 0,
    ];
    if ($data->id) {
        $DB->update_record('local_notifications', $record);
    } else {
        $DB->insert_record('local_notifications', $record);
    }
    redirect(new moodle_url('/local/notifications/index.php', ['id' => $courseid]));
}

$toform = $notification ?? (object)['courseid' => $courseid, 'id' => 0];
$mform->set_data($toform);

$PAGE->set_url(new moodle_url('/local/notifications/edit.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('editnotification', 'local_notifications'));
$PAGE->set_heading(get_string('editnotification', 'local_notifications'));

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
